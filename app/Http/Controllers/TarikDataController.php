<?php

namespace App\Http\Controllers;

date_default_timezone_set('Asia/Jakarta');

use Illuminate\Http\Request;
use App\Services\BaseService;
use App\Services\TarikDataService;
use Dotenv\Repository\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Response;
use Http;

class TarikDataController extends Controller
{
    protected $title    = "ERP Percik Tours | ";

    private function link_api()
    {
        return env('API_PERCIK_V2');
    }

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
        $tgl_cari   = $request->all()['tgl_cari'];
        // $url        = $host . "/api/presensi/get_data_presensi?tgl_awal=".$tgl_cari;

        $get_data   = Http::get($this->link_api() . "/api/presensi/get_data_presensi?tgl_awal=" . $tgl_cari);

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

    // 18 NOVEMBER 2024
    // NOTE : SYNC DATA UMHAJ KE LOCAL
    public function umhaj_jadwal_umrah_sync(Request $request)
    {
        // DATA
        $tahun  = $request->all()['tahun'];
        // API URL
        $base_url   = env('API_PERCIK_V2');
        $url        = $base_url . "/api/umhaj/master/jadwal_umrah?tahun=" . $tahun;
        
        // GET DATA FROM API
        $umhaj_data = Http::get($url);

        if($umhaj_data->status() >= 200 || $umhaj_data->status() < 300)
        {
            // POST KE MODEL
            $data_send  = [
                "tahun"     => $tahun,
                "umhaj_data"=> $umhaj_data->json('data'),
                "user_id"   => Auth::user()->id,
                "ip_address"=> $request->ip(),
            ];
            
            $do_save    = TarikDataService::do_sync_jadwal_umrah_local($data_send);
            
            if($do_save['status'] == 'berhasil') {
                $output     = [
                    "success"   => true,
                    "status"    => 200,
                    "message"   => "Berhasil Tarik Data Jadwal Umrah Sebanyak : " . $do_save['count'],
                    "data"      => [],
                ];
            } else {
                $output     = [
                    "success"   => false,
                    "status"    => 500,
                    "message"   => "Gagal Tarik Data Jadwal Umrah",
                    "data"      => [],
                ];
            }
        } else {
            $output     = [
                "success"       => false,
                "status"        => $umhaj_data->status(),
                "message"       => $umhaj_data->json('message'),
                "data"          => []          
            ];
        }

        return Response::json($output, $output['status']);
    }

    // 15 JANUARI 2025
    // NOTE : TARIK DATA UNTUK SUMMARY
    public function perciktourscom_index()
    {
        $data   = [
            "title"     => $this->title . " Perciktours.com",
            "sub_title" => "Tarik Data untuk Perciktours.com",
        ];

        return view('activities.tarik_data.perciktours.index', $data);
    }

    public function perciktourscom_summary_data()
    {
        $get_data   = TarikDataService::percikTours_data_summary();

        if(count($get_data) > 0) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil ambil data summary",
                "data"      => $get_data,
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 404,
                "message"   => "Gagal ambil data summary",
                "data"      => [
                    [
                        'total_jemaah'      => 0,
                        'total_agen'        => 0,
                        'total_perjalanan'  => 0,
                        'total_pembimbing'  => 0,
                        'last_update'       => date('Y-m-d H:i:s'),
                    ]
                ]
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function perciktourscom_get_data_summary()
    {
        $get_data   = Http::get($this->link_api() . "/api/perciktours/tarik_data_summary");
        
        $output     = [
            "success"   => $get_data->json()['success'],
            "status"    => $get_data->status(),
            "message"   => $get_data->json()['message'],
            "data"      => $get_data->json()['data'],
        ];
        
        return Response::json($output, $output['status']);
    }
}