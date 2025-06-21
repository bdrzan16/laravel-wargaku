<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Ambil user BUKAN admin, dan paginate 10 per halaman
        $users = User::with(['rwDetail', 'rtDetail', 'daerah'])
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->paginate(10); // <= ini paginasi aktif

        // Format data untuk frontend
        $formatted = $users->getCollection()->map(function ($user) {
            $role = $user->getRoleNames()->first() ?? '-';

            $rw = optional($user->rwDetail)->name ?? '-';
            $rt = optional($user->rtDetail)->name ?? '-';

            // Jika role RT, ambil RW dari rtDetail->rwDetail
            if ($role === 'rt') {
                $rw = optional($user->rtDetail?->rwDetail)->name ?? $rw;
            }

            return [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $role,
                'email' => $user->email ?? '-',
                'rw' => $rw,
                'rt' => $rt,
                'daerah' => optional($user->daerah)->name ?? '-',
                'created_at' => $user->created_at?->format('d-m-Y H:i') ?? '-',
            ];
        });

        // Gantikan collection dengan hasil yang sudah diformat
        $users->setCollection($formatted);

        // Return response lengkap (termasuk pagination info)
        return response()->json($users);
    }
}
