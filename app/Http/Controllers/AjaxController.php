<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function getRW(Request $request)
    {
        $daerah = strtolower($request->daerah);

        $rwList = User::role('rw')
            ->whereRaw('LOWER(daerah) = ?', [$daerah])
            ->select('rw')
            ->distinct()
            ->orderBy('rw')
            ->get();

        return response()->json($rwList);
    }

    public function getRT(Request $request)
    {
        $daerah = strtolower($request->daerah);
        $rw = $request->rw;

        $rtList = User::role('rt')
            ->whereRaw('LOWER(daerah) = ?', [$daerah])
            ->where('rw', $rw)
            ->select('rt')
            ->distinct()
            ->orderBy('rt')
            ->get();

        return response()->json($rtList);
    }
}
