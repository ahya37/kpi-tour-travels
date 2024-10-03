<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SysUmhajService;
use Illuminate\Support\Facades\Auth;
use Response;

class SysUmhajController extends Controller
{
    //
    protected $title    = "ERP Percik Tours | ";
    protected $mkt_view = "/marketings/umhaj";

    public function index_umhaj()
    {
        $data   = [
            "title"     => $this->title."Umhaj",
            "sub_title" => "Umhaj Dashboard"
        ];

        return view($this->mkt_view."/dashboard/index", $data);
    }

    // MASTER
    public function umhaj_umrah_get_list_program()
    {
        $get_data   = SysUmhajService::get_list_program_umrah();
        
        if(count($get_data) > 0) {
            $output     = [
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Memuat Data",
                "data"      => $get_data,
            ];
        } else {
            $output     = [
                "status"    => 404,
                "success"   => false,
                "message"   => "Gagal Memuat Data",
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function umhaj_umrah_get_data(Request $request)
    {
        if(Auth::user()->getRoleNames()[0] == 'admin' || Auth::user()->getRoleNames()[0] == 'marketing') {
            $send_data  = [
                "type_umrah"    => $request->all()['data']['jenis'] == "semua" ? "%" : $request->all()['data']['jenis'],
                "tahun_cari"    => $request->all()['data']['tahun_cari'],
                "bulan_cari"    => $request->all()['data']['bulan_cari']
            ];
    
            $get_data   = SysUmhajService::get_data_umhaj_umrah($send_data);

            if(count($get_data) > 0) {
                // MERGING DATA
                $res    = [];
                foreach($get_data as $item) :
                    $bulan_ke   = $item->BULAN_DAFTAR;
                    $total_data = $item->TOTAL_DATA;

                    if(isset($res[$bulan_ke])) {
                        $res[$bulan_ke]['total_data'] += $total_data;
                    } else {
                        $res[$bulan_ke] = [
                            "bulan_ke"      => $bulan_ke,
                            "total_data"    => $total_data
                        ];
                    }
                endforeach;
                
                for($i = 0; $i < 12; $i++)
                {
                    if(!empty($res[$i + 1]['bulan_ke'])) {
                        $data[]     = [
                            "month"     => $res[$i + 1]['bulan_ke'] < 10 ? "0".$res[$i + 1]['bulan_ke'] : $res[$i + 1]['bulan_ke'],
                            "total_data"=> $res[$i + 1]['total_data'],
                        ];
                    } else {
                        $data[]     = [
                            "month"     => $i + 1 < 10 ? "0".$i + 1 : $i + 1,
                            "total_data"=> 0,
                        ];
                    }
                }
                $output     = [
                    "success"   => true,
                    "status"    => 200,
                    "message"   => "Berhasil Ambil Data",
                    "data"      => $data,
                ];
            } else {
                $output     = [
                    "success"   => false,
                    "status"    => 404,
                    "message"   => "Gagal Mengambil Data",
                    "data"      => [],
                ];
            }

            return Response::json($output, $output['status']);
        } else {
            return redirect('/');
        }
    }

    public function umhaj_member_get_data(Request $request)
    {
        $send_data  = [
            "cs"        => $request->all()['data']['cs_name'] == 'semua' ? '%' : $request->all()['data']['cs_name'],
            "tahun_cari"=> $request->all()['data']['tahun_cari'],
            "bulan_cari"=> $request->all()['data']['bulan_cari'],
        ];

        $data       = [];

        $get_data   = SysUmhajService::get_data_umhaj_member($send_data);

        if(count($get_data) > 0) {
            for($i = 0; $i < 12; $i++) {
                $bulan_ke   = $i + 1;
                if(!empty($get_data[$i]->month)) {
                    $data[]     = [
                        "month"     => $bulan_ke < 10 ? "0".$bulan_ke : $bulan_ke,
                        "total_data"=> $get_data[$i]->total_data_member
                    ];
                } else {
                    $data[]     = [
                        "month"     => $bulan_ke < 10 ? "0".$bulan_ke : $bulan_ke,
                        "total_data"=> 0,
                    ];
                }
            }

            $output     = [
                "status"        => 200,
                "success"       => true,
                "message"       => "Berhasil Mengambil Data Member",
                "data"          => $data,
            ];
        } else {
            $output     = [
                "status"        => 404,
                "success"       => false,
                "message"       => "Gagal Memuat Data",
                "data"          => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function umhaj_cs_get_data()
    {
        $get_data   = SysUmhajService::get_data_umhaj_cs();

        if(count($get_data) > 0) {
            $output     = [
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Memuat Data CS",
                "data"      => $get_data,
            ];
        } else {
            $output     = [
                "status"    => 404,
                "success"   => false,
                "message"   => "Gagal Memuat Data CS",
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function umhaj_member_get_data_detail(Request $request)
    {
        $send_data  = [
            "cs_name"       => $request->all()['data']['cs_name'] == 'semua' ? '%' : $request->all()['data']['cs_name'],
            "tahun_cari"    => $request->all()['data']['tahun_cari'],
            "bulan_cari"    => $request->all()['data']['bulan_cari']
        ];

        $get_data   = SysUmhajService::get_data_umhaj_member_detail($send_data);

        if(count($get_data) > 0) {
            $output     = [
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Mengambil Data Bulan ".date('F', strtotime($send_data['bulan_cari'])),
                "data"      => $get_data,
            ];
        } else {
            $output     = [
                "status"    => 404,
                "success"   => false,
                "message"   => "Gagal Mengambil Data Bulan ".date('F', strtotime($send_data['bulan_cari'])),
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    // 03 OKTOBER 2024
    // NOTE : AMBIL DATA UMRAH DETAIL
    public function umhaj_umrah_get_data_detail(Request $req)
    {
        $send_data  = [
            "type_umrah"    => $req->all()['data']['jenis'] == 'semua' ? '%' : $req->all()['data']['jenis'],
            "bulan_ke"      => $req->all()['data']['bulan_cari'],
            "tahun_ke"      => $req->all()['data']['tahun_cari']
        ]; 

        $get_data   = SysUmhajService::get_data_umhaj_umrah_detail($send_data);

        if(count($get_data) > 0) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Mengambil Data Umrah Bulan ".$send_data['bulan_ke'],
                "data"      => $get_data,
            ];
        } else {
            $output     = [
                "sucess"    => false,
                "status"    => 404,
                "message"   => "Gagal Mengambil Data Umrah Bulan ".$send_data['bulan_ke'],
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }
}
