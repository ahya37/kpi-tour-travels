<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BaseService;
use App\Services\ProgramKerjaService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ProkerTahunan;
use App\Models\ProkerBulanan;
use App\Models\ProkerHarian;
use App\Models\Employee;
use App\Helpers\Months;
use Response;
use File;
use Carbon\Carbon;
use App\Helpers\ResponseFormatter;

date_default_timezone_set('Asia/Jakarta');

class ProgramKerjaController extends Controller
{
    //

    public function index()
    {
        $data   = [
            'title'     => 'Master Program Kerja',
            'sub_title' => 'Dashboard Program Kerja'
        ];

        return view('master/programKerja/index', $data);
    }

    public function getDataTotalProgramKerja()
    {
        $getData    = ProgramKerjaService::doGetDataTotalProgramKerja();
        if(!empty($getData)) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "data"      => $getData,
                "message"   => "Berhasil Mengambil Data",
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 404,
                "data"      => [],
                "message"   => "Berhasil Mengambil Data",
            ];
        }

        return Response::json($output, $output['status']);
    }

    // TAHUNAN
    public function indexTahunan()
    {
        $data   = [
            'title'     => 'Master Program Kerja',
            'sub_title' => 'Program Kerja - Tahunan'
        ];

        return view('master/programKerja/tahunan/index', $data);
    }

    public function ambilDataProkerTahunan($data)
    {
        return $getData    = ProgramKerjaService::getDataProkerTahunan($data);
    }

    public function ambilListDataProkerTahunan(Request $request)
    {
        $filter     = [
            "uid"       => request()->id,
            "roleName"  => Auth::user()->getRoleNames()[0] == 'admin' ? '%' : Auth::user()->getRoleNames()[0],
        ];
        $getData    = $this->ambilDataProkerTahunan($filter);
        if(!empty($getData)) {
            for($i = 0; $i < count($getData); $i++) {
                $data[]     = array(
                    $i + 1,
                    $getData[$i]->title,
                    $getData[$i]->division_group_name,
                    $getData[$i]->periode,
                    $getData[$i]->total_program,
                    "<button type='button' class='btn btn-sm btn-primary' value='" . $getData[$i]->uid . "' title='Edit Program Kerja' onclick='show_modal(`modalTambahDataProkerTahunan`, this.value)'><i class='fa fa-edit'></i></button>"
                );
            }
        } else {
            $data   = [];
        }

        $output     = array(
            "draw"  => 1,
            "recordsFiltered"   => count($data),
            "recordsData"       => count($data),
            "data"              => $data,
        );

        return Response::json($output, 200);
    }

    public function ambilDataProkerTahunanDetail(Request $request)
    {
        $getData    = ProgramKerjaService::getDataProkerTahunanDetail($request->all()['sendData']);
        if(!empty($getData['header'])) {
            $data_header    = array(
                "program_kerja_title"       => $getData['header'][0]->pkt_title,
                "program_kerja_description" => $getData['header'][0]->pkt_description,
                "program_kerja_periode"     => $getData['header'][0]->pkt_year,
                "program_kerja_pic_id"      => $getData['header'][0]->pkt_pic_job_employee_id,
                "program_kerja_group_div_id"=> $getData['header'][0]->division_group_id,
            );
        } else {
            $data_header    = [];
        }
        if(!empty($getData['detail'])) {
            for($i = 0; $i < count($getData['detail']); $i++) {
                $data_detail[]  = array(
                    "sub_program_kerja_seq"     => $i + 1,
                    "sub_program_kerja_title"   => $getData['detail'][$i]->detail_title,
                );
            }
        } else {
            $data_detail    = [];
        }

        if(!empty($getData['header'])) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Ambil Data",
                "data"      => [
                    "header"    => $data_header,
                    "detail"    => $data_detail,
                ],
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "message"   => "Data yang dipilih tidak ditemukan",
                "data"      => [
                    "header"    => null,
                    "detail"    => null,
                ],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function simpanDataProkerTahunan($jenis, Request $request)
    {
        $rulesProkerHeader  = [
            "prtTitle"              => 'required',
            "prtPeriode"            => 'required',
            "prtGroupDivisionID"    => 'required',
            "prtPICEmployeeID"      => 'required',
        ];

        $doValidate     = Validator::make($request->all()['sendData'], $rulesProkerHeader);

        if($doValidate->fails())
        {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "alert"     => [
                    "icon"  => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => "Data Gagal Disimpan",
                        "errMsg"    => $doValidate->getMessageBag()->toArray()
                    ],
                ],
            );
        } else {
            $ip         = $request->ip();
            $doSimpan   = ProgramKerjaService::doSimpanProkerTahunan($request->all()['sendData'], $jenis, $ip);
            if($doSimpan['transStatus'] == 'berhasil') {
                $output     = array(
                    "success"   => true,
                    "status"    => 200,
                    "alert"     => [
                        "icon"  => "success",
                        "message"   => [
                            "title"     => "Berhasil",
                            "text"      => "Data Program Kerja Tahunan Berhasil Disimpan",
                            "errMsg"    => "",
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
                            "title"     => "Terjadi Kesalahan",
                            "text"      => "Data Program Kerja Tahunan Gagal Disimpan",
                            "errMsg"    => $doSimpan['errMsg'],
                        ],
                    ],
                );
            }
        }
        return Response::json($output, $output['status']);
    }
    // BULANAN
    public function indexBulanan()
    {
        $data   = [
            'title'     => 'Master Program Kerja',
            'sub_title' => 'Program Kerja - Bulanan'
        ];

        return view('master/programKerja/bulanan/index', $data);
    }

    public function getProkerBulananAll(Request $request)
    {
        // var_dump($request->all()['sendData']);die();
        $data_cari  = [
            "uuid"                  => $request->all()['sendData']['cari'],
            "current_role"          => Auth::user()->getRoleNames()[0],
            "group_divisi"          => $request->all()['sendData']['divisi'],
            "tgl_awal"              => !empty($request->all()['sendData']['tgl_awal']) ? $request->all()['sendData']['tgl_awal'] : date('Y')."-".date('m')."-01",
            "tgl_akhir"             => !empty($request->all()['sendData']['tgl_akhir']) ? $request->all()['sendData']['tgl_akhir'] : date('Y-m-d'),
            "jadwal"                => !empty($request->all()['sendData']['jadwal']) ? $request->all()['sendData']['jadwal'] : null,
            "sub_divisi"            => !empty($request->all()['sendData']['sub_divisi']) ? $request->all()['sendData']['sub_divisi'] : '%',
        ];
        $getData    = ProgramKerjaService::getProkerBulananAll($data_cari);
        
        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "data"      => [
                    "list"      => $getData['list'],
                    "header"    => $getData['header'],
                    "detail"    => $getData['detail'],
                ],
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "data"      => []
            );
        }

        return Response::json($output);
    }

    public function getProkerTahunan(Request $request)
    {
        $getData    = ProgramKerjaService::doGetProkerTahunan($request->all()['sendData']);

        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Ambil Data Proker Tahunan",
                "data"      => $getData
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "message"   => "Terjadi Kesalahan",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function getSubProkerTahunan(Request $request)
    {
        $getData    = ProgramKerjaService::getDataProkerTahunanDetail($request->all()['sendData']['prokerTahunan_ID']);

        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Load Data",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "message"   => "Tidak ada data yang dimuat",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function getDataPICbyGroupDivisionID(Request $request)
    {
        $getData    = ProgramKerjaService::getDataPIC($request->all()['sendData']['GroupDivisionID']);
        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Ambil Data PIC",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "message"   => "Gagal Mengambil Data PIC",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function simpanProkerBulanan(Request $request)
    {
        // print("<pre>" . print_r($request->all()['sendData'], true) . "</pre>");die();
        $doSimpan   = ProgramKerjaService::doSimpanProkerBulanan($request);
        if($doSimpan['status'] == 'berhasil') {
            $output     = array(
                "success"       => true,
                "status"        => 200,
                "alert"         => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => "Berhasil Menyimpan Program Kerja Baru",
                        "errMsg"    => null,
                    ],
                ],
            );
        } else if($doSimpan['status'] == 'gagal') {
            $output     = array(
                "success"       => false,
                "status"        => 500,
                "alert"         => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => "Gagal Menyimpan Program Kerja Baru",
                        "errMsg"    => null,
                    ],
                ],
            );
        }
        
        return Response::json($output, $output['status']);
    }

    public function getListDataHarian(Request $request)
    {
        $getData     = ProgramKerjaService::doGetListDataHarian($request);

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
                "data"      => $getData,
            );
        }

        return Response::json($output, $output['status']);
    }

    public function listProkerTahunan()
    {
        $getData    = ProgramKerjaService::listProkerTahunan();
        
        if(!empty($getData)) {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "status"    => 500,
                "success"   => false,
                "data"      => $getData,
            );
        }

        return Response::json($output, $output['status']);
    }

    public function cellProkerBulanan(Request $request)
    {
        $getData    =  ProgramKerjaService::getCellProkerBulanan($request->all()['sendData']);
        
        if(!empty($getData)) {
            for($i = 0; $i < count($getData); $i++) {
                $data_ke    = intval($getData[$i]->data_ke) - $i;
                $start_date = intval(explode('-', $getData[$i]->pkb_start_date)[2]);

                $data[]     = [
                    "row_ke"    => $data_ke,
                    "cell_ke"   => $start_date,
                    "text"      => "<i class='fa fa-check'></i>",
                ];
            }
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "data"      => $data,
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

    // 14/06/2024
    // NOTE : PEMBUATAN FUGNSI UNTUK MENGAMBIL DATA PADA SELECT DI VIEW PROGRAM KERJA BULANAN

    public function listSelectJadwalUmrah()
    {
        $getData    = ProgramKerjaService::getListSelectJadwalUmrah();

        if(!empty($getData)) {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Ambil Data Jadwal Umrah",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "status"    => 404,
                "success"   => false,
                "message"   => "Tidak Ada Jadwal Umrah",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    // HARIAN
    public function indexHarian()
    {
        $data   = [
            'title'     => 'Master Program Kerja',
            'sub_title' => 'Program Kerja - Harian',
        ];

        return view('master/programKerja/harian/index', $data);
    }

    public function listTableProkerHarian(Request $request)
    {
        $getData    = ProgramKerjaService::listProkerHarian($request->all());
        if(!empty($getData)) {
            
            for($i = 0; $i < count($getData); $i++) {
                $data[]     = array(
                    $i + 1,
                    $getData[$i]->pkh_title,
                    $getData[$i]->pkh_date,
                    $getData[$i]->group_division,
                    "<button type='button' class='btn btn-sm btn-primary' value='".$getData[$i]->pkh_id."' title='Preview' onclick='showModal(`modalForm`, `edit`, this.value)'><i class='fa fa-eye'></i></button>"
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

        return Response::json($output);
    }

    public function detailDataProkerHarian(Request $request)
    {
        $uuid   = $request->all()['sendData']['pkh_id'];
        $getData    = ProgramKerjaService::getProkerHarianDetail($uuid);

        if(!empty($getData['header']) || !empty($getData['detail']))
        {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function testUpload(Request $request)
    {
		$file = $request->file('file');
        $namaFile       = time()."_".str_replace(' ','_',$file->getClientOriginalName());
        $tujuanUpload   = 'user_data';
        $fileStorage           = $tujuanUpload."/".$namaFile;
        // $file->move($tujuanUpload, $namaFile);
        Storage::disk('local')->makeDirectory($tujuanUpload);
        $file->storeAs($tujuanUpload, $namaFile);
        $path       = array(
            "originalName"  => $file->getClientOriginalName(),
            "systemName"    => $namaFile,
            "path"          => $fileStorage,
        );

        return $path;
    }

    public function deleteUpload(Request $request)
    {
        $path   = $request->all()['sendData']['path_files'];
        // if(File::delete($path)) {
            if(Storage::disk('local')->delete($path)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Hapus Data",
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "message"   => "Gagal Hapus Data",
            );
        }

        return Response::json($output, $output['status']);
    }

    public function dataProkerBulanan(Request $request)
    {
        $sendData   = [
            "rolesName"     => (Auth::user()->getRoleNames()[0] == 'admin' || Auth::user()->getRoleNames()[0] == 'umum') ? '%' : Auth::user()->getRoleNames()[0],
            "currentDate"   => date('Y-m-d'),
            "pkt_uuid"      => $request->all()['sendData']['pkt_uid'],
            "pkb_uuid"      => $request->all()['sendData']['pkb_uid'],
        ];
        $getData    = ProgramKerjaService::getProkerBulanan($sendData);
        if(count($getData) > 0)
        {
            $header     = [];
            $detail     = [];

            for($i = 0; $i < count($getData); $i++) {
                $header[]   = array(
                    "pkb_uuid"  => $getData[$i]->pkb_uuid,
                    "pkb_title" => $getData[$i]->pkb_title,
                    "pkb_date"  => $getData[$i]->pkb_date,
                );
            }
            // REMOVE DUPLICATE HEADER
            $header_remove_duplicate    = array_reduce($header, function($carry, $item){
                if(!isset($carry[$item['pkb_uuid']])) {
                    $carry[$item['pkb_uuid']] = $item;
                }
                return $carry;
            }, []);

            $header_remove_duplicate    = array_values($header_remove_duplicate);
            
            // INSERT KE DETAIL
            for($k = 0; $k < count($getData); $k++) {
                $detail[]   = array(
                    "pkbd_id"   => $getData[$k]->pkbd_id,
                    "pkb_detail"=> $getData[$k]->pkb_type_detail,
                );
            }

            // print("<pre>" . print_r(array_unique($header), true) . "</pre>");die();
            // PISAHIN ARRAY
            // $keyArray   = [];
            // $tempArray  = [];
            // for($i = 0; $i < count($getData); $i++) {
            //     if(!in_array($getData[$i]->pkb_uuid, $keyArray)) {
            //         $keyArray[$i]   = $getData[$i]->pkb_uuid;
            //         $tempArray[]  = $getData[$i];
            //     }
            // }

            // // INSERT KE HEADER
            // // print("<pre>".print_r($tempArray, true)."</pre>");die();
            // for($j = 0; $j < count($tempArray); $j++) {
            //     $header[]   = array(
            //         "pkb_uuid"  => $tempArray[$j]->pkb_uuid,
            //         "pkb_title" => $tempArray[$j]->pkb_title,
            //         "pkb_date"  => $tempArray[$j]->pkb_date,
            //     );
            // }

            $output     = array(
                "success"   => true,
                "status"    => 200,
                "data"      => [
                    "header"    => $header_remove_duplicate,
                    "detail"    => $sendData['pkb_uuid'] == '%' ? [] : $detail
                ],
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "data"      => null,
            );
        }

        return Response::json($output, $output['status']);
    }

    public function simpanDataHarian(Request $request)
    {
        $ip         = $request->ip();
        $doSimpan   = ProgramKerjaService::simpanDataHarian($request->all()['sendData'], $ip);
        if($doSimpan['status'] == "berhasil") {
            $output     = array(
                "sucess"    => true,
                "status"    => 200,
                "alert"     => [
                    "icon"  => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => "Aktivitas Harian Baru Telah Ditambahkan",
                        "errMsg"    => null,
                    ],
                ],
            );
        } else {
            $output     = array(
                "sucess"    => true,
                "status"    => 200,
                "alert"     => [
                    "icon"  => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => "Aktivitas Harian Baru Telah Ditambahkan",
                        "errMsg"    => $doSimpan['errMsg'],
                    ],
                ],
            );
        }

        return Response::json($output, $output['status']);
        
    }

    public function ProkerHarianDownloadFile($path)
    {
        $filePath   = "/user_data/".$path;
        if(Storage::exists($filePath)) {
            return Storage::download($filePath);
        } else {
            abort(404);
        }
    }

    // 21 JUNI 2024
    // NOTE : PEMBUATAN FUNGSI UNTUK MEMANGGIL PROKER TAHUNAN BERDASARKAN GROUP DIVISI
    public function getProgramKerjaTahunan($groupDivisionID)
    {
        $getData    = ProgramKerjaService::doGetProgramKerjaTahunan($groupDivisionID);
    
        if(!empty($getData)) {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Mengambil Data Program Kerja Tahunan",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "status"    => 404,
                "success"   => false,
                "message"   => "Tidak ada data yang bisa diambil",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function getProgramKerjaBulanan($prokerTahunanID)
    {
        $getData    = ProgramKerjaService::doGetProgramKerjaBulanan($prokerTahunanID);

        if(!empty($getData)) {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Mengambil Data Program Kerja Bulanan",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "status"    => 404,
                "success"   => false,
                "message"   => "Tidak ada data yang bisa diambil",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    // 26 JUNI 2024
    // NOTE : UNTUK MENARIK JADWAL PADA PROGRAM KERJA BULANAN
    public function listSelectJadwalUmrahForm()
    {
        $getData    = ProgramKerjaService::getListSelectJadwalUmrahForm();

        if(!empty($getData)) {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Mengambil Data",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "status"    => 500,
                "success"   => false,
                "message"   => "Terjadi Kesalahan",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function listSelectedJadwalUmrahForm(Request $request)
    {
        $jadwalID   = $request->all()['sendData']['prog_jdw_id'];
        $getData    = ProgramKerjaService::getListSelectedJadwalUmrahForm($jadwalID);

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
                "message"   => "Gagal Mengambil Data",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    // GLOBAL
    public function getDataPIC(Request $request)
    {
        $getData    = BaseService::getDataEmployeeByGroupDivision($request->all()['sendData']['groupDivisionID']);
        if(count($getData) > 0) {
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

    public function reportPekerjaanMarketing()
    {
        $query['created_by'] = request('created_by');

        // get data employee
        $employees = Employee::getEmployees();

        $data   = [
            'title'     => 'Laporan Pekerjaan Divisi Marketing',
            'sub_title' => 'Laporan Pekerjaan Divisi Marketing',
            // 'html' => $html,
            'employees' => $employees
        ];

       return view('marketings.laporan.report-pekerjaan', $data);
        
    }

    public function getReportPekerjaanMarketing()
    {
        $year  = date('Y');

        $results = [];

        // get data employee
        $employees = Employee::getEmployees();

        // get data pekerjaan harian
        #Get divisi id marketing 
        $groupDivisionID = env('APP_GROUPDIV_MARKETING');
        $proker_tahunan_group_bulan   = ProkerBulanan::prokerGroupBulananByTahunan($groupDivisionID);

        foreach ($proker_tahunan_group_bulan as $annual) {

            $res_proker_harian = ProkerHarian::getAktivitasHarianByBulanTahunAndDivisiByTest($groupDivisionID, $annual->month, $year);

            // if (request()->created_by != '' && request()->date != '') {

            //     $date = Carbon::createFromFormat('d-m-Y',request()->date)->format('Y-m-d');

            //     $res_proker_harian = $res_proker_harian->where('d.id',request()->created_by)->where('a.pkh_date', $date);

            // }elseif (request()->created_by = '' && request()->date != '') {

            //     $date = Carbon::createFromFormat('d-m-Y',request()->date)->format('Y-m-d');
            //     $res_proker_harian = $res_proker_harian->where('a.pkh_date', $date);


            // }elseif (request()->created_by != '' && request()->date = '') {

            //     $res_proker_harian = $res_proker_harian->where('d.id',request()->created_by);
            // }
            if (request()->created_by != '') {
                $res_proker_harian = $res_proker_harian->where('d.id',request()->created_by);
            }

            $res_proker_harian = $res_proker_harian->orderBy('a.pkh_date','asc')->get();
            
            if (count($res_proker_harian) != 0) {
                $results[] = [
                    // 'annual' => $annual->pkt_title,
                    'month_number' => $annual->month,
                    'month_name' => Months::monthName($annual->month),
                    'aktivitas' => $res_proker_harian,
                    'count_aktivitas' => count($res_proker_harian) + 1
                ];
            }

        }

        // return $results;
        $html = "";
        foreach ($results as $value) {
            $rowspan = $value['count_aktivitas'];
            $html = $html.'<tr>';
            $html = $html.'<td rowspan='.$rowspan.' style="display: table-cell;text-align: center;font-size:14px">'.$value['month_name'].'</td>';
            $html = $html.'</tr>';

            foreach ($value['aktivitas'] as $aktivitas) {
                $html = $html.'<tr>';
                $html = $html.'<td>'.$aktivitas->pkh_date ?? ''.'</td>';
                $html = $html.'<td>'.$aktivitas->pkh_title ?? ''.'</td>';
                $html = $html.'<td>'.$aktivitas->pic ??''.'</td>';
                $html = $html.'</tr>';
            }
        }


        $data   = [
            'html' => $html,
        ];

        return response()->json($data);

    }
	
	public function reportRencanaKerjaMarekting()
	{
        try {
			
			$groupDivisionID = env('APP_GROUPDIV_MARKETING');
			
			$year  = date('Y');

			$results = [];

			// get data employee
			// $employees = Employee::getEmployees();

			// get data pekerjaan harian
			#Get divisi id marketing 
			$groupDivisionID = env('APP_GROUPDIV_MARKETING');
			$proker_tahunan_group_bulan   = ProkerBulanan::prokerGroupBulananByTahunan($groupDivisionID);

			foreach ($proker_tahunan_group_bulan as $annual) {

				// $res_proker_harian = ProkerHarian::getAktivitasHarianByBulanTahunAndDivisiByTest($groupDivisionID, $annual->month, $year);
				// $res_proker_harian = $res_proker_harian->orderBy('a.pkh_date','asc')->get();
				
				// get kegiatan berdasarkan bulanan dan divisinya
				$prokerBulanan = ProkerBulanan::getProkerBulananMarkering($groupDivisionID, $annual->month, $year);
				
				$res_proker_bulanan = [];
				foreach($prokerBulanan as $item){
					// GET HARIAN BERDASARKAN BULANAN DAN CREATED_BY NYA 
					$aktivitas_harian = ProkerHarian::getProkerHarianByBulananAndUser($item->uuid,$item->created_by);
				
					// get jenis pekerjaan nya di table bulanan detail berdasarkan id bulan nya
					$prokerBulananDetail = ProkerBulanan::getProkerBulananDetail($item->id);
					$res_proker_bulanan[] = [
						'pkb_start_date' => $item->pkb_start_date,
						'pkb_title' => $item->pkb_title,
						'created_by_name' => $item->created_by_name,
						'created_by' => $item->created_by,
						'janis_pekerjaan' => $prokerBulananDetail,
						'aktivitas_harian' => $aktivitas_harian 
					];
				}
				
				
				$results[] = [
						// 'annual' => $annual->pkt_title,
						'month_number' => $annual->month,
						'month_name' => Months::monthName($annual->month),
						'rencana_kerja_bulanan' => $res_proker_bulanan
					];

			}
			
            return ResponseFormatter::success([  
				'rencanakerja'  => $results,
                'message' => 'Laporan percenaan kerja marketing'
            ]);

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
			return $e->getMessage();
            return ResponseFormatter::error([
                'message' => 'Gagal Singkronkan data!'
            ]);
        }
	} 
}
