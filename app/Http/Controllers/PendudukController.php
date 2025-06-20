<?php

namespace App\Http\Controllers;

use App\Models\RT;
use App\Models\RW;
use App\Models\User;
use App\Models\Daerah;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use App\Filters\PendudukFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PendudukController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if ($user->hasRole('admin')) {
            return $this->getIndexForAdmin($request);
        } elseif ($user->hasRole('rw')) {
            return $this->getIndexForRw($user, $request);
        } elseif ($user->hasRole('rt')) {
            return $this->getIndexForRt($user, $request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak memiliki akses.',
        ], 403);
    }

    private function getIndexForAdmin(Request $request)
    {
        $pendudukQuery = Penduduk::query()
            ->when($request->filled('daerah_id'), fn($q) => $q->where('daerah_id', $request->daerah_id))
            ->when($request->filled('rw_id'), fn($q) => $q->where('rw_id', $request->rw_id))
            ->when($request->filled('rt_id'), fn($q) => $q->where('rt_id', $request->rt_id))
            ->filter($request)
            ->orderBy('created_at', 'desc');

        $penduduk = $pendudukQuery->get();

        $formattedPenduduk = $penduduk->map(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama,
                'no_nik' => $item->no_nik,
                'jenis_kelamin' => $item->jenis_kelamin,
                'tempat_lahir' => $item->tempat_lahir,
                'tanggal_lahir' => $item->tanggal_lahir,
                'umur' => $item->umur,
                'agama' => $item->agama,
                'pendidikan' => $item->pendidikan,
                'pekerjaan' => $item->pekerjaan,
                'status' => $item->status,
                'alamat' => $item->alamat,
                'kep_di_kelurahan' => $item->kep_di_kelurahan,
                'tgl_mulai' => $item->tgl_mulai,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),

                // ID relasi (tidak tampilkan nama)
                'daerah_id' => $item->daerah_id,
                'rw_id' => $item->rw_id,
                'rt_id' => $item->rt_id,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data penduduk berhasil diambil',
            'data' => $formattedPenduduk,
        ]);
    }

    private function getIndexForRw($user, Request $request)
    {
        // Ambil data RW dari relasi user
        $rw = $user->rwDetail; // asumsi relasi hasOne(RW::class)

        if (!$rw) {
            return response()->json([
                'success' => false,
                'message' => 'Data RW tidak ditemukan.',
            ], 404);
        }

        // Ambil semua RT yang berada di bawah RW ini
        $rtList = RT::where('rw_id', $rw->id)->get();
        $rtIds = $rtList->pluck('id');

        // Query dasar penduduk
        $pendudukQuery = Penduduk::with(['daerah', 'rw', 'rt'])->filter($request);

        // Cek jika ada filter rt_id dan pastikan valid
        if ($request->filled('rt_id')) {
            if ($rtIds->contains($request->rt_id)) {
                $pendudukQuery->where('rt_id', $request->rt_id);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke RT ini.',
                ], 403);
            }
        } else {
            $pendudukQuery->whereIn('rt_id', $rtIds);
        }

        // Ambil data
        $penduduk = $pendudukQuery->get();

        // Log aktivitas jika perlu (opsional)
        logActivity('mengakses', 'data penduduk sebagai RW');

        return response()->json([
            'success' => true,
            'message' => 'Data penduduk berhasil diambil.',
            'data'    => $penduduk,
        ]);
    }

    private function getIndexForRt($user, Request $request)
    {
        // Pastikan user RT memiliki detail RT
        if (!$user->rtDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Data RT belum terdaftar.',
            ], 403);
        }

        // Ambil data penduduk berdasarkan RT yang dimiliki user
        $penduduk = Penduduk::with(['daerah', 'rw', 'rt'])
            ->where('rt_id', $user->rtDetail->id)
            ->filter($request)
            ->orderBy('created_at', 'desc')
            ->get();

        // Log aktivitas (opsional)
        logActivity('mengakses', 'data penduduk sebagai RT');

        // Kembalikan data sebagai JSON untuk Flutter
        return response()->json([
            'success' => true,
            'message' => 'Data penduduk berhasil diambil.',
            'data'    => $penduduk,
        ]);
    }

    public function getRwByDaerah(Request $request)
    {
        // Validasi input dari Flutter
        $request->validate([
            'daerah_id' => 'required|exists:daerahs,id',
        ]);

        // Ambil data RW berdasarkan daerah_id
        $rwList = RW::where('daerah_id', $request->daerah_id)
            ->get(['id', 'name']);

        // Kembalikan dalam format JSON untuk Flutter
        return response()->json([
            'success' => true,
            'message' => 'Data RW berhasil diambil.',
            'data'    => $rwList,
        ]);
    }

    public function getRtByDaerahRw(Request $request)
    {
        // Validasi input dari Flutter
        $request->validate([
            'rw_id' => 'required|exists:rws,id',
        ]);

        // Ambil data RT berdasarkan rw_id
        $rtList = RT::where('rw_id', $request->rw_id)
            ->get(['id', 'name']);

        // Kembalikan dalam format JSON untuk Flutter
        return response()->json([
            'success' => true,
            'message' => 'Data RT berhasil diambil.',
            'data'    => $rtList,
        ]);
    }

    public function getDaerahFromUsers()
    {
        // Ambil semua user yang memiliki daerah dan relasi sudah dimuat
        $users = User::whereNotNull('daerah_id')
            ->with('daerah')
            ->get();

        // Filter dan ambil daftar unik dari relasi 'daerah'
        $daerahs = $users->map(function ($user) {
            return $user->daerah;
        })->filter() // hilangkan null
        ->unique('id') // ambil unik berdasarkan ID
        ->values()
        ->map(function ($daerah) {
            return [
                'id' => $daerah->id,
                'name' => $daerah->name,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daerah yang memiliki user berhasil diambil.',
            'data' => $daerahs,
        ]);
    }

    // private function getIndexForAdmin(Request $request)
    // {
    //     // Ambil semua data daerah untuk dropdown
    //     $daerahList = Daerah::all();
    //     $rwList = collect();
    //     $rtList = collect();

    //     // Default pagination kosong
    //     $penduduk = new LengthAwarePaginator([], 0, 10);

    //     if ($request->filled('daerah_id')) {
    //         // Ambil daftar RW berdasarkan daerah
    //         $rwList = RW::where('daerah_id', $request->daerah_id)->get();

    //         // Ambil daftar RT jika RW dipilih
    //         if ($request->filled('rw_id')) {
    //             $rtList = RT::where('rw_id', $request->rw_id)->get();
    //         }

    //         // Query penduduk berdasarkan filter
    //         $penduduk = Penduduk::with(['daerah', 'rw', 'rt'])
    //             // ->where('daerah_id', $request->daerah_id)
    //             // ->when($request->filled('rw_id'), fn($q) => $q->where('rw_id', $request->rw_id))
    //             // ->when($request->filled('rt_id'), fn($q) => $q->where('rt_id', $request->rt_id))
    //             ->filter($request) // <== penting
    //             ->paginate(10);
                
    //             // ->where('daerah_id', $request->daerah_id)
    //             // ->when($request->filled('rw_id'), function ($query) use ($request) {
    //             //     $query->where('rw_id', $request->rw_id);
    //             // })
    //             // ->when($request->filled('rt_id'), function ($query) use ($request) {
    //             //     $query->where('rt_id', $request->rt_id);
    //             // })
    //             // ->paginate(10);
    //     }

    //     logActivity('mengakses', 'halaman data penduduk');

    //     return view('pages.penduduk', [
    //         'penduduk'    => $penduduk,
    //         'daerahList'  => $daerahList,
    //         'rwList'      => $rwList,
    //         'rtList'      => $rtList,
    //         'filterAktif' => [
    //             'daerah_id' => $request->daerah_id,
    //             'rw_id'     => $request->rw_id,
    //             'rt_id'     => $request->rt_id,
    //             'jenis_kelamin' => $request->jenis_kelamin,
    //             'usia' => $request->usia,
    //             'status_pernikahan' => $request->status_pernikahan,
    //             'agama' => $request->agama,
    //             'pendidikan' => $request->pendidikan,
    //             'pekerjaan' => $request->pekerjaan,
    //             'search' => $request->search,
    //         ],
    //     ]);
    // }

    // private function getIndexForRw($user, Request $request)
    // {
    //     // Ambil data RW dari tabel rws berdasarkan user RW yang login
    //     $rw = $user->rwDetail; // asumsi: relasi hasOne(RW::class)

    //     // Ambil semua RT yang terdaftar di bawah RW tersebut
    //     $rtList = RT::where('rw_id', $rw->id)->get(); // dari tabel rts
    //     $rtIds = $rtList->pluck('id');

    //     // Ambil rt_id dari request (filter form)
    //     // $selectedRtId = request('rt_id');

    //     // Ambil data penduduk berdasarkan filter
    //     $pendudukQuery = Penduduk::with(['daerah', 'rw', 'rt'])->filter($request);

    //     if ($request->filled('rt_id') && $rtIds->contains($request->rt_id)) {
    //         $pendudukQuery->where('rt_id', $request->rt_id);
    //     }elseif ($request->filled('rt_id')) {
    //         abort(403, 'Anda tidak memiliki akses ke RT ini.');
    //     }else {
    //         $pendudukQuery->whereIn('rt_id', $rtIds);
    //     }

    //     // if ($selectedRtId && $rtIds->contains($selectedRtId)) {
    //     //     // Jika ada filter rt_id dan itu milik RW yang login, filter berdasarkan itu
    //     //     $pendudukQuery->where('rt_id', $selectedRtId);
    //     // } else {
    //     //     // Kalau tidak ada filter, ambil semua penduduk di bawah RT milik RW tersebut
    //     //     $pendudukQuery->whereIn('rt_id', $rtIds);
    //     // }

    //     $penduduk = $pendudukQuery->paginate(10)->appends($request->all());

    //     // Tidak perlu isi daerahList dan rwList karena role RW hanya butuh RT
    //     $daerahList = collect();
    //     $rwList = collect();

    //     logActivity('mengakses', 'halaman data penduduk');

    //     return view('pages.penduduk', compact('penduduk', 'daerahList', 'rwList', 'rtList'));
    // }

    // private function getIndexForRt($user,Request $request)
    // {
    //     if (!$user->rtDetail) {
    //         abort(403, 'RT belum terdaftar.');
    //     }

    //     $penduduk = Penduduk::with(['daerah', 'rw', 'rt'])
    //         // ->where('rt_id', $user->rt_id)
    //         ->where('rt_id', $user->rtDetail->id)
    //         ->filter($request)
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(10)
    //         ->appends(request()->all());

    //     $daerahList = collect();
    //     $rwList = collect();
    //     $rtList = collect();

    //     logActivity('mengakses', 'halaman data penduduk');

    //     return view('pages.penduduk', compact('penduduk', 'daerahList', 'rwList', 'rtList'));
    // }

    // public function getRwByDaerah(Request $request)
    // {
    //      $request->validate(['daerah_id' => 'required|exists:daerahs,id']);

    //     $rwList = RW::where('daerah_id', $request->daerah_id)->get(['id', 'name']);

    //     return response()->json($rwList);
    //     // $rwList = RW::where('daerah_id', $request->daerah_id)->get(['id', 'name']);

    //     // // $daerah = $request->daerah;

    //     // // // Ambil RW unik dari user yang sesuai daerah
    //     // // $rwList = User::where('daerah', $daerah)
    //     // //     ->whereHas('roles', function ($query) {
    //     // //         $query->where('name', 'rw');
    //     // //     })
    //     // //     ->pluck('rw')
    //     // //     ->unique()
    //     // //     ->values()
    //     // //     ->map(function ($rw) {
    //     // //         return ['rw' => str_pad($rw, 2, '0', STR_PAD_LEFT)];
    //     // //     });

    //     // return response()->json($rwList);
    // }

    // public function getRtByDaerahRw(Request $request)
    // {
    //     $rtList = RT::where('rw_id', $request->rw_id)->get(['id', 'name']);

    //     // $daerah = $request->daerah;
    //     // $rw = $request->rw;

    //     // // Ambil RT unik dari user yang sesuai daerah & rw
    //     // $rtList = User::where('daerah', $daerah)
    //     //     ->where('rw', $rw)
    //     //     ->whereHas('roles', function ($query) {
    //     //         $query->where('name', 'rt');
    //     //     })
    //     //     ->pluck('rt')
    //     //     ->unique()
    //     //     ->values()
    //     //     ->map(function ($rt) {
    //     //         return ['rt' => str_pad($rt, 2, '0', STR_PAD_LEFT)];
    //     //     });

    //     return response()->json($rtList);
    // }

    
    // public function store(Request $request)
    // {
    //     $this->authorize('create', Penduduk::class);

    //     $user = Auth::user(); // user yang sedang login

    //     // Logika pengisian lokasi berdasarkan role
    //     if ($user->role === 'RT') {
    //         // Jika RT, semua ID lokasi dari user
    //         $request->merge([
    //             'rt_id' => $user->rt_id,
    //             'rw_id' => $user->rw_id,
    //             'daerah_id' => $user->daerah_id,
    //         ]);
    //     } elseif ($user->role === 'RW') {
    //         // Jika RW, rw_id dan daerah_id dari user, rt_id dari input form
    //         $request->merge([
    //             'rw_id' => $user->rw_id,
    //             'daerah_id' => $user->daerah_id,
    //         ]);
    //         // rt_id tetap dari form
    //     } elseif ($user->role === 'admin') {
    //         // Semua lokasi dari input form
    //         // Tidak perlu merge, karena semua diambil dari $request langsung
    //     }

    //     // Validasi dasar
    //     $rules = [
    //         'no_nik' => 'required|numeric|unique:penduduks,no_nik',
    //         'nama' => 'required|string|max:255',
    //         'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
    //         'tempat_lahir' => 'required|string|max:100',
    //         'tanggal_lahir' => 'required|date|before_or_equal:today',
    //         'status' => 'required|in:Kawin,Belum Kawin,Janda,Duda',
    //         'agama' => 'required|in:Islam,Kristen Protestan,Kristen Katolik,Hindu,Buddha,Konghucu',
    //         'pendidikan' => 'required|string|max:100',
    //         'pendidikan_lainnya' => 'nullable|string|max:255',
    //         'pekerjaan' => 'required|string|max:100',
    //         'kep_di_kelurahan' => 'required|string|max:100',
    //         'kep_di_kelurahan_lainnya' => 'nullable|string|max:255',
    //         'alamat' => 'required|string|max:255',
    //         'tgl_mulai' => 'required|date',
    //     ];

    //     // Validasi lokasi:
    //     // RT butuh semua sudah di-merge (tidak perlu validasi input form)
    //     if ($user->role === 'admin') {
    //         $rules = array_merge($rules, [
    //             'rt_id' => 'required|exists:rts,id',
    //             'rw_id' => 'required|exists:rws,id',
    //             'daerah_id' => 'required|exists:daerahs,id',
    //         ]);
    //     } elseif ($user->role === 'RW') {
    //         $rules = array_merge($rules, [
    //             'rt_id' => 'required|exists:rts,id', // RT dari input dropdown
    //         ]);
    //     }

    //     $request->validate($rules);

    //     // ⬇️ Validasi tambahan agar RW tidak memasukkan RT dari RW lain
    //     if ($user->role === 'RW') {
    //         $rtValid = RT::where('id', $request->rt_id)
    //                     ->where('rw_id', $user->rw_id)
    //                     ->exists();

    //         if (!$rtValid) {
    //             return back()->withErrors(['rt_id' => 'RT yang dipilih tidak sesuai dengan RW Anda.'])->withInput();
    //         }

    //         $validated['rw_id'] = $user->rw_id;
    //         $validated['daerah_id'] = $user->daerah_id;
    //     }

    //     // Validasi tambahan untuk kolom 'Lainnya'
    //     if ($request->pendidikan === 'Lainnya' && empty($request->pendidikan_lainnya)) {
    //         return back()->withErrors(['pendidikan_lainnya' => 'Kolom pendidikan lainnya wajib diisi.'])->withInput();
    //     }

    //     if ($request->kep_di_kelurahan === 'Lainnya' && empty($request->kep_di_kelurahan_lainnya)) {
    //         return back()->withErrors(['kep_di_kelurahan_lainnya' => 'Kolom kependudukan lainnya wajib diisi.'])->withInput();
    //     }

    //     // Proses nilai final untuk pendidikan dan kependudukan
    //     $pendidikan = $request->pendidikan === 'Lainnya' ? $request->pendidikan_lainnya : $request->pendidikan;
    //     $kependudukan = $request->kep_di_kelurahan === 'Lainnya' ? $request->kep_di_kelurahan_lainnya : $request->kep_di_kelurahan;

    //     // Simpan data penduduk
    //     $penduduk = Penduduk::create([
    //         'no_nik' => $request->no_nik,
    //         'nama' => $request->nama,
    //         'jenis_kelamin' => $request->jenis_kelamin,
    //         'tempat_lahir' => $request->tempat_lahir,
    //         'tanggal_lahir' => $request->tanggal_lahir,
    //         'status' => $request->status,
    //         'agama' => $request->agama,
    //         'pendidikan' => $pendidikan,
    //         'pekerjaan' => $request->pekerjaan,
    //         'kep_di_kelurahan' => $kependudukan,
    //         'alamat' => $request->alamat,
    //         'tgl_mulai' => $request->tgl_mulai,
    //         'rt_id' => $request->rt_id,
    //         'rw_id' => $request->rw_id,
    //         'daerah_id' => $request->daerah_id,
    //         'user_id' => $user->id,
    //     ]);

    //     logActivity('menambahkan', 'data penduduk', $penduduk->nama);

    //     return redirect()->route('penduduk.index')->with('success', 'Data penduduk berhasil ditambahkan.');
    // }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'RT') {
            $request->merge([
                'rt_id' => $user->rt_id,
                'rw_id' => $user->rw_id,
                'daerah_id' => $user->daerah_id,
            ]);
        } elseif ($user->role === 'RW') {
            $request->merge([
                'rw_id' => $user->rw_id,
                'daerah_id' => $user->daerah_id,
            ]);
        }

        $rules = [
            'no_nik' => 'required|numeric|unique:penduduks,no_nik',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'status' => 'required|in:Kawin,Belum Kawin,Janda,Duda',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Khonghucu',
            'pendidikan' => 'required|string|max:100',
            'pendidikan_lainnya' => 'nullable|string|max:255',
            'pekerjaan' => 'required|string|max:100',
            'kep_di_kelurahan' => 'required|string|max:100',
            'kep_di_kelurahan_lainnya' => 'nullable|string|max:255',
            'alamat' => 'required|string|max:255',
            'tgl_mulai' => 'required|date|before_or_equal:today',
        ];

        if ($user->role === 'admin') {
            $rules = array_merge($rules, [
                'rt_id' => 'required|exists:rts,id',
                'rw_id' => 'required|exists:rws,id',
                'daerah_id' => 'required|exists:daerahs,id',
            ]);
        } elseif ($user->role === 'RW') {
            $rules = array_merge($rules, [
                'rt_id' => 'required|exists:rts,id',
            ]);
        } elseif ($user->role === 'RT') {
            $rules = array_merge($rules, [
                'rt_id' => 'required|exists:rts,id',
            ]);
        }

        $validated = $request->validate($rules);

        // Validasi tambahan: pastikan RT milik RW
        if ($user->role === 'RW') {
            $rtValid = RT::where('id', $request->rt_id)
                ->where('rw_id', $user->rw_id)
                ->exists();

            if (!$rtValid) {
                return response()->json([
                    'success' => false,
                    'message' => 'RT yang dipilih tidak sesuai dengan RW Anda.',
                ], 422);
            }

            $validated['rw_id'] = $user->rw_id;
            $validated['daerah_id'] = $user->daerah_id;
        }

        // Validasi tambahan jika user memilih "Lainnya"
        if ($request->pendidikan === 'Lainnya' && empty($request->pendidikan_lainnya)) {
            return response()->json([
                'success' => false,
                'message' => 'Kolom pendidikan lainnya wajib diisi.',
                'errors' => ['pendidikan_lainnya' => 'Kolom pendidikan lainnya wajib diisi.'],
            ], 422);
        }

        if ($request->kep_di_kelurahan === 'Lainnya' && empty($request->kep_di_kelurahan_lainnya)) {
            return response()->json([
                'success' => false,
                'message' => 'Kolom kependudukan lainnya wajib diisi.',
                'errors' => ['kep_di_kelurahan_lainnya' => 'Kolom kependudukan lainnya wajib diisi.'],
            ], 422);
        }

        $pendidikan = $request->pendidikan === 'Lainnya'
            ? $request->pendidikan_lainnya
            : $request->pendidikan;

        $kependudukan = $request->kep_di_kelurahan === 'Lainnya'
            ? $request->kep_di_kelurahan_lainnya
            : $request->kep_di_kelurahan;

        $penduduk = Penduduk::create([
            'no_nik' => $request->no_nik,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'status' => $request->status,
            'agama' => $request->agama,
            'pendidikan' => $pendidikan,
            'pekerjaan' => $request->pekerjaan,
            'kep_di_kelurahan' => $kependudukan,
            'alamat' => $request->alamat,
            'tgl_mulai' => $request->tgl_mulai,
            'rt_id' => $request->rt_id,
            'rw_id' => $request->rw_id,
            'daerah_id' => $request->daerah_id,
            'user_id' => $user->id,
        ]);

        logActivity('menambahkan', 'data penduduk', $penduduk->nama);

        return response()->json([
            'success' => true,
            'message' => 'Data penduduk berhasil ditambahkan.',
            'data' => $penduduk,
        ]);
    }

    // public function index(Request $request)
    // {
    //     $this->authorize('viewAny', Penduduk::class);

    //     /** @var \App\Models\User $user */
    //     $user = Auth::user();

    //     if ($user->hasRole('admin')) {

    //         if ($request->filled('daerah_id')) {
    //             $penduduk = Penduduk::with(['rt', 'rw', 'daerah'])
    //                 ->when($request->filled('rw_id'), fn($q) => $q->where('rw_id', $request->rw_id))
    //                 ->when($request->filled('rt_id'), fn($q) => $q->where('rt_id', $request->rt_id))
    //                 ->where('daerah_id', $request->daerah_id)
    //                 ->paginate(10);

    //             $rwList = RW::where('daerah_id', $request->daerah_id)->get();
    //             $rtList = $request->filled('rw_id') ? RT::where('rw_id', $request->rw_id)->get() : collect();

    //         } else {
    //             $penduduk = new LengthAwarePaginator([], 0, 10);
    //             $rwList = collect();
    //             $rtList = collect();
    //         }

    //         $daerahList = Daerah::all();

    //          // Ambil daftar daerah dari user dengan role rw dan rt
    //         $daerahList = User::whereHas('roles', function ($q) {
    //                 $q->whereIn('name', ['rw', 'rt']);
    //             })
    //             ->pluck('daerah')
    //             ->unique()
    //             ->values();

    //     } elseif ($user->hasRole('rw')) {
    //         // RW hanya bisa lihat data dari RT bawahannya
    //         $rtIds = $user->rts->pluck('id');

    //         $penduduk = Penduduk::with(['rt', 'rw', 'daerah'])
    //             ->whereIn('rt_id', $rtIds)
    //             ->paginate(10);

    //         $rtList = $user->rts;

    //     } elseif ($user->hasRole('rt')) {
    //         // RT hanya lihat datanya sendiri
    //         $penduduk = Penduduk::with(['rt', 'rw', 'daerah'])
    //             ->where('rt_id', $user->id)
    //             ->paginate(10);
                
    //     } else {
    //         abort(403, 'Tidak memiliki akses.');
    //     }

    //     logActivity('mengakses halaman data penduduk');

    //     return view('pages.penduduk', [
    //         'penduduk' => $penduduk,
    //         'daerahList' => $daerahList ?? [],
    //         'rwList' => $rwList ?? [],
    //         'rtList' => $rtList ?? [],
    //         'daerah_id' => $request->daerah_id,
    //         'rw_id' => $request->rw_id,
    //         'rt_id' => $request->rt_id,
    //     ]);
    // }
    
    // public function index(Request $request)
    // {
    //     $this->authorize('viewAny', Penduduk::class);

    //     /** @var \App\Models\User $user */
    //     $user = Auth::user();

    //     if ($user->hasRole('admin')) {
    //         return $this->getIndexForAdmin($request);
    //     } elseif ($user->hasRole('rw')) {
    //         return $this->getIndexForRw($user, $request);
    //     } elseif ($user->hasRole('rt')) {
    //         return $this->getIndexForRt($user, $request);
    //     }

    //     abort(403, 'Tidak memiliki akses.');
    // }
    
    // public function edit($id) {
    //     // $penduduk = Penduduk::find($id);
    //     // $penduduk = Penduduk::with(['rt', 'rw'])->find($id);
    //     $penduduk = Penduduk::with(['rt.rw.daerah'])->findOrFail($id);
    //     Gate::authorize('view', $penduduk);

    //     // Tambahan data readonly untuk ditampilkan di form
    //     $penduduk->pendidikan_lainnya = $penduduk->pendidikan;
    //     $penduduk->kep_di_kelurahan_lainnya = $penduduk->kep_di_kelurahan;
    //     $penduduk->daerah = $penduduk->rw?->daerah?->nama ?? '-';
    //     $penduduk->rw_nama = $penduduk->rw?->rw ?? '-';
    //     $penduduk->rt_nama = $penduduk->rt?->rt ?? '-';

    //     // return response()->json(['message' => 'Data tidak ditemukan'], 404);
    //     return response()->json($penduduk);
    // }

    public function edit($id)
    {
        // Ambil data penduduk beserta relasi RT, RW, dan Daerah
        $penduduk = Penduduk::with(['rt.rw.daerah'])->findOrFail($id);

        // Autentikasi menggunakan Gate (pastikan policy-nya sudah dibuat)
        Gate::authorize('view', $penduduk);

        // Siapkan data tambahan untuk ditampilkan di Flutter
        $penduduk->pendidikan_lainnya = $penduduk->pendidikan;
        $penduduk->kep_di_kelurahan_lainnya = $penduduk->kep_di_kelurahan;
        $penduduk->daerah = $penduduk->rw?->daerah?->nama ?? '-';
        $penduduk->rw_nama = $penduduk->rw?->rw ?? '-';
        $penduduk->rt_nama = $penduduk->rt?->rt ?? '-';

        return response()->json([
            'status' => true,
            'message' => 'Data penduduk berhasil diambil',
            'data' => $penduduk
        ]);
    }
    
    // public function update(Request $request, $id)
    // {
    //     /** @var \App\Models\User $user */
    //     $user = Auth::user();

    //     /** Optional: untuk keamanan tambahan **/
    //     if (!$user || !$user->hasAnyRole(['admin', 'rw', 'rt'])) {
    //         abort(403, 'Unauthorized');
    //     }
        
    //     $validator = Validator::make($request->all(), [
    //         'no_nik' => 'required|string|max:50',
    //         'nama' => 'required|string|max:255',
    //         'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
    //         'tempat_lahir' => 'required|string|max:100',
    //         'tanggal_lahir' => 'required|date|before_or_equal:today',
    //         'status' => 'required|in:Kawin,Belum Kawin,Janda,Duda',
    //         'agama' => 'required|in:Islam,Kristen Protestan,Kristen Katolik,Hindu,Buddha,Konghucu',
    //         'pendidikan' => 'required|string|max:100',
    //         'pekerjaan' => 'required|string|max:100',
    //         'kep_di_kelurahan' => 'required|string|max:100',
    //         'alamat' => 'required|string|max:255',
    //         'tgl_mulai' => 'required|date',
    //     ]);

    //     if ($validator->fails()) {
    //         return back()->withErrors($validator)->withInput()->with('edit_id', $id);
    //     }

    //     // Validasi tambahan untuk 'Lainnya'
    //     if ($request->pendidikan === 'Lainnya' && empty($request->pendidikan_lainnya)) {
    //         return back()->withErrors(['pendidikan_lainnya' => 'Kolom pendidikan lainnya wajib diisi.'])->withInput()->with('edit_id', $id);
    //     }
        
    //     if ($request->kep_di_kelurahan === 'Lainnya' && empty($request->kep_di_kelurahan_lainnya)) {
    //         return back()->withErrors(['kep_di_kelurahan_lainnya' => 'Kolom kependudukan lainnya wajib diisi.'])->withInput()->with('edit_id', $id);
    //     }

    //     // Proses nilai pendidikan dan kependudukan
    //     $pendidikan = $request->pendidikan === 'Lainnya' ? $request->pendidikan_lainnya : $request->pendidikan;
    //     $kependudukan = $request->kep_di_kelurahan === 'Lainnya' ? $request->kep_di_kelurahan_lainnya : $request->kep_di_kelurahan;

    //     // Update data penduduk
    //     $penduduk = Penduduk::with('rt.rwDetail')->findOrFail($id);

    //     // 🔐 Cek akses spesifik via policy
    //     Gate::authorize('update', $penduduk);

    //     $penduduk->update([
    //         'no_nik' => $request->no_nik,
    //         'nama' => $request->nama,
    //         'jenis_kelamin' => $request->jenis_kelamin,
    //         'tempat_lahir' => $request->tempat_lahir,
    //         'tanggal_lahir' => $request->tanggal_lahir,
    //         'status' => $request->status,
    //         'agama' => $request->agama,
    //         'pendidikan' => $pendidikan,
    //         'pekerjaan' => $request->pekerjaan,
    //         'kep_di_kelurahan' => $kependudukan,
    //         'alamat' => $request->alamat,
    //         'tgl_mulai' => $request->tgl_mulai,
    //     ]);

    //     logActivity('mengedit', 'data penduduk', $penduduk->nama);

    //     return redirect()->back()->with('success', 'Data berhasil diubah.');
    // }

    public function update(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Pastikan role valid
        if (!$user || !$user->hasAnyRole(['admin', 'rw', 'rt'])) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Temukan data penduduk
        try {
            $penduduk = Penduduk::with('rt', 'rw')->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data penduduk tidak ditemukan'
            ], 404);
        }

        // Akses wilayah: RT hanya bisa update penduduk di rt_id sendiri
        if ($user->role === 'rt' && $penduduk->rt_id !== $user->rt_id) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak memiliki izin untuk mengubah data ini.'
            ], 403);
        }

        // RW hanya bisa update RT di bawahnya (cek apakah RT milik RW-nya)
        if ($user->role === 'rw') {
            $rtValid = RT::where('id', $penduduk->rt_id)
                ->where('rw_id', $user->rw_id)
                ->exists();

            if (!$rtValid) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data RT ini tidak berada dalam RW Anda.'
                ], 403);
            }
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'no_nik' => 'required|string|max:50|unique:penduduks,no_nik,' . $id,
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'status' => 'required|in:Kawin,Belum Kawin,Janda,Duda',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Khonghucu',
            'pendidikan' => 'required|string|max:100',
            'pendidikan_lainnya' => 'nullable|string|max:255',
            'pekerjaan' => 'required|string|max:100',
            'kep_di_kelurahan' => 'required|string|max:100',
            'kep_di_kelurahan_lainnya' => 'nullable|string|max:255',
            'alamat' => 'required|string|max:255',
            'tgl_mulai' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validasi "Lainnya"
        if ($request->pendidikan === 'Lainnya' && empty($request->pendidikan_lainnya)) {
            return response()->json([
                'status' => false,
                'message' => 'Kolom pendidikan lainnya wajib diisi.'
            ], 422);
        }

        if ($request->kep_di_kelurahan === 'Lainnya' && empty($request->kep_di_kelurahan_lainnya)) {
            return response()->json([
                'status' => false,
                'message' => 'Kolom kependudukan lainnya wajib diisi.'
            ], 422);
        }

        // Gunakan nilai dari input atau dari "lainnya"
        $pendidikan = $request->pendidikan === 'Lainnya' ? $request->pendidikan_lainnya : $request->pendidikan;
        $kependudukan = $request->kep_di_kelurahan === 'Lainnya' ? $request->kep_di_kelurahan_lainnya : $request->kep_di_kelurahan;

        
        // Update data penduduk
        $penduduk->update([
            'no_nik' => $request->no_nik,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'status' => $request->status,
            'agama' => $request->agama,
            'pendidikan' => $pendidikan,
            'pekerjaan' => $request->pekerjaan,
            'kep_di_kelurahan' => $kependudukan,
            'alamat' => $request->alamat,
            'tgl_mulai' => $request->tgl_mulai,
        ]);

        logActivity('mengedit', 'data penduduk', $penduduk->nama);

        return response()->json([
            'status' => true,
            'message' => 'Data penduduk berhasil diperbarui',
            'data' => $penduduk
        ]);
    }
    
    // public function destroy($id)
    // {
    //     $penduduk = Penduduk::with('rt.rwDetail.daerah')->findOrFail($id); // untuk memastikan relasi siap di policy
    //     Gate::authorize('delete', $penduduk);

    //     $penduduk->delete();

    //     logActivity('menghapus', 'data penduduk', $penduduk->nama);
    //     return redirect()->back()->with('success', 'Data penduduk berhasil dihapus.');
    // }

    public function destroy($id)
    {
        // Ambil data penduduk lengkap dengan relasi
        $penduduk = Penduduk::with('rt.rwDetail.daerah')->findOrFail($id);

        // Validasi apakah relasi lengkap
        if (
            !$penduduk->rt ||
            !$penduduk->rt->rwDetail ||
            !$penduduk->rt->rwDetail->daerah
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Data wilayah (RT/RW/Daerah) tidak lengkap. Tidak dapat menghapus data.'
            ], 400); // Bad Request
        }

        // Cek otorisasi pengguna (policy)
        Gate::authorize('delete', $penduduk);

        // Hapus data
        $penduduk->delete();

        // Log aktivitas
        logActivity('menghapus', 'data penduduk', $penduduk->nama);

        return response()->json([
            'status' => true,
            'message' => 'Data penduduk berhasil dihapus.'
        ]);
    }
}
