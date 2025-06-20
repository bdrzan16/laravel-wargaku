<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RW;
use App\Models\RT;
use App\Models\Daerah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:rw,rt',
            'rw' => ['required', 'regex:/^\d{2}$/'],
            'rt' => ['required_if:role,rt', 'nullable', 'regex:/^\d{2}$/'],
            'daerah' => 'required|string|max:255',
        ]);

        try {
            // Simpan/ambil daerah
            $daerah = Daerah::firstOrCreate([
                'name' => ucwords(strtolower($request->daerah))
            ]);
            $daerah_id = $daerah->id;

            $rwModel = null;
            $rtModel = null;

            if ($request->role === 'rw') {
                $rwModel = RW::create([
                    'name' => $request->rw,
                    'daerah_id' => $daerah_id,
                ]);
            }

            if ($request->role === 'rt') {
                $rwModel = RW::where('name', $request->rw)
                    ->where('daerah_id', $daerah_id)
                    ->first();

                if (!$rwModel) {
                    return response()->json([
                        'success' => false,
                        'message' => 'RW tidak ditemukan di daerah tersebut.'
                    ], 400);
                }

                $rtModel = RT::create([
                    'name' => $request->rt,
                    'rw_id' => $rwModel->id,
                    'daerah_id' => $daerah_id,
                ]);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rw_id' => $rwModel?->id,
                'rt_id' => $rtModel?->id,
                'daerah_id' => $daerah_id,
            ]);

            $user->assignRole($request->role);

            if ($request->role === 'rw') {
                $rwModel->update(['user_id' => $user->id]);
            }

            if ($request->role === 'rt') {
                $rtModel->update(['user_id' => $user->id]);
            }

            activity()->causedBy($user)->log('registrasi user baru');

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'user' => $user
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat registrasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:8|confirmed',
    //         'role' => 'required|in:rw,rt',
    //         'rw' => ['required', 'regex:/^\d{2}$/'],
    //         'rt' => ['required_if:role,rt', 'nullable', 'regex:/^\d{2}$/'],
    //         'daerah' => 'required|string|max:255',
    //     ]);

    //     // Simpan/ambil daerah
    //     $daerah = Daerah::firstOrCreate([
    //         'name' => ucwords(strtolower($request->daerah))
    //     ]);
    //     $daerah_id = $daerah->id;

    //     $rwModel = null;
    //     $rtModel = null;

    //     // Simpan data RW jika role-nya adalah RW
    //     if ($request->role === 'rw') {
    //         $rwModel = RW::create([
    //             'name' => $request->rw,
    //             'daerah_id' => $daerah_id,
    //         ]);
    //     }

    //     // Jika role RT, cari RW yang cocok
    //     if ($request->role === 'rt') {
    //         $rwModel = RW::where('name', $request->rw)
    //             ->where('daerah_id', $daerah_id)
    //             ->first();

    //         if (!$rwModel) {
    //             return back()->withErrors(['rw_id' => 'RW tidak ditemukan di daerah tersebut.']);
    //         }

    //         $rtModel = RT::create([
    //             'name' => $request->rt,
    //             'rw_id' => $rwModel->id,
    //             'daerah_id' => $daerah_id,
    //         ]);
    //     }

    //     // Simpan user baru
    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //         'rw_id' => $rwModel?->id,
    //         'rt_id' => $rtModel?->id,
    //         'daerah_id' => $daerah_id,
    //     ]);

    //     $user->assignRole($request->role);

    //     // Update RW dan tambahkan user_id
    //     if ($request->role === 'rw') {
    //         $rwModel->update([
    //             'user_id' => $user->id,
    //         ]);
    //     }

    //     if ($request->role === 'rt') {
    //         $rtModel->update([
    //             'user_id' => $user->id,
    //         ]);
    //     }

    //     activity()->causedBy($user)->log('registrasi user baru');

    //     return to_route('login')->with('status', 'Registrasi berhasil! Silakan login.');
    // }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|string|email',
    //         'password' => 'required|string',
    //     ]);

    //     $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

    //     if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
    //         $seconds = RateLimiter::availableIn($throttleKey);
    //         return response()->json([
    //             'message' => "Terlalu banyak percobaan login. Coba lagi dalam $seconds detik."
    //         ], 429);
    //     }

    //     if (Auth::attempt($credentials)) {
    //         RateLimiter::clear($throttleKey);

    //         /** @var \App\Models\User $user */
    //         $user = Auth::user();

    //         // Load relasi agar tidak terjadi N+1
    //         $user->load(['rtDetail.rwDetail', 'rwDetail', 'daerah']);

    //         // Periksa role yang valid
    //         if ($user && $user->hasRole(['admin', 'rw', 'rt'])) {
    //             $role = strtolower($user->getRoleNames()->first());

    //             // Ambil informasi rw, rt, dan daerah jika tersedia
    //             if ($role === 'rt') {
    //                 $rw = $user->rtDetail?->rwDetail?->name ?? '-';
    //                 $rt = $user->rtDetail?->name ?? '-';
    //             } elseif ($role === 'rw') {
    //                 $rw = $user->rwDetail?->name ?? '-';
    //                 $rt = '-';
    //             } else {
    //                 $rw = '-';
    //                 $rt = '-';
    //             }
    //             $rt = $user->rtDetail?->name ?? '-';
    //             $daerah = $user->daerah?->name ?? '-';

    //             // Log aktivitas login
    //             $description = match (strtoupper($role)) {
    //                 'ADMIN' => "ADMIN melakukan login",
    //                 'RW'    => "RW $rw $daerah melakukan login",
    //                 'RT'    => "RT $rt RW $rw $daerah melakukan login",
    //                 default => "$role melakukan login",
    //             };

    //             activity()->causedBy($user)->log($description);

    //             // Generate token
    //             $token = $user->createToken('auth_token')->plainTextToken;

    //             return response()->json([
    //                 'message' => 'Login berhasil',
    //                 'role' => $role, // boleh tetap ada
    //                 'token' => $token,
    //                 'user' => [
    //                     'id' => $user->id,
    //                     'email' => $user->email,
    //                     'name' => $user->name ?? 'Tidak diketahui',
    //                     'role' => $role,

    //                     // Tambahan ID untuk dipakai di Flutter
    //                     'rw_id' => $user->rw_id,
    //                     'rt_id' => $user->rt_id,
    //                     'daerah_id' => $user->daerah_id,

    //                     // Nama wilayah (opsional, untuk ditampilkan)
    //                     'rw' => $rw,
    //                     'rt' => $rt,
    //                     'daerah' => $daerah,
    //                 ],
    //             ], 200);
    //         }
    //     }

    //     // Gagal login, hit rate limiter
    //     RateLimiter::hit($throttleKey, 60);

    //     return response()->json([
    //         'message' => 'Email atau password salah.',
    //     ], 401);
    // }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam $seconds detik.",
            ]);
        }

        if (Auth::attempt($credentials)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate(); // security: regenerasi session

            $user = Auth::user();

            /** @var \App\Models\User $user */
            // Cek agar tidak mencatat log jika user null (misalnya akun dihapus)
            if ($user && $user->hasRole(['admin', 'rw', 'rt'])) {
                $role = strtoupper($user->getRoleNames()->first());

                $rw = $user->rwDetail->name ?? '-';
                $rt = $user->rtDetail->name ?? '-';
                $daerah = $user->daerah->name ?? '-';

                $description = match ($role) {
                    'ADMIN' => "$role melakukan login",
                    'RW'    => "$role $rw $daerah melakukan login",
                    'RT'    => "$role $rt RW $rw $daerah melakukan login",
                    default => "$role melakukan login",
                };

                activity()->causedBy($user)->log($description);

                return match (true) {
                    $user->hasRole('admin') => redirect('/dashboard-admin'),
                    $user->hasRole('rw')    => redirect('/dashboard-rw'),
                    $user->hasRole('rt')    => redirect('/dashboard-rt'),
                    default => $this->logoutAndRedirectWithError($request),
                };
            }
        }

        RateLimiter::hit($throttleKey, 60); // delay 60 detik
        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    private function logoutAndRedirectWithError(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->withErrors(['role' => 'Role tidak dikenali.']);
    }

    // public function logout(Request $request)
    // {
    //     if (Auth::check()) {
    //         logActivity('melakukan', 'logout');
    //         // activity()->causedBy(Auth::user())->log('melakukan logout');
    //     }
        
    //     Auth::logout();
    //     $request->session()->invalidate(); // hapus semua data session
    //     $request->session()->regenerateToken(); // regenerate CSRF token
    //     return redirect('/')->with('status', 'Berhasil logout.');
    // }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            logActivity('melakukan', 'logout');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Berhasil logout']);
        }

        return redirect('/')->with('status', 'Berhasil logout.');
    }
}
