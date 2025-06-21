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
            return [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->getRoleNames()->first() ?? '-', // dari spatie
                'email' => $user->email ?? '-',
                'rw' => optional($user->rwDetail)->nama_rw ?? '-',
                'rt' => optional($user->rtDetail)->nama_rt ?? '-',
                'daerah' => optional($user->daerah)->nama_daerah ?? '-',
                'created_at' => $user->created_at?->format('d-m-Y H:i') ?? '-',
            ];
        });

        return response()->json($formatted);
    }
}
