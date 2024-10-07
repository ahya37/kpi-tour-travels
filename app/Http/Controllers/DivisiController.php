<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DivisiService;
use App\Services\BaseService;
use App\Services\EmployeeService;
use Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Helpers\ResponseFormatter;
use DateInterval;
use DateTime;
use File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
class DivisiController extends Controller
{
    var $title  = "ERP Percik Tours";
    // MARKETING
    // IT
    // OPERASIONAL
    public function indexOperasional() {
        if(Auth::user()->getRoleNames()[0] == 'operasional' || Auth::user()->getRoleNames()[0] == 'admin') {
            $data   = [
                'title'     => 'Divisi Operasional','sub_title' => 'Dashboard - Divisi Operasional',
                'is_active' => '1',
                'sub_division'      => Auth::user()->getRoleNames()[0] != 'admin' ? DivisiService::getCurrentSubDivision()[0]->sub_division_name : 'pic',
                'sub_division_id'   => Auth::user()->getRoleNames()[0] != 'admin' ? DivisiService::getCurrentSubDivision()[0]->sub_division_id : '%',
            ];
            return view('divisi/operasional/index', $data);
        } else {
            // abort(404);
            $data = [
                'title' => 'Halaman Sedang Dalam Pengembangan',
                'sub_title' => 'Halaman Sedang Dalam Pengembangan'
            ];
            return view('maintenance', $data);

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

    public function hapusJadwalProgramByTourCode(Request $request)
    {
        
        try {

            $ip = 'umhaj_'.$request->ip;
            $log_user_id = 'umhaj_'.$request->username;
            $doDelete   = DivisiService::doHapusProgramByTourcode($request->tourcode, $ip, $request->is_active, $log_user_id);
			
            $message   = $request->is_active == 'f' ? 'Berhasil non aktifkan '. $request->tourcode : 'Berhasil aktifkan '. $request->tourcode;
			
            if ($doDelete['status'] == '0') return ResponseFormatter::success(null,  $message.', namun tourcode belum tersedia di ERP'); 

            return ResponseFormatter::success($doDelete,$message); 

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error(null,'Gagal non aktifkan tourcode !');
        }
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
            "current_role"  => Auth::user()->getRoleNames()[0] == 'admin' ? 'operasional' : Auth::user()->getRoleNames()[0],
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
            "current_role"  => Auth::user()->getRoleNames()[0] == 'admin' ? 'marketing' : Auth::user()->getRoleNames()[0],
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
            'title'         => $this->title." | Dashboard Keuangan",
            'sub_title'     => "Dashboard - Divisi Keuangan"
        ];

        return view('divisi.finance.dashboard.index', $data);
    }

    public function eventsFinance(Request $request) 
    {
        $data   = [
            "sendData"  => $request->all()['sendData'],
            "user_id"   => Auth::user()->getRoleNames()[0] == 'admin' ? '%' : Auth::user()->id,
            "user_role" => Auth::user()->getRoleNames()[0] == 'admin' ? 'finance' : Auth::user()->getRoleNames()[0]
        ];

        $getData = DivisiService::getEventsFinance($data);
        
        $output     = array(
            "success"   => true,
            "status"    => 200,
            "message"   => "Berhasil Ambil Data Jenis Pekerjaan Finance",
            "data"      => $getData,
        );

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

    // DIGITAL
    public function digital_programKerja_index()
    {
        if(Auth::user()->getRoleNames()[0] == 'digital') {
            $data   = [
                "title"     => "Aktivitas Harian - Digital",
                "sub_title" => "List Aktivitas Harian",
            ];

            return view('divisi/digital/aktivitas_harian/index', $data);
        } else {
            abort(404);
        }
    }

    public function digital_programKerja_listEvents(Request $request)
    {
        $data   = [
            "user_id"       => Auth::user()->id,
            "start_date"    => $request->all()['start_date'],
            "end_date"      => $request->all()['end_date'],
        ];

        $getData    = DivisiService::getListEventsDigital($data);

        if(count($getData) > 0) {
            $output     = [
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Ambil Data Kalendar", 
                "data"      => $getData,
            ];
        } else {
            $output     = [
                "status"    => 500,
                "success"   => false,
                "message"   => "Gagal Ambil Data Kalendar", 
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function digital_programKerja_listEventDetail(Request $request)
    {
        $data   = [
            "id"        => $request->all()['id'],
            "user_id"   => Auth::user()->id,
        ];

        $getData    = DivisiService::getListEventDigitalDetail($data);
        
        if(count($getData) > 0) {
            $output     = [
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Ambil Data",
                "data"      => $getData,
            ];
        } else {
            $output     = [
                "status"    => 404,
                "success"   => false,
                "message"   => "Gagal Ambil Data",
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function digital_programKerja_listProgram(Request $request)
    {
        $data   = [
            "today"     => $request->all()['today'],
            "user_id"   => Auth::user()->getRoleNames()[0] != 'admin' ? Auth::user()->id : '%',
        ];

        $getData    = DivisiService::getListProgramDigital($data);

        if(count($getData['header']) > 0) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Mengambil Data",
                "data"      => [
                    "header"    => $getData['header'],
                    "detail"    => $getData['detail'],
                ],
            );
        } else {
            $output     = [
                "success"   => false,
                "status"    => 404,
                "message"   => "Gagal Mengambil Data",
                "data"      => [
                    "header"    => [],
                    "detail"    => [], 
                ],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function digital_programKerja_simpanAktivitasHarian($jenis, Request $request)
    {
        $data_simpan    = [
            "data"      => $request->all(),
            "jenis"     => $jenis,
            "user_id"   => Auth::user()->id,
            "user_role" => Auth::user()->getRoleNames()[0],
            "ip"        => $request->ip()
        ];

        $doSimpan   = DivisiService::doSimpanAktivitasHarianDigital($data_simpan);
        
        if($doSimpan['status'] == 'berhasil') {
            $output    = [
                "success"   => true,
                "status"    => 200,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => $jenis == 'add' ? "Berhasil Menyimpan Data Aktivitas Baru" : ($jenis != 'delete' ? "Berhasil Mengubah Data Aktivitas" : "Berhasil Menghapus Data Aktivitas"),
                    ],
                ],
            ];
        } else if($doSimpan['status'] == 'gagal') {
            $output    = [
                "success"   => true,
                "status"    => 200,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => $jenis == 'add' ? "Berhasil Menyimpan Data Aktivitas Baru" : ($jenis != 'delete' ? "Berhasil Mengubah Data Aktivitas" : "Berhasil Menghapus Data Aktivitas"),
                    ],
                ],
            ];
        }

        return Response::json($output, $output['status']);
    }
    
    // 12 AGUSTUS 2024
    // NOTE : PENGAMBILAN RKAP UNTUK OPERASIONAL
    public function divisi_operasional_getRKAP(Request $request)
    {
        $sendData   = [
            "pkt_id"        => $request->all()['sendData']['pkt_id'],
            "current_year"  => date('Y'),
            "current_role"  => Auth::user()->getRoleNames()[0] == 'admin' ? 'operasional' : Auth::user()->getRoleNames()[0]
        ];

        $getData    = DivisiService::doGetRKAPOperasional($sendData);

        $output     = [
            "success"   => true,
            "status"    => 200,
            "message"   => "Berhasil Ambil Data",
            "data"      => $getData,
        ];

        return Response::json($output, $output['status']);
    }

    // 13 AGUSTUS 2024
    // NOTE : AMBIL DATA LIST AKTIVITAS USER DIVISI DIGITAL
    public function digital_programKerja_listAktivitasHarian(Request $request)
    {
        $sendData   = [
            "user_id"           => Auth::user()->getRoleNames()[0] == 'admin' ? '%' : Auth::user()->id,
            "selected_month"    => $request->all()['today'],
        ];
        
        $getData    = DivisiService::getListAktivitasHarian($sendData);

        $output     = [
            "success"   => true,
            "status"    => 200,
            "message"   => "Berhasil Mengambil Data",
            "data"      => $getData,
        ];

        return Response::json($output, $output['status']);
    }

    public function operasional_dahsboard_actDetailUserChart(Request $request)
    {
        $sendData   = [
            "user_name" => $request->all()['sendData']['user_name'],
        ];

        $getData    = DivisiService::doGetDataActUserChart($sendData);
        
        $output     = [
            "success"   => true,
            "status"    => 200,
            "message"   => "Data Berhasil Dimuat",
            "data"      => $getData,
        ];

        return Response::json($output, $output['status']);
    }

    // 15 AGUSTUS 2024
    // NOTE : LIST RKAP FINANCE
    public function finance_rkap_list(Request $request)
    {
        $sendData   = [
            "user_role"     => Auth::user()->getRoleNames()[0] == 'admin' ? 'finance' : Auth::user()->getRoleNames()[0],
            "rkap_id"       => $request->all()['sendData']['rkap_id'],
        ];

        $getData    = DivisiService::getFinanceRKAPList($sendData);

        $output     = [
            "success"   => true,
            "status"    => 200,
            "message"   => "Berhasil Memuat Data RKAP Finance",
            "data"      => $getData,
        ];

        return Response::json($output, $output['status']);
    }

    // 16 AGUSTUS 2024
    // NOTE : SIMPAN RKAP FINANCE
    public function finance_rkap_simpan($jenis, Request $request)
    {
        $sendData   = [
            "data"      => $request->all()['sendData'],
            "jenis"     => $jenis,
            "user_id"   => Auth::user()->id,
            "user_role" => Auth::user()->getRoleNames()[0] == 'admin' ? 'finance' : Auth::user()->getRoleNames()[0],
            "ip"        => $request->ip(),
        ];

        $doSimpan   = DivisiService::doSimpanRKAPFinance($sendData);
        
        switch($doSimpan['status'])
        {
            case "berhasil" :
                $output     = [
                    "success"   => true,
                    "status"    => 200,
                    "alert"     => [
                        "icon"      => "success",
                        "message"   => [
                            "title"     => "Berhasil",
                            "text"      => $jenis == 'add' ? "Berhasil Menambahkan RKAP Baru" : "Berhasil Merubah Data RKAP",
                        ],
                    ],
                    "errMsg"    => $doSimpan['errMsg'],
                ];
                break;
            case "gagal" :
                $output     = [
                    "success"   => false,
                    "status"    => 500,
                    "alert"     => [
                        "icon"      => "error",
                        "message"   => [
                            "title"     => "Terjadi Kesalahan",
                            "text"      => $jenis == 'add' ? "Gagal Menambahkan RKAP Baru" : "Gagal Merubah Data RKAP",
                        ],
                    ],
                    "errMsg"    => $doSimpan['errMsg'],
                ];
                break;
        }

        return Response::json($output, $output['status']);
    }

    public function finance_rkap_getData(Request $request)
    {
        $sendData   = [
            "rkap_id"   => $request->all()['sendData']['rkap_id'],
        ];

        $getData    = DivisiService::doGetDataRKAP($sendData);

        if((!empty($getData['header'])) && (count($getData['detail']) > 0)) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Memuat Data",
                "data"      => $getData,
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 404,
                "message"   => "Gagal Memuat Data : Data Tidak Ditemukan",
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    // 21 AGUSTUS 2024
    // NOTE : PEMBUATAN MODUL HR
    public function indexHR()
    {
        $data   = [
            "user_id"   => Auth::user()->id,
            "title"     => $this->title." | Dashboard",
            "sub_title" => "Dashboard - Divisi Human Resource",
        ];

        return view("/divisi/human_resource/dashboard/index", $data);
    }

    // 22 AGUSTUS 2024
    // NOTE : FORM PENGAJUAN CUTI
    public function pengajuan_cuti()
    {
        $data   = [
            "user_id"   => Auth::user()->id,
            "user_name" => Auth::user()->name,
            "user_role" => Auth::user()->getRoleNames()[0],
            "title"     => $this->title." | Pengajuan",
            "sub_title" => "Pengajuan Cuti / Izin / Tidak Masuk Kerja",
        ];
        
        return view('activities.pengajuan.cuti.index', $data);
    }

    public function pengajuan_list_cuti()
    {
        $data   = [
            "user_id"       => Auth::user()->getRoleNames()[0] == 'admin' ? '%' : Auth::user()->id,
            "current_year"  => date('Y'),
        ];

        $getData    = DivisiService::getListPengajuan($data);

        $output     = [
            "success"   => true,
            "status"    => 200,
            "message"   => "Data Berhasil Dimuat",
            "data"      => $getData,
        ];

        return Response::json($output, $output['status']);
    }

    public function pengajuan_simpan_cuti(Request $request)
    {
        $sendData   = [
            "user_id"   => Auth::user()->id,
            "data"      => $request->all(),
            "ip"        => $request->ip()
        ];

        $doSimpan   = DivisiService::doSimpanPengajuanCuti($sendData);
        
        if($doSimpan['status'] == 'berhasil') {
            $output     = [
                "status"    => 200,
                "success"   => true,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => $request->all()['pgj_status'] == "3" ? "Berhasil Membuat Pengajuan ".$request->all()['pgj_type'] : "Berhasil Konfirmasi Pengajuan ".$request->all()['pgj_type']
                    ],
                ],
            ];
        } else if($doSimpan['status'] == 'gagal') {
            $output     = [
                "status"    => 500,
                "success"   => true,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => $request->all()['pgj_status'] == "3" ? "Gagal Membuat Pengajuan ".$request->all()['pgj_type'] : "Gagal Konfirmasi Pengajuan ".$request->all()['pgj_type']
                    ],
                ],
            ];
        }
        
        return Response::json($output, $output['status']);
    }

    // 26 AGUSTUS 2024
    // NOTE : LIST ABSENSI
    public function absensi_list(Request $request)
    {
        $sendData   = [
            "data"  => $request->all(),
        ];

        $getData    = DivisiService::getListAbsensi($sendData);

        $output     = [
            "success"   => true,
            "status"    => 200,
            "message"   => "Berhasil Memuat Data",
            "data"      => $getData,
        ];

        return Response::json($output, $output['status']);
    }

    private function getDiffTime($day, $time_in, $time_out)
    {
        // ABSEN AWAL
        $tanggal_1  = new DateTime("2024-07-15");
        $tanggal_2  = new DateTime($day);
        switch(date('D', strtotime($day)))
        {
            case "Sat" :
                if($tanggal_1 < $tanggal_2) {
                    $jam_masuk  = $day." 08:00:00";
                    $jam_keluar = $day." 13:30:00";
                } else {
                    $jam_masuk  = $day." 08:30:00";
                    $jam_keluar = $day." 12:00:00";
                }

                $jam_masuk_abs  = $day." ".$time_in;
                $jam_keluar_abs = $day." ".$time_out;
            break;
            case "Sun" : 
                $jam_masuk  = $day." 00:00:00";
                $jam_keluar = $day." 00:00:00";
                
                $jam_masuk_abs  = $day." ".$time_in;
                $jam_keluar_abs = $day." ".$time_out;
            break;
            default  : 
            if($tanggal_1 < $tanggal_2) {
                $jam_masuk  = $day." 08:00:00";
                $jam_keluar = $day." 16:00:00";
            } else {
                $jam_masuk  = $day." 08:30:00";
                $jam_keluar = $day." 16:30:00";
            }

                $jam_masuk_abs  = $day." ".$time_in;
                $jam_keluar_abs = $day." ".$time_out;
        }

        // GET PERBEDAAN WAKTU
        // KETERLAMBATAN
        $waktu_masuk_1  = new DateTime($jam_masuk);
        $waktu_masuk_2  = new DateTime($jam_masuk_abs);

        $interval_masuk     = $waktu_masuk_1->diff($waktu_masuk_2);
        $total_jam_masuk    = $interval_masuk->h < 10 ? "0".$interval_masuk->h : $interval_masuk->h;
        $total_min_masuk    = $interval_masuk->i < 10 ? "0".$interval_masuk->i : $interval_masuk->i;
        $total_sec_masuk    = $interval_masuk->s < 10 ? "0".$interval_masuk->s : $interval_masuk->s;

        $total_telat_masuk  = $total_jam_masuk.":".$total_min_masuk.":".$total_sec_masuk;
        
        // LEBIH JAM
        $waktu_keluar_1 = new DateTime($jam_keluar);
        $waktu_keluar_2 = new DateTime($jam_keluar_abs);

        $interval_keluar        = $waktu_keluar_1->diff($waktu_keluar_2);
        $total_jam_keluar       = $interval_keluar->h < 10 ? "0".$interval_keluar->h : $interval_keluar->h;
        $total_min_keluar       = $interval_keluar->i < 10 ? "0".$interval_keluar->i : $interval_keluar->i;
        $total_sec_keluar       = $interval_keluar->s < 10 ? "0".$interval_keluar->s : $interval_keluar->s;
        
        $total_lebih_waktu  = $total_jam_keluar.":".$total_min_keluar.":".$total_sec_keluar;

        return $data    = [
            "kurang_jam"    => $total_telat_masuk,
            "lebih_jam"     => $total_lebih_waktu,
        ];
    }

    public function absensi_download_excel(Request $request)
    {
        $tgl_awal   = $request->all()['tanggal_awal'];
        $tgl_akhir  = $request->all()['tanggal_akhir'];
        $jml_hari   = $request->all()['jml_hari'];
        // $getData    = DivisiService::getListAbsensi($sendData);
        
        // AMBIL DATA USER
        $getDataUser    = DivisiService::getDataEmployee();
        
        $spreadsheet    = new Spreadsheet;

        function autoSizeColumn(Worksheet $sheet, $column)
        {
            $maxLength = 0;
            $columnIndex = Coordinate::columnIndexFromString($column); // Mendapatkan indeks kolom dari huruf kolom
        
            // Iterasi melalui semua baris dalam kolom
            foreach ($sheet->getRowIterator() as $row) {
                $cell = $sheet->getCell($column . $row->getRowIndex());
                $value = $cell->getValue();
                $length = strlen((string) $value); // Konversi nilai sel ke string
        
                // Periksa panjang sel untuk menentukan lebar kolom maksimum
                if ($length > $maxLength) {
                    $maxLength = $length;
                }
            }
        
            // Set lebar kolom
            $sheet->getColumnDimension($column)->setWidth($maxLength + 2); // Menambahkan sedikit ruang ekstra
        }
        for($i = 0; $i < count($getDataUser); $i++)
        {
            $emp_data   = $getDataUser[$i];
            $emp_id     = $emp_data->emp_id;
            $emp_name   = $emp_data->emp_name;
            $curr_seq   = $i + 1;

            $sendData   = [
                "data"  => [
                    "tanggal_awal"  => $tgl_awal,
                    "tanggal_akhir" => $tgl_akhir,
                    "user_id"       => $emp_id,
                    "jml_hari"      => $jml_hari,
                ],
            ];

            $getDataDetail  = DivisiService::getListAbsensi($sendData);

            // STYLING
            $sheetStyle     = [
                "font"  => [
                    "bold"  => true
                ],
            ];

            // WAKTU TELAT
            $batas_jam_telat    = new DateTime('08:10:59');

            // UNTUK PERHITUNGAN TOTAL JAM
            $total_kurang_jam   = new DateTime("1970-01-01 00:00:00");
            $total_lebih_jam    = new DateTime("1970-01-01 00:00:00");

            // KETIKA SEQ 1 MAKA JADI ACTIVE SHEET JIKA TIDAK MAKA BUAT SHEET BARU
            if($curr_seq == 1) {
                $sheet1     = $spreadsheet->getActiveSheet();
            } else {
                $sheet1     = $spreadsheet->createSheet();
            }

            $sheet1->getStyle("A1:F1")->applyFromArray($sheetStyle);
            $sheet1->setTitle($emp_name);
            $sheet1->setCellValue('A1', 'TANGGAL');
            $sheet1->setCellValue('B1', 'JAM DATANG');
            $sheet1->setCellValue('C1', 'JAM PULANG');
            $sheet1->setCellValue('D1', 'KURANG JAM');
            $sheet1->setCellValue('E1', 'LEBIH JAM');
            $sheet1->setCellValue('F1', 'TOTAL JAM');
            
            for($j = 0; $j < count($getDataDetail);$j++)
            {
                $tgl_absen      = $getDataDetail[$j]['tanggal_absen'];
                $waktu_masuk    = $getDataDetail[$j]['jam_masuk'];
                $waktu_keluar   = $getDataDetail[$j]['jam_keluar'];
                $lebih_jam      = $waktu_masuk != '00:00:00' ? $this->getDiffTime($tgl_absen, $waktu_masuk, $waktu_keluar)['lebih_jam'] : "00:00:00";
                $kurang_jam     = $waktu_masuk != '00:00:00' ? $this->getDiffTime($tgl_absen, $waktu_masuk, $waktu_keluar)['kurang_jam'] : "00:00:00";

                // HITUNG LEBIH KURANG JAM
                $kurang_jam_jam     = explode(':', $kurang_jam)[0];
                $kurang_jam_min     = explode(':', $kurang_jam)[1];
                $kurang_jam_sec     = explode(':', $kurang_jam)[2];
                $total_kurang_jam->add(new DateInterval('PT'.$kurang_jam_jam.'H'.$kurang_jam_min.'M'.$kurang_jam_sec.'S'));

                $lebih_jam_jam      = explode(':', $lebih_jam)[0];
                $lebih_jam_min      = explode(':', $lebih_jam)[1];
                $lebih_jam_sec      = explode(':', $lebih_jam)[2];
                $total_lebih_jam->add(new DateInterval('PT'.$lebih_jam_jam.'H'.$lebih_jam_min.'M'.$lebih_jam_sec.'S'));

                // TOTAL JAM
                $jam_kerja_masuk    = new DateTime($waktu_masuk);
                $jam_kerja_keluar   = new DateTime($waktu_keluar);
                $interval_jam_masuk_jam_keluar  = $jam_kerja_masuk->diff($jam_kerja_keluar);
                $total_diff_jam     = $interval_jam_masuk_jam_keluar->h;
                $total_diff_menit   = $interval_jam_masuk_jam_keluar->i;
                $total_difF_detik   = $interval_jam_masuk_jam_keluar->s;
                $total_jam_kerja    = sprintf('%02d:%02d:%02d', $total_diff_jam, $total_diff_menit, $total_difF_detik);

                if(new DateTime($waktu_masuk) > $batas_jam_telat) {
                    $telat_style    = [
                        'font'  => [
                            'color' => [
                                'argb'  => 'FFFF0000',
                            ]
                        ],
                    ];
                    $sheet1->getStyle('B'.($j + 2))->applyFromArray($telat_style);
                    $sheet1->getComment('B'.($j + 2))->getText()->createTextRun('Telat');
                } else if($waktu_masuk == '00:00:00') {
                    // NOTICE SAJA
                    $sheet1->getComment('b'.($j + 2))->getText()->createTextRun('Tidak ada Absen');
                }

                $sheet1->setCellValue('A'.($j + 2), $tgl_absen);
                $sheet1->setCellValue('B'.($j + 2), $waktu_masuk);
                $sheet1->setCellValue('C'.($j + 2), $waktu_keluar);
                $sheet1->setCellValue('D'.($j + 2), $kurang_jam);
                $sheet1->setCellValue('E'.($j + 2), $lebih_jam);
                $sheet1->setCellValue('F'.($j + 2), $total_jam_kerja);
            }
            // FOOTER
            $total_data     = count($getDataDetail) + 2;
            $sheet1->getStyle('A'.$total_data.":F".$total_data)->applyFromArray($sheetStyle);
            $sheet1->setCellValue('A'.$total_data, 'JUMLAH');
            $sheet1->setCellValue('D'.$total_data, $total_kurang_jam->format('H:i:s'));
            $sheet1->setCellValue('E'.$total_data, $total_lebih_jam->format('H:i:s'));
            $spreadsheet->setActiveSheetIndex(0);

            autoSizeColumn($sheet1, 'A');
            autoSizeColumn($sheet1, 'B');
            autoSizeColumn($sheet1, 'C');
            autoSizeColumn($sheet1, 'D');
            autoSizeColumn($sheet1, 'E');
            autoSizeColumn($sheet1, 'F');
        }

        // Simpan file Excel ke disk
        $file_path      = public_path('storage/data-files/presensi_xls/');
        $file_name      = time()."_Laporan_Presensi_".$tgl_awal."_sampai_".$tgl_akhir.".xlsx";

        if(!File::exists($file_path)) {
            File::makeDirectory($file_path, 0755, true);
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($file_path.$file_name);

        $output     = [
            "status"    => 200,
            "success"   => true,
            "data"      => [
                "file_url"  => "storage/data-files/presensi_xls",
                "file_name" => $file_name,
            ],
            "message"   => "Berhasil Memuat Data",
        ];

        return Response::json($output, $output['status']);
        
    }

    public function absensi_delete_excel(Request $request)
    {
        $file_url   = $request->all()['file_url'];
        
        if(file_exists($file_url))
        {
            unlink($file_url);
            $output     = [
                "status"    => 200,
                "message"   => "Berhasil Hapus File"
            ];
        } else {
            $output     = [
                "status"    => 500,
                "message"   => "Gagal Hapus File",
            ];
        }

        return Response::json($output, $output['status']);
    }
    
    public function hr_list_employee(Request $request)
    {
        // GET DATA
        $emp_send_data  = $request->all()['cari'];
        $emp_get_data   = EmployeeService::getDataEmployee($emp_send_data);
        $emp_data       = [];

        if(count($emp_get_data) > 0) {
            foreach($emp_get_data as $emp)
            {
                $emp_data[]     = [
                    "emp_id"        => $emp->employee_id,
                    "emp_name"      => $emp->employee_name,
                    "emp_role"      => $emp->role_name,
                    "emp_division"  => $emp->group_division_name." (".$emp->sub_division_name.")",
                    "emp_is_active" => $emp->user_active
                ];
            }
        } else {
            $emp_data   = [];
        }

        $output     = [
            "success"   => true,
            "status"    => 200,
            "message"   => "Berhasil Mengambil Data Karyawan",
            "data"      => $emp_data,
            "total_data"=> count($emp_data)
        ];

        return Response::json($output, $output['status']);
    }

    public function hr_ubah_status_employee(Request $request)
    {
        $sendData   = [
            "emp_id"        => $request->all()['emp_id'],
            "emp_status"    => $request->all()['emp_status'] == 'active' ? '0' : '1',
            "ip"            => $request->ip(),
        ];

        $doSimpan   = EmployeeService::do_ubah_status_employee($sendData);

        if($doSimpan['status'] == 'berhasil') {
            $output     = [
                "status"    => 200,
                "success"   => true,
                "alert"     => [
                    "icon"  => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => "Berhasil Mengubah Status User",
                    ],
                ],
                "errMsg"    => $doSimpan['errMsg'],
            ];
        } else {
            $output     = [
                "status"    => 400,
                "success"   => false,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => "Sistem Sedang Gangguan, Silahkan Coba Lagi..",
                    ],
                ],
            ];
        }

        return Response::json($output, $output['status']);
    }


    // 11-09-2024
    // NOTE : AMBIL DATA GAJI POKO
    public function finance_master_employees_fee()
    {
        $get_data   = DivisiService::get_master_employees_fee();

        if(count($get_data) > 0) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Memuat Data",
                "total_data"=> count($get_data),
                "data"      => $get_data,
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 404,
                "message"   => "Data Gagal Dimuat",
                "total_data"=> 0,
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function finance_master_employees_fee_update($emp_id, Request $request)
    {
        $send_data  = [
            "emp_id"    => $emp_id,
            "emp_fee"   => $request->all()['emp_fee'],
            "user_id"   => Auth::user()->id,
            "ip"        => $request->ip(),
        ];

        $do_update  = DivisiService::do_update_employees_fee($send_data);

        if($do_update['status'] == 'berhasil') {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => "Berhasil Mengubah Data Gaji Pokok Karyawan"
                    ],
                ],
                "error_message" => $do_update['errMsg'],
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 500,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => "Gagal Mengubah Data Gaji Pokok Karyawan"
                    ],
                ],
                "error_message" => $do_update['errMsg']
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function index_simulasi_perhitungan_lembur()
    {
        $data       = [
            "title"     => $this->title . " | Simulasi",
            "sub_title" => "Pengajuan Perhitungan Lemburan"
        ];

        return view('simulasi.perhitungan.lembur.index', $data);
    }

    public function pengajuan_lembur()
    {
        // DATA EMPLOYEES
        $emp_id                 = Auth::user()->id;
        $get_data_employees     = DivisiService::getDataEmployee();
        $data_emp               = [];
        for($i = 0; $i < count($get_data_employees); $i++) {
            if($get_data_employees[$i]->emp_id == $emp_id)
            {
                $data_emp   = [
                    "emp_id"    => $emp_id,
                    "emp_name"  => $get_data_employees[$i]->emp_name,
                    "emp_divisi"=> $get_data_employees[$i]->emp_divisi,
                ];
                break;
            } else {
                $data_emp   = [
                    "emp_id"    => $emp_id,
                    "emp_name"  => Auth::user()->name,
                    "emp_divisi"=> "%",
                ];
            }
        }
        $data       = [
            "title"     => $this->title . " | Pengajuan Lembur",
            "sub_title" => "List Pengajuan Lembur",
            "data_emp"  => $data_emp,  
        ];

        return view('activities.pengajuan.lembur.index', $data);
    }

    public function list_lembur()
    {
        $get_data   = DivisiService::get_list_lembur();

        $output     = [
            "success"   => true,
            "status"    => 200,
            "message"   => "Berhasil Mengambil Data Lemburan",
            "data"      => $get_data,
        ];

        return Response::json($output, $output['status']);
    }

    public function simpan_pengajuan_lembur($jenis, Request $request)
    {
        $data   = [
            "user_id"   => Auth::user()->id, 
            "user_name" => Auth::user()->name,
            "data"      => [
                "header"    => $request->all()['header'],
                "detail"    => $request->all()['detail'],
            ],
            "jenis"     => $jenis,
            "ip"        => $request->ip(),
        ];

        $doSimpan   = DivisiService::do_simpan_pengajuan_lembur($data);

        if($doSimpan['status'] == 'berhasil') {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => $jenis == 'add' ? "Berhasil Menambahkan Pengajuan Lembur" : "Berhasil Mengubah Pengajuan Lembur",
                    ],
                ],
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 401,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => $jenis == 'add' ? "Gagal Menambahkan Pengajuan Lembur" : "Gagal Mengubah Pengajuan Lembur",
                    ],
                ],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function get_data_lembur(Request $request)
    {
        $user_act_id    = $request->all()['lmb_id'];

        $get_data       = DivisiService::do_get_data_lembur($user_act_id);

        if(count($get_data['header']) > 0) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Mengambil Data Lemburan",
                "data"      => $get_data,
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

    public function konfirmasi_data_lembur(Request $request)
    {
        $data   = [
            "emp_act_id"    => $request->all()['emp_act_id'],
            "emp_act_status"=> $request->all()['emp_act_status'],
            "emp_user_id"   => Auth::user()->id,
            "ip"            => $request->ip(),
        ];
        
        $do_simpan  = DivisiService::do_simpan_konfirmasi_data_lembur($data);

        if($do_simpan['status'] == 'berhasil') {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => "Berhasil Konfirmasi Pengajuan Lembur"
                    ],
                ],
            ];
        } else if($do_simpan['status'] == 'gagal') {
            $output     = [
                "success"   => false,
                "status"    => 500,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => "Gagal Konfirmasi Pengajuan Lembur"
                    ],
                ],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function finance_sim_employees_fee(Request $request)
    {
        $get_data   = DivisiService::get_data_finance_sim_employees_fee($request->all());
        
        if(count($get_data['header']) > 0) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Ambil Data",
                "data"      => $get_data,
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

    public function finance_sim_employees_fee_download(Request $request)
    {
        $get_data_employee  = DivisiService::get_data_employee_all();
        $get_data           = DivisiService::get_data_finance_sim_employees_fee($request->all());

        $spreadsheet    = new Spreadsheet;
        
        function autoSizeColumn(Worksheet $sheet, $column)
        {
            $maxLength = 0;
            $columnIndex = Coordinate::columnIndexFromString($column); // Mendapatkan indeks kolom dari huruf kolom
        
            // Iterasi melalui semua baris dalam kolom
            foreach ($sheet->getRowIterator() as $row) {
                $cell = $sheet->getCell($column . $row->getRowIndex());
                $value = $cell->getValue();
                $length = strlen((string) $value); // Konversi nilai sel ke string
        
                // Periksa panjang sel untuk menentukan lebar kolom maksimum
                if ($length > $maxLength) {
                    $maxLength = $length;
                }
            }
        
            // Set lebar kolom
            $sheet->getColumnDimension($column)->setWidth($maxLength + 3); // Menambahkan sedikit ruang ekstra
        }

        function getDayName($day)
        {
            switch($day) {
                case "Mon" :
                    $hari   = "Senin";
                break;
                case "Tue" : 
                    $hari   = "Selasa";
                break;
                case "Wed" :
                    $hari   = "Rabu";
                break;
                case "Thu"  : 
                    $hari   = "Kamis";
                break;
                case "Fri"  : 
                    $hari   = "Jumat";
                break;
                case "Sat"  :
                    $hari   = "Sabtu";
                break;
                case "Sun"  :
                    $hari   = "Minggu";
                break;
            }

            return $hari;
        }

        function getTime($day)
        {
            switch($day)
            {
                case "Sat" :
                    $jam_masuk  = "08:00:00";
                    $jam_keluar = "13:30:00";
                    $batas_ot_1 = "14:30:00";
                    $batas_ot_2 = "23:59:59";
                break;
                case "Sun" : 
                    $jam_masuk  = "00:00:00";
                    $jam_keluar = "23:59:00";
                    $batas_ot_1 = "00:00:00";
                    $batas_ot_2 = "00:00:00";
                break;
                default : 
                    $jam_masuk  = "08:00:00";
                    $jam_keluar = "16:00:00";
                    $batas_ot_1 = "17:00:00";
                    $batas_ot_2 = "23:59:00";
            }

            $data   = [
                "jam_masuk"     => $jam_masuk,
                "jam_keluar"    => $jam_keluar,
                "batas_ot_1"    => $batas_ot_1,
                "batas_ot_2"    => $batas_ot_2,
            ];

            return $data;
        }
        
        if(count($get_data_employee) > 0) {
            for($x = 0; $x < count($get_data_employee); $x++) {
                $emp_id     = $get_data_employee[$x]->emp_id;
                $emp_name   = $get_data_employee[$x]->emp_name;
    
                $curr_seq   = $x + 1;
    
                $send_data  = [
                    "emp_id"    => $emp_id,
                    "date_start"=> $request->all()['date_start'],
                    "date_end"  => $request->all()['date_end'],
                ];
    
                $get_data   = DivisiService::get_data_finance_sim_employees_fee($send_data);
    
                if(count($get_data['header']) > 0) {
                    $header     = $get_data['header'][0];
                    $detail     = $get_data['detail'];
        
                    $employee_fee       = $header->emp_fee;
                    $employee_division  = $header->emp_division;
                    $column_start       = 2;
        
                    $ot_1                   = 0;
                    $ot_2                   = 0;
                    $ot_3                   = 0;
                    $total_ot_1             = 0;
                    $total_ot_2             = 0;
                    $total_ot_3             = 0;
        
                    $sheetStyle = [
                        "font"  => [
                            "bold"  => true,
                        ],
                    ];
        
                    $sheetStyleBorder    = [
                        "borders"   => [
                            "allBorders"    => [
                                "borderStyle"   => Border::BORDER_THIN,
                                "color"         => ['argb'  => '000']
                            ],
                        ],
                    ];
        
                    if($curr_seq == 1) {
                        $sheet1     = $spreadsheet->getActiveSheet();
                    } else {
                        $sheet1     = $spreadsheet->createSheet();
                    }
        
                    // TABLE LAPORAN ABSENSI
                    $sheet1->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet1->getStyle('B1:E1')->applyFromArray($sheetStyleBorder);
                    $sheet1->getStyle('B1:E1')->applyFromArray($sheetStyle);
        
                    $sheet1->getStyle('B2:E2')->applyFromArray($sheetStyle);
                    $sheet1->getStyle('B2:E2')->applyFromArray($sheetStyleBorder);
                    $sheet1->getStyle('B2:E2')->getAlignment()->setHorizontal('center');
        
                    $sheet1->setTitle($emp_name);
                    $sheet1->setCellValue('B1', 'LAPORAN ABSENSI');
                    $sheet1->mergeCells('B1:E1');
                    $sheet1->setCellValue('B2', 'NAMA');
                    $sheet1->setCellValue('C2', 'TANGGAL');
                    $sheet1->setCellValue('D2', 'JAM MASUK');
                    $sheet1->setCellValue('E2', 'JAM KELUAR');
        
                    // TABLE LAPORAN LEMBURAN
                    $sheet1->setCellValue('G1', 'LAPORAN LEMBURAN');
                    $sheet1->mergeCells('G1:N1');
                    $sheet1->getStyle('G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet1->getStyle('G1:N1')->applyFromArray($sheetStyleBorder);
                    $sheet1->getStyle('G1:N1')->applyFromArray($sheetStyle);
                    
                    $sheet1->getStyle('G2:N2')->applyFromArray($sheetStyle);
                    $sheet1->getStyle('G2:N2')->applyFromArray($sheetStyleBorder);
                    $sheet1->getStyle('G2:N2')->getAlignment()->setHorizontal('center');
                    $sheet1->setCellValue('G2', 'TANGGAL');
                    $sheet1->setCellValue('H2', 'HARI');
                    $sheet1->setCellValue('I2', 'JAM MASUK');
                    $sheet1->setCellValue('J2', 'JAM KELUAR');
                    $sheet1->setCellValue('K2', 'OT1');
                    $sheet1->setCellValue('L2', 'OT2');
                    $sheet1->setCellValue('M2', 'OT3');
                    $sheet1->setCellValue('N2', 'TOTAL');
        
                    // TABLE PEMBAYARAN
                    $sheet1->setCellValue('P1', 'LAPORAN PEMBAYARAN');
                    $sheet1->mergeCells('P1:R1');
                    $sheet1->getStyle('P1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet1->getStyle('P1:R6')->applyFromArray($sheetStyleBorder);
                    $sheet1->getStyle('P1:R1')->applyFromArray($sheetStyle);
                    $sheet1->getStyle('P6:R6')->applyFromArray($sheetStyle);
        
                    $sheet1->getStyle('Q1:R1')->applyFromArray($sheetStyle);
                    $sheet1->getStyle('Q1:R1')->applyFromArray($sheetStyleBorder);
                    $sheet1->getStyle('P1:R1')->getAlignment()->setHorizontal('center');
        
                    $sheet1->getStyle('P1:P6')->applyFromArray($sheetStyle);
                    $sheet1->getStyle('P1:P6')->applyFromArray($sheetStyleBorder);
                    $sheet1->setCellValue('P2', 'Gaji Pokok');
                    $sheet1->setCellValue('P3', 'OT1');
                    $sheet1->setCellValue('P4', 'OT2');
                    $sheet1->setCellValue('P5', 'OT3');
                    $sheet1->setCellValue('P6', 'Total');
        
                    for($i = 0; $i < count($detail); $i++)
                    {
                        $column_start           = $column_start + 1;
                        $employee_prs_date      = $detail[$i]->emp_prs_date;
                        $employee_prs_in_time   = date("H:i:s", strtotime($detail[$i]->emp_prs_in_time));
                        $employee_prs_out_time  = date("H:i:s", strtotime($detail[$i]->emp_prs_out_time));
                        $employee_prs_name      = $detail[$i]->emp_name;
        
                        $date_day_name          = date('D', strtotime($employee_prs_date));
        
                        // GET JAM MASUK - JAM KELUAR
                        $jam_telat              = "08:10:00";
                        $jam_masuk              = getTime($date_day_name)['jam_masuk'];
                        $jam_keluar             = getTime($date_day_name)['jam_keluar'];
                        $batas_jam_ot_1         = getTime($date_day_name)['batas_ot_1'];
                        $batas_jam_ot_2         = getTime($date_day_name)['batas_ot_2'];
        
                        // CHECK TELAT
                        if($employee_prs_in_time > $jam_telat) {
                            $waktu_1    = strtotime($employee_prs_in_time);
                            $waktu_2    = strtotime($jam_telat);
                            $beda_menit = ($waktu_1 - $waktu_2) / 60;
                            
                            $jam_keluar_konversi    = strtotime($employee_prs_out_time);
        
                            $jam_keluar_konversi    -= $beda_menit * 60;
        
                            $jam_keluar_baru        = date('H:i:s', $jam_keluar_konversi);
                        } else {
                            $jam_keluar_baru        = $employee_prs_out_time;
                        }
        
                        if($jam_keluar_baru >= $batas_jam_ot_1) 
                        {
                            // GET OT2
                            $beda_waktu_ot_2_sec    = strtotime($jam_keluar_baru) - strtotime($batas_jam_ot_1);
                            $beda_waktu_ot_2_min    = $beda_waktu_ot_2_sec / 60;
                            $beda_jam_ot_2          = floor($beda_waktu_ot_2_min / 60);
        
                            if($beda_waktu_ot_2_min > 60) {
                                $beda_jam_ot_2   += 1;
                            }
        
                            // GET OT3
                            if($jam_keluar_baru >= $batas_jam_ot_2) {
                                $beda_waktu_ot_3_sec    = strtotime($jam_keluar_baru) - strtotime($batas_jam_ot_2);
                                $beda_waktu_ot_3_min    = $beda_waktu_ot_3_sec / 60;
                                $beda_jam_ot_3          = floor($beda_waktu_ot_3_min / 60);
        
                                if($beda_jam_ot_3 > 60) {
                                    $beda_jam_ot_3  += 1;
                                }
                            } else {
                                $beda_jam_ot_3  = 0;
                            }
        
                            $ot_1                   = 1;
                            $ot_2                   = number_format($beda_jam_ot_2, 0) > 0 ? number_format($beda_jam_ot_2, 0) - 1 : 0;
                            $ot_3                   = number_format($beda_jam_ot_3, 0) > 0 ? number_format($beda_jam_ot_3, 0) - 1 : 0;
        
                            $data[]   = [
                                "tanggal_lembur"    => $employee_prs_date,
                                "jam_masuk"         => $employee_prs_in_time,
                                "jam_keluar"        => $employee_prs_out_time,
                                "ot_1"              => $ot_1,
                                "ot_2"              => $ot_2,
                                "ot_3"              => $ot_3
                            ];
        
                            $total_ot_1         += $ot_1;
                            $total_ot_2         += $ot_2;
                            $total_ot_3         += $ot_3;
                        }
        
                        $ot_1   = 0;
                        $ot_2   = 0;
                        $ot_3   = 0;
        
                        $sheet1->getStyle('B'.$column_start.':E'.$column_start)->applyFromArray($sheetStyleBorder);
                        $sheet1->setCellValue('B'.$column_start, strtoupper($employee_prs_name));
                        $sheet1->setCellValue('C'.$column_start, getDayName($date_day_name).", ".date('d.m.Y', strtotime($employee_prs_date)));
                        $sheet1->setCellValue('D'.$column_start, $employee_prs_in_time);
                        $sheet1->setCellValue('E'.$column_start, $employee_prs_out_time);
                        // $sheet1->setCellValue('F'.$column_start, $jam_masuk);
                        // $sheet1->setCellValue('G'.$column_start, $jam_keluar);
                        $sheet1->getStyle('C'.$column_start.':E'.$column_start)->getAlignment()->setHorizontal('right');
                    }
        
                    $data_lemburan  = [
                        "nama_karyawan"     => $header->emp_name,
                        "gaji_pokok"        => $header->emp_fee,
                        "data_lemburan"     => $data, 
                        "total_overtime_1"  => $total_ot_1,
                        "total_overtime_2"  => $total_ot_2,
                        "total_overtime_3"  => $total_ot_3
                    ];

                    $data   = [];
        
                    $emp_gaji_pokok     = $data_lemburan['gaji_pokok'];
                    $emp_gaji_perjam    = $emp_gaji_pokok / 173;
                    $emp_total_ot_1     = $data_lemburan['total_overtime_1'];
                    $emp_total_ot_2     = $data_lemburan['total_overtime_2'];
                    $emp_total_ot_3     = $data_lemburan['total_overtime_3'];
                    $emp_fee_ot_1       = $emp_gaji_perjam * $emp_total_ot_1 * 1.5;
                    $emp_fee_ot_2       = $emp_gaji_perjam * $emp_total_ot_2 * 2;
                    $emp_fee_ot_3       = $emp_gaji_perjam * $emp_total_ot_3 * 3;
                    $total_semua_ot     = $emp_total_ot_1 + $emp_total_ot_2 + $emp_total_ot_3;
                    $total_bayar_ot     = $emp_fee_ot_1 + $emp_fee_ot_2 + $emp_fee_ot_3;
        
                    $sheet1->setCellValue('R2', number_format(round($emp_gaji_pokok, 3), 2));
                    $sheet1->setCellValue('S2', number_format(round($emp_gaji_perjam, 3), 2));
                    $sheet1->setCellValue('Q3', $emp_total_ot_1);
                    $sheet1->setCellValue('R3', number_format(round($emp_fee_ot_1, 3), 2));
                    $sheet1->setCellValue('Q4', $emp_total_ot_2);
                    $sheet1->setCellValue('R4', number_format(round($emp_fee_ot_2, 3), 2));
                    $sheet1->setCellValue('Q5', $emp_total_ot_3);
                    $sheet1->setCellValue('R5', number_format(round($emp_fee_ot_3, 3), 2));
                    $sheet1->setCellValue('Q6', $total_semua_ot);
                    $sheet1->setCellValue('R6', number_format(round($total_bayar_ot, 3), 2));
                    $sheet1->getStyle('Q2:R6')->getAlignment()->setHorizontal('right');
        
                    // LOOP LEMBURAN
                    $cell_lemburan  = 3;
                    if(count($data_lemburan['data_lemburan']) > 0) 
                    {
                        for($k = 0; $k < count($data_lemburan['data_lemburan']); $k++)
                        {
                            $lemburan_tanggal   = $data_lemburan['data_lemburan'][$k]['tanggal_lembur'];
                            $lemburan_jam_masuk = $data_lemburan['data_lemburan'][$k]['jam_masuk'];
                            $lemburan_jam_keluar= $data_lemburan['data_lemburan'][$k]['jam_keluar'];
                            $lemburan_ot1       = $data_lemburan['data_lemburan'][$k]['ot_1'];
                            $lemburan_ot2       = $data_lemburan['data_lemburan'][$k]['ot_2'];
                            $lemburan_ot3       = $data_lemburan['data_lemburan'][$k]['ot_3'];
                            $lemburan_total     = $lemburan_ot1 + $lemburan_ot2 + $lemburan_ot3;
                            
                            $sheet1->setCellValue('G'.$cell_lemburan, date('d.m.Y', strtotime($lemburan_tanggal)));
                            $sheet1->setCellValue('H'.$cell_lemburan, getDayName(date('D', strtotime($lemburan_tanggal))));
                            $sheet1->setCellvalue('I'.$cell_lemburan, $lemburan_jam_masuk);
                            $sheet1->setCellvalue('J'.$cell_lemburan, $lemburan_jam_keluar);
                            $sheet1->setCellvalue('K'.$cell_lemburan, $lemburan_ot1);
                            $sheet1->setCellvalue('L'.$cell_lemburan, $lemburan_ot2);
                            $sheet1->setCellvalue('M'.$cell_lemburan, $lemburan_ot3);
                            $sheet1->setCellvalue('N'.$cell_lemburan, $lemburan_total);
            
                            $cell_lemburan++;
                        }
                        // TOTAL TOTALAN
                        $sheet1->getStyle('G3:N'.$cell_lemburan)->applyFromArray($sheetStyleBorder);
                        $sheet1->getStyle('G'.$cell_lemburan.':N'.$cell_lemburan)->applyFromArray($sheetStyle);
                        $sheet1->mergeCells('G'.$cell_lemburan.':H'.$cell_lemburan);
                        $sheet1->setCellValue('G'.$cell_lemburan, 'Total');
                        $sheet1->setCellValue('K'.$cell_lemburan, $data_lemburan['total_overtime_1']);
                        $sheet1->setCellValue('L'.$cell_lemburan, $data_lemburan['total_overtime_2']);
                        $sheet1->setCellValue('M'.$cell_lemburan, $data_lemburan['total_overtime_3']);
                        $sheet1->setCellValue('N'.$cell_lemburan, $total_semua_ot);
                    }

                    $data_lemburan  = "";
        
                    autoSizeColumn($sheet1, 'B');
                    autoSizeColumn($sheet1, 'C');
                    autoSizeColumn($sheet1, 'D');
                    autoSizeColumn($sheet1, 'E');
                    autoSizeColumn($sheet1, 'G');
                    autoSizeColumn($sheet1, 'H');
                    autoSizeColumn($sheet1, 'I');
                    autoSizeColumn($sheet1, 'J');
                    autoSizeColumn($sheet1, 'K');
                    autoSizeColumn($sheet1, 'L');
                    autoSizeColumn($sheet1, 'M');
                    autoSizeColumn($sheet1, 'N');
                    autoSizeColumn($sheet1, 'P');
                    autoSizeColumn($sheet1, 'Q');
                    autoSizeColumn($sheet1, 'R');
                }
            }
            $spreadsheet->setActiveSheetIndex(0);
            // Simpan file Excel ke disk
            $file_path      = public_path('storage/data-files/lemburan_xls/');
            $file_name      = time()."_Lemburan.xlsx";

            if(!File::exists($file_path)) {
                File::makeDirectory($file_path, 0755, true);
            }
            $writer = new Xlsx($spreadsheet);
            $writer->save($file_path.$file_name);

            $output     = [
                "status"    => 200,
                "success"   => true,
                "data"      => [
                    "file_url"  => "storage/data-files/lemburan_xls",
                    "file_name" => $file_name,
                ],
                "message"   => "Berhasil Memuat Data",
            ];
        } else {
            $output     = [
                "status"    => 404,
                "success"   => false,
                "data"      => [
                    "file_url"  => "",
                    "file_name" => "",
                ],
                "message"   => "Gagal Membuat Report ",
            ];
        }

        return Response::json($output, $output['status']);
    }

    // GET DATA FROM UMHAJ
    public function umh_get_data_tour_code($tahun)
    {
        $get_data   = DivisiService::get_data_tour_code_umhaj($tahun);

        if(count($get_data) > 0) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Mengambil Data Tour Code Tahun ".$tahun,
                "data"      => $get_data,
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 404,
                "message"   => "Gagal Mengambil Data Tour Code",
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    // GET DATA FROM UMHAJ BY TOUR CODE
    public function umh_get_data_tour_code_detail(Request $request)
    {
        $tour_code  = $request->all()['sendData']['tour_code'];
        
        $get_data   = DivisiService::get_data_tour_code_detail($tour_code);
        
        if(count($get_data) > 0) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Mengambil Data Tour Code : ".$tour_code,
                "data"      => $get_data,
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 404,
                "message"   => "Gagal Mengambil Data Tour Code : ".$tour_code,
                "data"      => []
            ];
        }

        return Response::json($output, $output['status']);
    }
}