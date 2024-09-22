<?php

namespace App\Http\Controllers;

date_default_timezone_set('Asia/Jakarta');

use Illuminate\Http\Request;
use App\Services\BaseService;
use App\Services\TarikDataService;
use Response;

class TarikDataController extends Controller
{
    protected $title    = "ERP Percik Tours | ";

    public function tarik_data_index()
    {
        $data   = [
            "title"     => $this->title."Tarik Data",
            "sub_title" => "Tarik Data Absen dari Presensi ke ERP",
        ];

        return view('activities.tarik_data.absensi.index', $data);
    }

    public function tarik_data_get_absensi(Request $request)
    {
        $data   = [
            "tgl_cari"  => $request->all()['tgl_cari']
        ];

        $getData    = TarikDataService::get_data_absensi($data);
        $abs_temp_data  = [];

        if(count($getData) > 0) {
            for($i = 0; $i < count($getData); $i++)
            {
                $abs_temp_data[]    = [
                    "abs_no"            => $i + 1,
                    "abs_name"          => $getData[$i]->name,
                    "abs_in"            => date('H:i:s', strtotime($getData[$i]->clock_in)),
                    "abs_in_location"   => $getData[$i]->clock_in_latitude.", ".$getData[$i]->clock_in_longitude,
                    "abs_out"           => $getData[$i]->clock_out == "0000-00-00 00:00:00" ? null : date('H:i:s', strtotime($getData[$i]->clock_out)),
                    "abs_out_location"  => $getData[$i]->clock_out == "0000-00-00 00:00:00" ? null : $getData[$i]->clock_out_latitude.", ".$getData[$i]->clock_out_longitude,
                ];
            }

            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Ambil Data Presensi",
                "data"      => $abs_temp_data,
            ];

        } else {
            $output     = [
                "success"   => false,
                "status"    => 404,
                "message"   => "Data Tidak Ditemukan",
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function tarik_data_absensi(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $today  = $request->all()['tgl_cari'];
        // $today  = "2024-09-05";

        // GET DATA
        $getData    = TarikDataService::get_tarik_data_presensi($today);

        $output     = [
            "success"   => true,
            "status"    => 200,
            "message"   => $getData['message'],
        ];

        return Response::json($output, $output['status']);
    }
}
