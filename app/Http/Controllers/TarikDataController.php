<?php

namespace App\Http\Controllers;

date_default_timezone_set('Asia/Jakarta');

use Illuminate\Http\Request;
use App\Services\BaseService;
use App\Services\TarikDataService;
use Response;
use Http;

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
        $host   = env('API_PERCIK_V2');
        $tgl_cari   = $request->all()['tgl_cari'];
        
        $url        = $host . "/api/presensi/get_data_presensi?tgl_awal=".$tgl_cari;
        $get_data   = Http::get($url);

        if($get_data->status() == 200) {
            $presensi_data  = $get_data->json()['data'];
            for($i = 0; $i < count($presensi_data); $i++)
            {
                $abs_temp_data[]    = [
                    "abs_no"            => $i + 1,
                    "abs_name"          => $presensi_data[$i]['emp_name'],
                    "abs_in"            => date('H:i:s', strtotime($presensi_data[$i]['prs_in_time'])),
                    "abs_in_location"   => $presensi_data[$i]['prs_in_location'],
                    "abs_out"           => !empty($presensi_data[$i]['prs_out_time']) ? date('H:i:s', strtotime($presensi_data[$i]['prs_out_time'])) : "",
                    "abs_out_location"  => !empty($presensi_data[$i]['prs_out_location']) ? $presensi_data[$i]['prs_out_location'] : "",
                ];
            }

            $output     = [
                "success"   => true,
                "status"    => $get_data->status(),
                "message"   => "Berhasil Ambil Data Presensi Tanggal ".date('d-M-Y', strtotime($tgl_cari)),
                "data"      => $abs_temp_data,
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => $get_data->status(),
                "message"   => "Gagal Ambil Data Presensi Tanggal ".date('d-M-Y', strtotime($tgl_cari)),
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

    // 15 NOVEMBER 2024
    // PENAMABAHAN MODUL TARIK DATA UMRAH
    public function umhaj_jadwal_umrah_index()
    {
        $data    = [
            "title"     => "ERP Percik Tours | Tarik Data",
            "sub_title" => "Tarik Data - Jadwal Umrah (Umhaj)",
        ];

        return view('activities.tarik_data.umhaj.jadwal_umrah.index', $data);
    }
    
    // AMBIL DATA JADWAL UMRAH DARI UMHAJ
    public function umhaj_jadwal_umrah_get(Request $request)
    {
        $base_url   = env('API_PERCIK_V2');

        $url        = $base_url . "/api/umhaj/master/jadwal_umrah?tahun=".$request->all()['tahun'];
        $get_data   = Http::get($url);

        if($get_data->status() == 200) {
            $output     = [
                "success"   => true,
                "status"    => $get_data->status(),
                "message"   => $get_data->json('message'),
                "data"      => $get_data->json('data'),
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => $get_data->status(),
                "message"   => $get_data->json('message'),
                "data"      => []
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function umhaj_jadwal_umrah_sync(Request $request)
    {
        // GET DATA FROM UMHAJ
        $base_url   = env('API_PERCIK_V2');

        $url        = $base_url + "/api/umhaj/master/jadwal_umrah?tahun=".$request->all()['tahun'];
        $get_data   = Http::get($url);

        if($get_data->status() == 200) {
            $data_umhaj     = $get_data->json('data');
            
            $do_sync_data   = TarikDataService::sync_data_jadwal_umrah($data_umhaj, $request->all()['tahun']);

            var_dump($do_sync_data);die();

        } else {
            $output     = [
                "success"   => false,
                "status"    => $get_data->status(),
                "message"   => $get_data->json('message'),
                "data"      => []
            ];
        }

        return Response::json($output, $output['status']);
    }
}