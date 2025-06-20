<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

if (!function_exists('logActivity')) {
    function logActivity($action = 'mengakses', $halaman = 'halaman', $targetNama = null)
    {
        if (!Auth::check()) return;
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $role = strtoupper($user->getRoleNames()->first());

        // Ambil nama RW & RT sesuai role
        $rt = '';
        $rw = '';

        if ($role === 'RT') {
            $rt = $user->rtDetail->name ?? '';
            $rw = $user->rtDetail?->rwDetail->name ?? ''; // Akses RW lewat RT
        } elseif ($role === 'RW') {
            $rw = $user->rwDetail->name ?? '';
        }
        // $rw = $user->rwDetail->name ?? '';
        // $rt = $user->rtDetail->name ?? '';
        $daerah = $user->daerah->name ?? '';

        // Format identitas user
        $identity = match ($role) {
            'ADMIN' => "$role",
            'RW'    => "$role $rw $daerah",
            'RT'    => "$role $rt RW $rw $daerah",
            default => "$role",
        };

        // Cegah duplikasi kata "halaman"
        // $target = $targetNama ? "data $targetNama" : (Str::contains($action, 'halaman') ? $halaman : "halaman $halaman");

        // // Tambahkan nama target jika ada (contoh: "data Fauzan Ambadar")
        $target = trim("$halaman $targetNama");

        // Simpan log
        activity()->causedBy($user)->log("$identity $action $target");
    }
}

// if (!function_exists('logActivity')) {
//     function logActivity($message)
//     {
//         $user = Auth::user();
//         $role = strtoupper($user->role->name ?? 'GUEST');
//         $rw = $user->rw ?? '';
//         $daerah = $user->daerah ?? '';

//         activity()
//             ->causedBy($user)
//             ->log("{$role} {$rw} {$daerah} {$message}");
//     }
// }