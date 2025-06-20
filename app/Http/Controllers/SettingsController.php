<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        logActivity('mengakses', 'halaman pengaturan');

        return response()->json([
            'message' => 'Data profil berhasil diambil.',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
                'daerah' => $user->daerah->name ?? '-',
            ],
        ]);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        logActivity('mengedit', 'profil', $user->name);

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Password lama tidak cocok.'], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        logActivity('mengubah', 'password', $user->name);

        return response()->json(['message' => 'Password berhasil diperbarui.']);
    }


    // public function index()
    // {
    //     // Menggunakan filter yang ada di model untuk filter data penduduk
    //     $users = User::all();

    //     logActivity('mengakses', 'halaman pengaturan');
    //     return view('pages.settings', compact('users'));
    // }

    // public function update(Request $request)
    // {
    //     /** @var \App\Models\User $users */
    //     $users = Auth::user();

    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|max:255|unique:users,email,' . $users->id,
    //     ]);

    //     $users->update([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //     ]);

    //     logActivity('mengedit', 'profil', $users->name);
    //     return redirect()->back()->with('profile_success', 'Profil berhasil diperbarui.');
    // }

    // public function updatePassword(Request $request)
    // {
    //     /** @var \App\Models\User $users */
    //     $users = Auth::user();

    //     $request->validate([
    //         'current_password' => ['required'],
    //         'new_password' => ['required', 'string', 'min:8', 'confirmed'],
    //     ]);

    //     if (!Hash::check($request->current_password, $users->password)) {
    //         return back()->withErrors(['current_password' => 'Password lama tidak cocok.']);
    //     }

    //     $users->update([
    //         'password' => Hash::make($request->new_password),
    //     ]);

    //     logActivity('mengubah', 'password', $users->name);
    //     return back()->with('password_success', 'Password berhasil diperbarui.');
    // }
}
