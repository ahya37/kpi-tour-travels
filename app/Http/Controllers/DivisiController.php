<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DivisiService;
use App\Services\BaseService;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

use function PHPSTORM_META\map;

class DivisiController extends Controller
{
    // MARKETING
    // IT
    // OPERASIONAL
    public function indexOperasional() {
        $data   = [
            'title'     => 'Divisi Operasional',
            'sub_title' => 'Dashboard - Divisi Operasional',
            'is_active' => '1',
        ];
        return view('divisi/operasional/index', $data);
    }
    
    // 10/06/2024
    // NOTE : PEMBUATAN LIST PROGRAM UMRAH
    public function indexProgram()
    {
        $data   = [
            'title'     => 'Divisi Operasional - Program',
            'sub_title' => 'List Program Umrah',
        ];

        return view('divisi/operasional/program/index', $data);
    }

    public function listJadwalUmrah(Request $request)
    {
        $getData    = DivisiService::getListJadwalUmrah($request->all()['sendData']);

        if(!empty($getData)) {
            for($i = 0; $i < count($getData); $i++) {
                $data[]     = array(
                    $i + 1,
                    $getData[$i]->jdw_program_name,
                    $getData[$i]->jdw_mentor_name,
                    date('d-M-Y', strtotime($getData[$i]->jdw_depature_date)),
                    date('d-M-Y', strtotime($getData[$i]->jdw_arrival_date)),
                    "<button type='button' class='btn btn-sm btn-primary' value='" . $getData[$i]->jdw_id . "' title='Lihat Data' onclick='showModal(`modalForm`, this.value, `edit`)'><i class='fa fa-eye'></i></button>"
                );
            }
        } else {
            $data   =   [];
        }

        $output     = array(
            "draw"  => 1,
            "data"  => $data,
        );

        return $output;
    }

    public function simpanJadwalUmrah(Request $request)
    {
        $doSimpan   = DivisiService::doSimpanJadwalUmrah($request);

        if($doSimpan['status'] == 'berhasil') {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title" => "Berhasil",
                        "text"  => "Berhasil Menambahkan Jadwal Umrah",
                    ], 
                ],
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title" => "Terjadi Kesalahan",
                        "text"  => "Gagal Menambahkan Jadwal Umrah",
                    ], 
                ],
            );
        }
        
        return Response::json($output, $output['status']);
    }

    public function getDataJadwalUmrah(Request $request)
    {
        $getData   = DivisiService::doGetDataJadwalUmrah($request->all()['sendData']);

        if(!empty($getData)) {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "status"    => 404,
                "success"   => false,
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    // 06/10/2024
    // NOTE : PEMBUATAN LIST ATURAN PROGRAM KERJA BULANAN
    public function indexRuleProkerBulanan()
    {
        $data   = [
            'title'     => 'Divisi Operasional - Aturan Program Kerja Bulanan',
            'sub_title' => 'List Aturan Program Kerja Bulanan',
        ];

        return view('divisi/operasional/aturanProgramKerja/index', $data);
    }

    // 11/06/2024
    // NOTE : SIMPAN DATA RULES
    public function simpanDataRules(Request $request, $jenis)
    {
        $doSimpan   = DivisiService::doSimpanDataRules($request, $jenis);

        if($doSimpan['status'] == 'berhasil') {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => "Data Berhasil Disimpan",
                        "errMsg"    => '',
                    ],
                ],
            );
        } else if($doSimpan['status'] == 'gagal') {
            $output     = array(
                "status"    => 500,
                "success"   => false,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => "Data Gagal Disimpan",
                        "errMsg"    => $doSimpan['errMsg'],
                    ],
                ],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function listRules(Request $request) 
    {
        $getData    = DivisiService::doGetListRules($request);

        if(!empty($getData)) {
            for($i = 0; $i < count($getData); $i++) {
                $data[]     = array(
                    $i + 1,
                    $getData[$i]->rul_title,
                    $getData[$i]->rul_duration_day." Hari",
                    "H".$getData[$i]->rul_sla,
                    $getData[$i]->rul_pic_name,
                    "<button type='button' class='btn btn-sm btn-primary' value='" . $getData[$i]->id . "' title='Lihat Data' onclick='showModal(`modalForm`, `edit`, this.value)'><i class='fa fa-eye'></i></button>"
                );
            }
            $output     = array(
                "draw"  => 1,
                "data"  => $data,
            );
        } else {
            $output     = array(
                "draw"  => 1,
                "data"  => [],
            );
        }

        return Response::json($output, 200);
    }

    // 12/06/2024
    // NOTE : PEMBUATAN LIST PROGRAM 
    public function dataTableGenerateJadwalUmrah(Request $request)
    {
        $getData    = DivisiService::getListJadwalUmrah($request->all()['sendData']);

        if(!empty($getData)) {
            for($i = 0; $i < count($getData); $i++) {
                $button_generate    = "<button type='button' class='btn btn-sm btn-success' title='Generate Aturan Program Kerja' data-startdate='".$getData[$i]->jdw_depature_date."' data-enddate='".$getData[$i]->jdw_arrival_date."' value='".$getData[$i]->jdw_id."' onclick='generateRules(this, this.value)'><i class='fa fa-cog'></i></button>";
                // $button_generate    = "<button type='button' class='btn btn-sm btn-success' title='Generate Aturan Program Kerja' value='".$getData[$i]->jdw_id."' onclick='showModal(`modaGenerateRules`, this.value)'><i class='fa fa-cog'></i></button>";
                $button_success     = "<button type='button' class='btn btn-sm btn-primary' title='Lihat Detail' value='" .$getData[$i]->jdw_id. "' onclick='showModal(`modalForm`, this.value)' title='Berhasil Generate'><i class='fa fa-check'></i></button>";
                $button         = $getData[$i]->is_generated == 'f' ? $button_generate : $button_success;
                $data[]     = array(
                    $i + 1,
                    $getData[$i]->jdw_program_name,
                    $getData[$i]->jdw_mentor_name,
                    date('d-M-Y', strtotime($getData[$i]->jdw_depature_date)),
                    date('d-M-Y', strtotime($getData[$i]->jdw_arrival_date)),
                    $button
                );
            }
        } else {
            $data   =   [];
        }

        $output     = array(
            "draw"  => 1,
            "data"  => $data,
        );

        return $output;
    }

    public function generateRules(Request $request)
    {   
        $doGenerate     = DivisiService::doGenerateRules($request);
        if($doGenerate['status'] == 'berhasil') {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => "Berhasil Generate Program Kerja Bulanan",
                        "errMsg"    => "",
                    ],
                ],
            );
        } else {
            $output     = array(
                "status"    => 500,
                "success"   => false,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => "Terjadi Kesalahan pada Sistem, silahkan hubungi admin",
                        "errMsg"    => $doGenerate['errMsg'],
                    ],
                ],
            );
        }

        return Response::json($output, $output['status']);
    }

    // NOTE : PEMBUATAN FUNGSI UNTUK MENGAMBIL DATA RULES
    public function getRulesDetail($rulesID)
    {
        $getData    = DivisiService::doGetRulesDetail($rulesID);

        if(!empty($getData)) {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Mengambil Data",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "status"    => 404,
                "success"   => false,
                "message"   => "Tidak Ada Data",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
        
    }

    // 13/06/2024
    // NOTE : GET DATA DASHBOARD
    public function getDataDashboard($year)
    {
        $getData    = DivisiService::doGetDataDashboard($year);
        if(!empty($getData)) {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Ambil Data",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "status"    => 404,
                "success"   => false,
                "message"   => "Gagal Ambil Data",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function getDataRulesJadwal($idJadwalProgram)
    {
        $currentID      = Auth::user()->id;
        $roleName       = Auth::user()->getRoleNames()[0];
        $subDivision    = !empty(BaseService::doGetCurrentSubDivision($roleName, $currentID)) ? BaseService::doGetCurrentSubDivision($roleName, $currentID)[0]->sub_division_name : '%';

        $getData        = DivisiService::doGetDataRulesJadwal($idJadwalProgram, $subDivision);

        if(!empty($getData))
        {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil",
                "data"      => $getData
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "message"   => "Terjadi Kesalahan",
                "data"      => []
            );
        }

        // if(!empty($getData)) {
        //     for($i = 0; $i < count($getData['jadwal']); $i++) {
        //         $data[$i]     = array(
        //             $i + 1,
        //             $getData['jadwal'][$i]->rules,
        //             date('d-M-Y', strtotime($getData['jadwal'][$i]->start_date_job))." s/d ".date('d-M-Y', strtotime($getData['jadwal'][$i]->end_date_job)),
        //             $getData['jadwal'][$i]->pic_role,
        //             $getData['jadwal'][$i]->duration_day,
        //             !empty($getData['jadwal'][$i]->realization_start_date) ? ($getData['jadwal'][$i]->realization_start_date == $getData['jadwal'][$i]->realization_end_date ? date('d-M-Y', strtotime($getData['jadwal'][$i]->realization_start_date)) : date('d-M-Y', strtotime($getData['jadwal'][$i]->realization_start_date))." s/d ".date('d-M-Y', strtotime($getData['jadwal'][$i]->realization_end_date))) : null,
        //             $getData['jadwal'][$i]->realization_duration_day != 0 ? ($getData['jadwal'][$i]->realization_duration_day <= $getData['jadwal'][$i]->duration_day_num ? "<span class='badge badge-primary d-block' style='font-size: 0.8rem;'>+" .$getData['jadwal'][$i]->realization_duration_day. " Hari</span>" : "<span class='badge badge-danger d-block' style='font-size: 0.8rem;'>-" .$getData['jadwal'][$i]->realization_duration_day. " Hari</span>") : '',
        //         );
        //     }

        //     $output     = array(
        //         "status"    => 200,
        //         "success"   => true,
        //         "message"   => "Berhasil Ambil Data",
        //         "data"      => $data,
        //     );
        // } else {
        //     $output     = array(
        //         "status"    => 404,
        //         "success"   => false,
        //         "message"   => "Gagal Mengambil Data",
        //         "data"      => [],
        //     );
        // }

        return Response::json($output, $output['status']);

    }

    // 26 JUNI 2024
    // NOTE : PENGAMBILAN DATA UNTUK ATURAN JADWAL
    // STATUS : HOLD
    public function getDataRulesJadwalDetail(Request $request)
    {
        $jadwalID   = $request->all()['sendData']['jadwalID'];
        
        $getData    = DivisiService::doGetDataRulesJadwalDetail($jadwalID);
        
        if(!empty($getData['jadwal']) && !empty($getData['jadwal_rules']))
        {
            $dataJadwal     = $getData['jadwal'];
            $dataJadwalRules= $getData['jadwal_rules'];
            
            $data_header    = $dataJadwal;

            $tgl_keberangkatan  = $dataJadwal[0]->jdw_depature_date;
            $tgl_kepulangan     = $dataJadwal[0]->jdw_arrival_date;

            for($i = 0; $i < count($dataJadwalRules); $i++) {
                $rules_seq      = $dataJadwalRules[$i]->jdw_rules_id;
                $rules_title    = $dataJadwalRules[$i]->jdw_rules_title;
                $rules_pic      = $dataJadwalRules[$i]->jdw_rules_sub_division_name;
                $rules_duration_day     = $dataJadwalRules[$i]->jdw_rules_duration_day;
                $rules_deadline_day     = $dataJadwalRules[$i]->jdw_rules_deadline_day;
                $cond_1         = $dataJadwalRules[$i]->jdw_rules_deadline_cond_1;
                $cond_2         = $dataJadwalRules[$i]->jdw_rules_deadline_cond_2;

                $data_detail[]         = array(
                    $rules_title,
                    $rules_duration_day." Hari",
                    "H".$cond_1." ".$rules_deadline_day,
                    "",
                    $rules_pic
                );
            }

            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Ambil Data",
                "data"      => [
                    "header"    => $data_header,
                    "detail"    => $data_detail
                ],
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "message"   => "Gagal Mengambil Data",
                "data"      => [
                    "header"    => [],
                    "detail"    => [],
                ],
            );
        }

        return Response::json($output, $output['status']);
    }

    // MASTER ZONE
    // 11/06/2024
    public function getDataProkerTahunan(Request $request)
    {
        $roleId     = Auth::user()->getRoleNames()[0] == 'admin' ? 'operasional' : Auth::user()->getRoleNames()[0];
        $getData    = DivisiService::doGetDataProkerTahunan($roleId, $request);

        if(!empty($getData)) {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Mengambil Data Program Kerja Tahunan untuk ".$roleId,
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "status"    => 404,
                "success"   => false,
                "message"   => "Tidak Ada Program Kerja Tahunan untuk ".$roleId,
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function getDataSubDivision(Request $request)
    {
        $roleId     = Auth::user()->getRoleNames()[0] == 'admin' ? 'operasional' : Auth::user()->getRoleNames()[0];
        $getData    = DivisiService::doGetDataSubDivision($roleId, $request);
        
        if(!empty($getData)) {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Mengambil Data Sub Divisi untuk ".$roleId,
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "status"    => 404,
                "success"   => false,
                "message"   => "Tidak ada data Sub Divisi ".$roleId,
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    // 1 JULI 2024
    
    public function getDataJobUser()
    {
        $getData    = DivisiService::doGetDataJobUser();

        if(!empty($getData)) {
            $output     = array(
                "status"        => 200,
                "success"       => true,
                "message"       => "Berhasil",
                "data"          => [
                    "chart"     => $getData['chart'],
                    "table"     => $getData['table']
                ],
            );
        } else {
            $output     = array(
                "status"        => 500,
                "success"       => false,
                "message"       => "Terjadi Kesalahan",
                "data"          => [
                    "chart"     => $getData['chart'],
                    "table"     => $getData['table'],
                ]
            );
        }

        return Response::json($output, $output['status']);
    }
}