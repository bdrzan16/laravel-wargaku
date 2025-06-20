<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{

    public function index(Request $request)
    {
        // Cek apakah request dari API (misal pakai prefix atau header)
        if ($request->wantsJson() || $request->is('api/*')) {
            $users = User::where(function ($query) {
                $query->whereNotNull('rw_id')->orWhereNotNull('rt_id');
            })->get();

            $chartData = array_fill(0, 12, 0);
            foreach ($users as $user) {
                if ($user->created_at) {
                    $monthIndex = Carbon::parse($user->created_at)->month - 1;
                    $chartData[$monthIndex]++;
                }
            }

            $rwUsers = User::whereHas('rwDetail')->count();
            $rtUsers = User::whereHas('rtDetail')->count();

            $topDaerahs = Penduduk::select('daerah_id', DB::raw('count(*) as jumlah'))
                ->groupBy('daerah_id')
                ->orderByDesc('jumlah')
                ->limit(5)
                ->with('daerah')
                ->get()
                ->map(function ($item) {
                    return [
                        'nama' => $item->daerah->name,
                        'jumlah' => $item->jumlah
                    ];
                });

            return response()->json([
                'pie' => [
                    'rw' => $rwUsers,
                    'rt' => $rtUsers,
                ],
                'chart' => $chartData,
                'bar' => $topDaerahs,
            ]);
        }

        // Kalau request dari web biasa, tampilkan Blade
        $users = User::where(function ($query) {
            $query->whereNotNull('rw_id')->orWhereNotNull('rt_id');
        })->get();

        $chartData = array_fill(0, 12, 0);
        foreach ($users as $user) {
            if ($user->created_at) {
                $monthIndex = Carbon::parse($user->created_at)->month - 1;
                $chartData[$monthIndex]++;
            }
        }

        $rwUsers = User::whereHas('rwDetail')->count();
        $rtUsers = User::whereHas('rtDetail')->count();

        $topDaerahs = Penduduk::select('daerah_id', DB::raw('count(*) as jumlah'))
            ->groupBy('daerah_id')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->with('daerah')
            ->get();

        $totalMax = $topDaerahs->max('jumlah');

        return view('dashboard.admin', compact('chartData', 'rwUsers', 'rtUsers', 'topDaerahs', 'totalMax'));
    }

    // public function index()
    // {
    //     $users = User::where(function ($query) {
    //         $query->whereNotNull('rw_id')->orWhereNotNull('rt_id');
    //     })->get();

    //     $chartData = array_fill(0, 12, 0);
    //     foreach ($users as $user) {
    //         if ($user->created_at) {
    //             $monthIndex = Carbon::parse($user->created_at)->month - 1;
    //             $chartData[$monthIndex]++;
    //         }
    //     }

    //     $rwUsers = User::whereHas('rwDetail')->count();
    //     $rtUsers = User::whereHas('rtDetail')->count();

    //     $topDaerahs = Penduduk::select('daerah_id', DB::raw('count(*) as jumlah'))
    //         ->groupBy('daerah_id')
    //         ->orderByDesc('jumlah')
    //         ->limit(5)
    //         ->with('daerah')
    //         ->get()
    //         ->map(function ($item) {
    //             return [
    //                 'nama' => $item->daerah->name,
    //                 'jumlah' => $item->jumlah
    //             ];
    //         });

    //     return response()->json([
    //         'pie' => [
    //             'rw' => $rwUsers,
    //             'rt' => $rtUsers,
    //         ],
    //         'chart' => $chartData,
    //         'bar' => $topDaerahs,
    //     ]);
    // }

    // public function index()
    // {
    //     // Grafik Line : Hitung Banyak User
    //     $users = User::where(function ($query) {
    //         $query->whereNotNull('rw_id')
    //             ->orWhereNotNull('rt_id');
    //     })->get();

    //     $chartData = array_fill(0, 12, 0); // Jan-Dec
        
    //     foreach ($users as $user) {
    //         if ($user->created_at) {
    //             $monthIndex = Carbon::parse($user->created_at)->month - 1;
    //             $chartData[$monthIndex]++;
    //         }
    //     }
        
    //     // Diagram Pie : Persentase perbandingan user RT dan RW
    //     $totalUsers = User::count();
    //     $rwUsers = User::whereHas('rwDetail')->count();
    //     $rtUsers = User::whereHas('rtDetail')->count();

    //     $pieData = [
    //         'RW' => $rwUsers,
    //         'RT' => $rtUsers,
    //     ];

    //     // Diagram Bar - 5 daerah dengan jumlah penduduk terbanyak
    //     $topDaerahs = Penduduk::select('daerah_id', DB::raw('count(*) as jumlah'))
    //         ->groupBy('daerah_id')
    //         ->orderByDesc('jumlah')
    //         ->limit(5)
    //         ->with('daerah') // pastikan relasi daerah sudah ada di model Penduduk
    //         ->get();

    //     $totalMax = $topDaerahs->max('jumlah'); // Untuk hitung persentase progress bar

    //     logActivity('mengakses', 'halaman dashboard');
    //     return view('dashboard.admin', compact('chartData', 'pieData', 'topDaerahs', 'totalMax'));
    // }
}
