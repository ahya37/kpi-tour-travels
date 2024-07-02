<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function report(Request $request)
    {
        $data   = [
            'title'     => 'Laporan Presensi',
            'sub_title' => 'Laporan Presensi',
        ];

        return view('presensi/report', $data);
    }
}
