<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class RTController extends Controller
{
    public function index()
    {
        $rtId = Auth::user()->rt_id;
        $year = now()->year;

        $chartData = collect(range(1, 12))->map(function ($month) use ($rtId, $year) {
            return Penduduk::where('rt_id', $rtId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
        });

        $penduduk = Penduduk::where('rt_id', $rtId)->get();

        $pieData = [
            'jenis_kelamin' => $penduduk->groupBy('jenis_kelamin')->map->count(),
            'agama' => $penduduk->groupBy('agama')->map->count(),
            'pendidikan' => $penduduk->groupBy('pendidikan')->map->count(),
            'pekerjaan' => $penduduk->groupBy('pekerjaan')->map->count(),
            'status' => $penduduk->groupBy('status')->map->count(),
            'usia' => $penduduk->map(function ($item) {
                return Carbon::parse($item->tanggal_lahir)->age;
            })->filter(fn ($age) => $age >= 1)
              ->map(function ($age) {
                  if ($age <= 5) return '1-5';
                  if ($age <= 12) return '6-12';
                  if ($age <= 17) return '13-17';
                  if ($age <= 25) return '18-25';
                  if ($age <= 40) return '26-40';
                  if ($age <= 60) return '41-60';
                  return '61+';
              })->groupBy(fn ($group) => $group)->map->count(),
        ];

        return response()->json([
            'chart' => $chartData,
            'data' => $pieData,
            'message' => 'success'
        ]);
    }


    // public function index()
    // {
    //     $rtId = Auth::user()->rt_id;
    //     $year = now()->year;

    //     $chartData = collect(range(1, 12))->map(function ($month) use ($rtId, $year) {
    //         return Penduduk::where('rt_id', $rtId)
    //             ->whereYear('created_at', $year)
    //             ->whereMonth('created_at', $month)
    //             ->count();
    //     });

    //     // Ambil seluruh penduduk RW ini
    //     $penduduk = Penduduk::where('rt_id', $rtId)->get();

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

    //     logActivity('mengakses halaman dashboard');
    //     return view('dashboard.rt', compact('chartData', 'pieData'));
    // }
}
