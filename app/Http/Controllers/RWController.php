<?php

namespace App\Http\Controllers;

use App\Models\RW;
use App\Models\User;
use App\Models\Daerah;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RWController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->hasRole('rw')) {
            return response()->json([
                'message' => 'Akses ditolak. Hanya RW yang dapat mengakses.'
            ], 403);
        }

        $year = now()->year;

        $chartData = collect(range(1, 12))->map(function ($month) use ($user, $year) {
            return Penduduk::where('rw_id', $user->rw_id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
        })->toArray();

        $penduduk = Penduduk::where('rw_id', $user->rw_id)->get();

        $pieData = [
            'jenis_kelamin' => $penduduk->groupBy('jenis_kelamin')->map->count(),
            'agama' => $penduduk->groupBy('agama')->map->count(),
            'pendidikan' => $penduduk->groupBy('pendidikan')->map->count(),
            'pekerjaan' => $penduduk->groupBy('pekerjaan')->map->count(),
            'status' => $penduduk->groupBy('status')->map->count(),
            'usia' => $penduduk->map(function ($item) {
                return \Carbon\Carbon::parse($item->tanggal_lahir)->age;
            })->filter(function ($age) {
                return $age >= 1;
            })->map(function ($age) {
                if ($age <= 5) return '1-5';
                elseif ($age <= 12) return '6-12';
                elseif ($age <= 17) return '13-17';
                elseif ($age <= 25) return '18-25';
                elseif ($age <= 40) return '26-40';
                elseif ($age <= 60) return '41-60';
                else return '61+';
            })->groupBy(function ($group) {
                return $group;
            })->map->count(),
        ];

        return response()->json([
            'chartData' => $chartData,
            'pieData' => $pieData,
        ]);
    }

    // public function index()
    // {
    //     $rwId = Auth::user()->rw_id;
    //     $year = now()->year;
        
    //     $chartData = collect(range(1, 12))->map(function ($month) use ($rwId, $year) {
    //         return Penduduk::where('rw_id', $rwId)
    //             ->whereYear('created_at', $year)
    //             ->whereMonth('created_at', $month)
    //             ->count();
    //     });

    //     // Ambil seluruh penduduk RW ini
    //     $penduduk = Penduduk::where('rw_id', $rwId)->get();

    //     // Pie chart: hitung berdasarkan kategori
    //     $pieData = [
    //         'jenis_kelamin' => $penduduk->groupBy('jenis_kelamin')->map->count(),
    //         'agama' => $penduduk->groupBy('agama')->map->count(),
    //         'pendidikan' => $penduduk->groupBy('pendidikan')->map->count(),
    //         'pekerjaan' => $penduduk->groupBy('pekerjaan')->map->count(),
    //         'status' => $penduduk->groupBy('status')->map->count(),
    //         'usia' => $penduduk->map(function ($item) {
    //             return \Carbon\Carbon::parse($item->tanggal_lahir)->age;
    //         })->filter(function ($age) {
    //             return $age >= 1;
    //         })->map(function ($age) {
    //             if ($age <= 5) return '1-5';
    //             elseif ($age <= 12) return '6-12';
    //             elseif ($age <= 17) return '13-17';
    //             elseif ($age <= 25) return '18-25';
    //             elseif ($age <= 40) return '26-40';
    //             elseif ($age <= 60) return '41-60';
    //             else return '61+';
    //         })->groupBy(function ($group) {
    //             return $group;
    //         })->map->count(),
    //     ];

    //     logActivity('mengakses', 'halaman dashboard');
    //     return view('dashboard.rw', compact('chartData', 'pieData'));
    // }
}
