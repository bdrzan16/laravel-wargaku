<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua user beserta relasi rwDetail, rtDetail, dan daerah
        $users = User::with(['rwDetail', 'rtDetail', 'daerah'])->get();

        // Ubah data menjadi format yang mudah dibaca di frontend
        $formatted = $users->map(function ($user) {
            $role = $user->getRoleNames()->first() ?? '-';

            // Default values
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

        return response()->json($formatted);
    }
}
