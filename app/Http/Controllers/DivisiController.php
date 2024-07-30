<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use Illuminate\Http\Request;
use App\Services\DivisiService;
use App\Services\BaseService;
use Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use LDAP\Result;

use function PHPSTORM_META\map;

class DivisiController extends Controller
{
    // MARKETING
    // IT
    // OPERASIONAL
    public function indexOperasional() {
        if(Auth::user()->getRoleNames()[0] == 'operasional') {
            $data   = [
                'title'     => 'Divisi Operasional',
                'sub_title' => 'Dashboard - Divisi Operasional',
                'is_active' => '1',
                'sub_division'      => DivisiService::getCurrentSubDivision()[0]->sub_division_name,
                'sub_division_id'   => DivisiService::getCurrentSubDivision()[0]->sub_division_id,
            ];
            return view('divisi/operasional/index', $data);
        } else {
            abort(404);
        }
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
                $sequence       = $i + 1;
                $tour_code      = $getData[$i]->jdw_tour_code;
                $program_name   = $getData[$i]->jdw_program_name;
                $mentor_name    = $getData[$i]->jdw_mentor_name;
                $dpt_date       = $getData[$i]->jdw_depature_date;
                $arv_date       = $getData[$i]->jdw_arrival_date;
                $status_active  = $getData[$i]->status_active;
                $status_generate= $getData[$i]->status_generated;
                
                if($status_active == "t") {
                    if($status_generate == "t") {
                        $badge  = "<span class='badge badge-pill badge-primary'>Sudah Generated</span>";
                    } else {
                        $badge  = "<span class='badge badge-pill badge-secondary'>Belum Generated</span>";
                    }
                } else {
                    $badge  = "<span class='badge badge-pill badge-danger'>Tidak Aktif</span>";
                }


                $data[]     = array(
                    $sequence,
                    $tour_code,
                    $mentor_name,
                    date('d-M-Y', strtotime($dpt_date)),
                    date('d-M-Y', strtotime($arv_date)),
                    $badge,
                    "<button type='button' class='btn btn-sm btn-primary' value='" . $getData[$i]->jdw_id . "' title='Lihat Data' onclick='showModalV2(`modalFormV2`, this.value, `edit`)'><i class='fa fa-eye'></i></button>"
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
                $button         = $getData[$i]->status_generated == 'f' ? $button_generate : $button_success;
                $data[]     = array(
                    $i + 1,
                    $getData[$i]->jdw_tour_code,
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

    public function hapusProgram($id, Request $request)
    {
        $ip         = $request->ip();
        $doDelete   = DivisiService::doHapusProgram($id, $ip);

        if($doDelete['status'] == 'berhasil') {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => "Berhasil Hapus Data"
                    ],
                ],
                "errMsg"    => [],
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => "Data Tidak Terhapus"
                    ],
                ],
                "errMsg"    => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    // 17 JULI 2024
    // NOTE : AMBIL LIST UNTUK KALENDAR PROGRAM KERJA OPERASIONAL
    public function operasional_programKerja_listDaily(Request $request)
    {
        $sendData   = [
            "start_date"    => $request->all()['sendData']['start_date'],
            "end_date"      => $request->all()['sendData']['end_date'],
            "current_role"  => Auth::user()->getRoleNames()[0],
            "program"       => $request->all()['sendData']['program'],
            "sub_divisi"    => $request->all()['sendData']['sub_divisi'],
            "aktivitas"     => $request->all()['sendData']['aktivitas'],
        ];
        $getData    = DivisiService::getListDailyOperasional($sendData);
        
        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    // 20 JULY 2024
    // NOTE : AMBIL LIST ALL PROKER UNTUK PROGRAM KERJA OPERASIONAL
    public function operasional_programKerja_listProkerAll(Request $request)
    {
        $data       = [
            "current_role"  => Auth::user()->getRoleNames()[0],
            "current_id"    => Auth::user()->id,
        ];
        $getData    = DivisiService::getListProkerAllOperasional($data);

        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Ambil Data",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "message"   => "Gagal Ambil Data",
                "data"      => [],
            );
        }
        
        return Response::json($output, $output['status']);
    }

    public function operasional_programKerja_listPIC()
    {
        $data       = [
            "current_role"  => Auth::user()->getRoleNames()[0],
            "current_id"    => Auth::user()->id,
        ];

        $getData    = DivisiService::getListPIC($data);

        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Ambil Data",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "message"   => "Gagal Ambil Data",
                "data"      => [],
            );
        }
        
        return Response::json($output, $output['status']);
    }

    public function operasional_programKerja_detailCalendarOperasional(Request $request)
    {
        $data   = [
            "pkb_id"        => $request->all()['sendData'],
            "current_role"  => Auth::user()->getRoleNames()[0],
            "current_id"    => Auth::user()->id,
        ];

        $getData        = DivisiService::getDetailCalendarOperasional($data);
        
        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Ambil Data",
                "data"      => $getData[0],
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "message"   => "Gagal Ambil Data",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function operasional_programKerja_simpanJenisPekerjaan(Request $request) 
    {
        $data   = [
            "ip"            => $request->ip(),
            "user_id"       => Auth::user()->id,
            "user_role"     => Auth::user()->getRoleNames()[0],
            "data"          => $request->all()['sendData'],
        ];

        $doSimpan   = DivisiService::doSimpanOperasionalJenisPekerjaan($data);

        if($doSimpan['status'] == 'berhasil') {
            $output     = [
                "status"    => 200,
                "success"   => true,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => $data['data']['jenis'] == 'add' ? "Berhasil Menambahkan Data Baru" : "Berhasil Mengubah Data", 
                    ],
                ],
                "errMsg"    => $doSimpan['errMsg'],
            ];
        } else {
            $output     = [
                "status"    => 500,
                "success"   => false,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => "Gagal Menambahkan Data Baru", 
                    ],
                ],
                "errMsg"    => $doSimpan['errMsg'],
            ];
        }

        return Response::json($output, $output['status']);

        print("<pre>".print_r($data, true)."</pre>");
        // die();
    }
    
    public function operasional_programKerja_listFilter()
    {
        $getData    = DivisiService::getDataListFilter();

        if(count($getData) > 0) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Ambil Data",
                "data"      => $getData,
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 404,
                "message"   => "Gagal Ambil Data",
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function operasional_programKerja_hapusJenisPekerjaan(Request $request)
    {
        $data   = [
            "pkb_id"    => $request->all()['sendData']['id'],
            "ip"        => $request->ip(),
            "user_id"   => Auth::user()->id,
            "user_role" => Auth::user()->getRoleNames()[0]
        ];

        $doHapus= DivisiService::doHapusJenisPekerjaan($data);

        if($doHapus['status'] == 'berhasil') {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => "Data Berhasil Dihapus",
                    ],
                ],
                "errMsg"    => $doHapus['errMsg']
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => "Data Gagal Dihapus",
                    ],
                ],
                "errMsg"    => $doHapus['errMsg']
            );
        }

        return Response::json($output, $output['status']);
    }

    public function operasional_programKerja_listAktivitasProgram(Request $request)
    {
        $data       = [
            "sub_division_id"   => $request->all()['sendData']['sub_division'],
        ];
        $getData    = DivisiService::getListAktivitasProgram($data);

        if(count($getData) > 0) {
            $output     = [
                'status'    => 200,
                'success'   => true,
                'message'   => 'Berhasil',
                'data'      => $getData,
            ];
        } else {
            $output     = [
                "status"    => 404,
                "success"   => true,
                "message"   => "Gagal Ambil Data",
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function generate_with_api(Request $request)
    {
        // GET DATA API
        $api_key    = env('API_PERCIK_KEY');
        $api_url    = env('API_PERCIK');
        $header     = [
            'x-api-key' => $api_key,
        ];
        $options    = [
            'verify'    => false,
            'timeout'   => 60 // 60 DETIK
        ];
        $response   = Http::withHeaders($header)->withOptions($options)->get($api_url.'/umrah/tourcode?year=2024');
        // PRETIER
        // print("<pre>".print_r(json_decode($response), true)."</pre>");die();
        
        $doGenerate     = DivisiService::doGenerateWithAPI(json_decode($response), $request->ip());
        
        echo $doGenerate;
    }
    
    // 24 JULY 2024
    // NOTE : SMPAN DATA V2
    public function simpanJadwalUmrahV2(Request $request)
    {
        $data   = [
            "ip"        => $request->ip(),
            "user_id"   => Auth::user()->id,
            "user_role" => Auth::user()->getRoleNames()[0],
            "data"      => $request->all()['sendData'],
        ];

        $doSimpan   = DivisiService::doSimpanJadwalUmrahV2($data);
        
        if($doSimpan['status'] == 'berhasil') {
            $output     = [
                "status"    => 200,
                "success"   => true,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => $request->all()['sendData']['program_umrah_jenis'] == 'add' ? 'Berhasil Menambahkan Data Program Umrah Baru' : 'Berhasil Merubah Data Program Umrah',
                    ],
                ],
                "errMsg"    => "",
            ];
        } else if($doSimpan['status'] == 'gagal') {
            $output     = [
                "status"    => 500,
                "success"   => false,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => $request->all()['sendData']['program_umrah_jenis'] == 'add' ? 'Gagal Menambahkan Data Program Umrah Baru' : 'Gagal Merubah Data Program Umrah',
                    ],
                ],
                "errMsg"    => $doSimpan['errMsg'],
            ];
        } else if($doSimpan['status'] == 'duplicate') {
            $output     = [
                "status"    => 409,
                "success"   => false,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => $doSimpan['errMsg'],
                    ],
                ],
                "errMsg"    => $doSimpan['errMsg'],
            ];
        }

        return Response::json($output, $output['status']);
    }

    // MODUL FINANCE
    public function indexFinance()
    {
        $data   = [
            'title'         => 'Keuangan - Dashboard',
            'sub_title'     => "Dashboard - Divisi Keuangan"
        ];

        return view('divisi.finance.dashboard.index', $data);
    }

    public function eventsFinance(Request $request) 
    {
        $data   = [
            "sendData"  => $request->all()['sendData'],
            "user_id"   => Auth::user()->id,
            "user_role" => Auth::user()->getRoleNames()[0]
        ];

        $getData = DivisiService::getEventsFinance($data);
        
        if(count($getData) > 0) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Ambil Data Jenis Pekerjaan Finance",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "message"   => "Gagal Ambil Data Jenis Pekerjaan Finance",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function finance_programKerja_tourCode($tourcode)
    {
        // var_dump($tourcode);die();
        $data   = array(
            "tour_code" => $tourcode == 'semua' ? '%' : $tourcode, 
        );

        $getData    = DivisiService::getTourCode($data);

        if(count($getData) > 0) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 200,
                "message"   => "Terjadi Kesalahan",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function finance_programKerja_simpanAktivitas(Request $request, $jenis)
    {
        $data   = array(
            "sendData"  => $request->all()['sendData'],
            "ip"        => $request->ip(),
            "user_id"   => Auth::user()->id,
            "user_role" => Auth::user()->getRoleNames()[0],
            "jenis"     => $jenis
        );

        $doSimpan   = DivisiService::doSimpanAktivitas($data);

        if($doSimpan['status'] == 'berhasil') {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => $jenis == 'add' ? "Berhasil Menambahkan Aktivitas Baru" : "Berhasil Merubah Aktivitas Harian",
                    ],
                ],
                "errMsg"    => $doSimpan['errMsg'],
            );
        } else if($doSimpan['status'] == 'gagal') {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => "Gagal Memproses Data",
                    ],
                ],
                "errMsg"    => $doSimpan['errMsg'],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function finance_programKerja_eventsDetail($eventsID)
    {
        $getData    = DivisiService::doGetEventsFinanceDetail($eventsID);

        if(count($getData) > 0) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Ambil Data",
                "data"      => $getData[0],
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 404,
                "message"   => "Data Gagal Diambil",
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }
}