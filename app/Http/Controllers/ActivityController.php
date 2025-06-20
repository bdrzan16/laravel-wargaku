<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $perPage = (int) $request->get('per_page', 10); // default 10
        $page = (int) $request->get('page', 1); // default 1

        // Ambil log + relasi causer dan role langsung dari query builder
        $query = Activity::with('causer.roles')
            ->latest();

        // Ambil semua dulu, lalu filter pakai Gate (harus pakai collection)
        $filteredLogs = $query->get()
            ->filter(fn ($log) => Gate::allows('view-activity', $log))
            ->values();

        // Paginasi manual setelah filter
        $total = $filteredLogs->count();
        $paginatedLogs = $filteredLogs->forPage($page, $perPage)->values();

        // Format JSON data
        $data = $paginatedLogs->map(function ($log) {
            $causer = $log->causer;
            $role = strtoupper(optional($causer)->getRoleNames()->first() ?? '-');
            $name = '-';

            if ($causer?->hasRole('rw')) {
                $name = $causer->rwDetail->name ?? '-';
            } elseif ($causer?->hasRole('rt')) {
                $name = $causer->rtDetail->name ?? '-';
            }

            return [
                'id'          => $log->id,
                'description' => $log->description ?? '-',
                'user_role'   => $role,
                'user_name'   => $name,
                'timestamp'   => optional($log->updated_at)
                    ->timezone('Asia/Jakarta')
                    ->format('Y-m-d H:i:s'),
            ];
        });

        // Simpan aktivitas log
        logActivity('mengakses', 'halaman catatan aktivitas');

        return response()->json([
            'status'  => 'success',
            'message' => 'Data log aktivitas berhasil diambil',
            'data'    => $data,
            'meta'    => [
                'current_page' => $page,
                'per_page'     => $perPage,
                'total'        => $total,
                'last_page'    => ceil($total / $perPage),
            ],
        ]);
    }


    // public function index()
    // {
    //     $user = Auth::user();

    //     // Ambil hanya log yang ada causer-nya + relasi role (lebih efisien)
    //     $allLogs = Activity::with('causer.roles')
    //         ->latest()
    //         ->get() // ambil semua data dulu
    //         ->filter(fn ($log) => Gate::allows('view-activity', $log)) // filter berdasarkan policy
    //         ->values(); // reset index
      
    //     // Paginasi manual
    //     $perPage = 10;
    //     $page = request()->get('page', 1);
    //     $pagedLogs = new LengthAwarePaginator(
    //         $allLogs->forPage($page, $perPage),
    //         $allLogs->count(),
    //         $perPage,
    //         $page,
    //         ['path' => request()->url(), 'query' => request()->query()]
    //     );

    //     logActivity('mengakses', 'halaman catatan aktivitas');

    //     return view('pages.activity', ['logs' => $pagedLogs]);
    // }
}
