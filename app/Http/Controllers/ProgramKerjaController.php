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
use App\Models\MarketingTarget;
use App\Models\Employee;
use App\Helpers\Months;
use Response;
use File;
use Carbon\Carbon;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use DB;
use App\Helpers\NumberFormat;
use App\Helpers\DateFormat;
use DateTime;

date_default_timezone_set('Asia/Jakarta');

class ProgramKerjaController extends Controller
{
    //

    public function index()
    {
        $currentUser    = Auth::user()->id;
        $currentRole    = Auth::user()->getRoleNames()[0];
        
        if($currentRole != 'admin') {
            $employee       = DB::select(
                "
                SELECT 	a.id,
                        a.name,
                        b.group_division_id,
                        LOWER(c.name) as group_division_name,
                        b.sub_division_id,
                        LOWER(d.name) as sub_division_name
                FROM 	employees a
                JOIN 	job_employees b ON a.id = b.employee_id
                JOIN 	group_divisions c ON b.group_division_id = c.id
                JOIN 	sub_divisions d ON b.sub_division_id = d.id
                AND 	a.user_id LIKE '$currentUser'
                "
            );

            if(!empty($employee)) {
                $currentGroupDivision   = $employee[0]->group_division_id;
                $currentSubDivision     = $employee[0]->sub_division_name;
            }
        } else {
            $currentGroupDivision   = '%';
            $currentSubDivision     = '%';
        }

        $data   = [
            'title'             => 'Master Program Kerja',
            'sub_title'         => 'Dashboard Program Kerja',
            'current_user'      => Auth::user()->id,
            'current_role'      => Auth::user()->getRoleNames()[0],
            'group_division'    => $currentGroupDivision,
            'sub_division'      => $currentSubDivision,
        ];
        
        $datas = [
            'title' => 'Halaman Sedang Dalam Pengembangan',
            'sub_title' => 'Halaman Sedang Dalam Pengembangan'
        ];

        return view('maintenance', $datas);

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
                    "sub_program_kerja_target"  => $getData['detail'][$i]->detail_target,
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

    // 04 JULI 2024
    // NOTE : AMBIL DATA UNTUK DATATABLES DASHBOARD
    public function getDataTableDashboard(Request $request)
    {
        $getData    = ProgramKerjaService::doGetDataTableDashboad($request->all());

        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil",
                "data"      => $getData,
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

    // HARIAN
    public function indexHarian()
    {
        $data   = [
            'title'     => 'Master Program Kerja',
            'sub_title' => 'Program Kerja - Harian',
        ];

        return view('maintenance');

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
            "pkb_selected_month"    => $request->all()['sendData']['pkb_selected_month'],
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

    // 07 JULI 2024
    // NOTE : HAPUS DATA HARIAN
    public function hapusDataHarian($id, Request $request)
    {
        $ip     = $request->ip();
        $doUpdate   = ProgramKerjaService::doHapusDataHarian($id, $ip);

        if($doUpdate['status'] == 'success') {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "alert"     => [
                    "icon"  => "success",
                    "message"   => [
                        "title" => "Berhasil",
                        "text"  => "Berhasil Hapus Data",
                    ],
                ],
                "errMsg"    => [],
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "alert"     => [
                    "icon"  => "error",
                    "message"   => [
                        "title" => "Terjadi Kesalahan",
                        "text"  => "Data Gagal Dihapus",
                    ],
                ],
                "errMsg"    => $doUpdate['errMsg'],
            );
        }

        return Response::json($output, $output['status']);
    }

    // 08 JULI 2024
    // NOTE : GET LIST USER BY GROUP DIVISION
    public function getDatatableDashboardListUser(Request $request)
    {
        $groupDivisionID    = $request->all()['sendData']['groupDivisionID'];
        
        $getData            = ProgramKerjaService::doGetDataTableListUser($groupDivisionID);

        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "message"   => "Terjadi Kesalahan",
                "data"      => []
            );
        }

        return Response::json($output, $output['status']);
    }

    // NOTE : FUNGSI HAPUS PROGRAM KERJA BULANAN
    public function hapusProgramKerja(Request $request)
    {
        $data       = array(
            "pkb_id"    => $request->all()['sendData'],
            "ip"        => $request->ip(),
        );

        $doHapus    = ProgramKerjaService::doHapusProgramKerja($data);
        
        if($doHapus['status'] == 'berhasil') {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => "Data berhasil dihapus",
                    ],
                ],
                "errMsg"    => $doHapus['errMsg'],
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => "Data berhasil dihapus",
                    ],
                ],
                "errMsg"    => $doHapus['errMsg'],
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
        $data   = [
            'title'     => 'Laporan Evaluasi Kerja Marketing',
            'sub_title' => 'Laporan Evaluasi Kerja Marketing',
        ];

       return view('marketings.laporan.report-rencana-kerja', $data);
    }
	
	public function getReportRencanaKerjaMarekting()
	{
        try {
			
			$groupDivisionID = env('APP_GROUPDIV_MARKETING');

			$year  = request()->year;
			$month = request()->month;
			
			#Get divisi id marketing 
			$groupDivisionID = env('APP_GROUPDIV_MARKETING');
			
			// get kegiatan berdasarkan bulanan dan divisinya
			$prokerBulanan = ProkerBulanan::getProkerBulananMarkering($groupDivisionID, $month, $year);

			// group per tanggal
			$grouped = $prokerBulanan->groupBy('pkb_start_date');

			$res_grouped = [];
			foreach ($grouped as $key => $value) {

				// get jenis pekerjaan 
				$res_jenis_pekerjaan = [];
				foreach ($value as $item) {

					$prokerBulananDetail = ProkerBulanan::getProkerBulananDetail($item->id);

					// GET HARIAN BERDASARKAN BULANAN DAN CREATED_BY NYA 
					$aktivitas_harian = ProkerHarian::getProkerHarianByBulananAndUser($item->uuid,$item->created_by);

					$res_jenis_pekerjaan[] = [
						'id' => $item->id,
						'pkb_start_date' => $item->pkb_start_date,
						'pkb_title' => $item->pkb_title,
						'created_by_name' => $item->created_by_name,
						'created_by' => $item->created_by,
						'jenis_pekerjaan' => $prokerBulananDetail ?? [],
                        'count_jenis_pekerjaan' => count( $prokerBulananDetail),
						'aktivitas_harian' => $aktivitas_harian ?? [],
                        'count_aktivitas_harian' => count($aktivitas_harian)
					];
				}

                // total rowspan per tanggal, sum aktivitas harian 
                $sum_aktivitas_harian = collect($res_jenis_pekerjaan)->sum(function($q){
                    return $q['count_aktivitas_harian'];
                });

                $sum_jenis_pekerjaan = collect($res_jenis_pekerjaan)->sum(function($q){
                    return $q['count_jenis_pekerjaan'];
                });

                // rowspan per title bulan
				$res_grouped[] = [
					'tanggal' => $key,
					'uraian_pekerjaan' => $res_jenis_pekerjaan,
                    'count_uraian_pekerjaan' => count($res_jenis_pekerjaan),
                    'sum_aktivitas_harian' => $sum_aktivitas_harian,
                    'sum_jenis_pekerjaan' => $sum_jenis_pekerjaan
				];
			}

            $html = "";
            foreach ($res_grouped as $value) {
                $rowspan = $value['sum_jenis_pekerjaan'] + $value['count_uraian_pekerjaan'] + 1;
                $html = $html.'<tr>';
                // $html = $html.'<td>'.$value['month_name'].'</td>';
                $html = $html.'<td rowspan='.$rowspan.' style="display: table-cell;text-align: center;font-size:14px">'. date('d-m-Y', strtotime($value['tanggal']) ?? '').'</td>';
                $html = $html.'</tr>';

                foreach ($value['uraian_pekerjaan'] as $aktivitas) {

                    $rowspan_pkb_title = $aktivitas['count_jenis_pekerjaan'] + 1;
                    $html = $html.'<tr>';
                    $html = $html.'<td rowspan='.$rowspan_pkb_title.'>'.$aktivitas['pkb_title'] ?? ''.'</td>';

                    if($aktivitas['count_jenis_pekerjaan'] == 0){
                        $html = $html.'<td></td>';
                        $html = $html.'<td></td>';
                        $html = $html.'<td></td>';
                        $html = $html.'<td></td>';
                        $html = $html.'<td></td>';
                    }
                    $html = $html.'</tr>';

                    foreach ($aktivitas['jenis_pekerjaan'] as $jenis_pekerjaan) {
                        $html = $html.'<tr>';
                        $html = $html.'<td>'.$jenis_pekerjaan->pkbd_type ?? ''.'</td>';
                        $html = $html.'<td>'.$jenis_pekerjaan->pkbd_target ?? ''.'</td>';
                        $html = $html.'<td>'.$jenis_pekerjaan->pkbd_result ?? ''.'</td>';
                        $html = $html.'<td>'.$jenis_pekerjaan->pkbd_evaluation ?? ''.'</td>';
                        $html = $html.'<td><button class="btn btn-sm btn-primary text-center onDetail" id='.$jenis_pekerjaan->id.'-'.$jenis_pekerjaan->pkb_id.'><i class="fa fa-eye"></i></button></td>';
                        $html = $html.'</tr>';

                    }
                }
            }

            return ResponseFormatter::success([  
				'bulan' =>  Months::monthName($month),  
				'rencanakerja'  => $html,
                'results' => $res_grouped,
            ]);

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' => 'Gagal Singkronkan data!'
            ]);
        }
	} 

     

    public function getRincianKegiatanByJenisPekerjaan()
    {
        try {
            $id = request()->id;

            // get data aktivitas harian berdasarkan bulanan dan detail nya
            $id_proker_bulanan  = explode("-", $id)[1];
            $proker_bulanan_uuid = DB::table('proker_bulanan')->select('uuid')->where('id', $id_proker_bulanan)->first();

            $id_jenis_pekerjaan = explode("-", $id)[0];
            $id_jenis_pekerjaan = (int) $id_jenis_pekerjaan;

            $rincian_kegiatan = DB::table('proker_harian as a')
                                ->select('a.pkh_title','a.pkh_date','a.pkh_start_time','a.pkh_end_time','c.name as cs')
                                ->join('proker_bulanan_detail as b', function($join1){
                                    $join1->on(DB::raw('SUBSTRING_INDEX(a.pkh_pkb_id, "|",-1)'), '=', 'b.id');
                                })
                                ->join('users as c','a.created_by','=','c.id')
                                ->whereRaw(DB::raw("SUBSTRING_INDEX(a.pkh_pkb_id, '|', 1) = '$proker_bulanan_uuid->uuid'"))
                                ->whereRaw(DB::raw("SUBSTRING_INDEX(a.pkh_pkb_id, '|', -1) = $id_jenis_pekerjaan"))
                                ->get();

            $results = [];
            $no = 1;
            foreach($rincian_kegiatan as $value){
                $results[] = [
                    'no' => $no++,
                    'pkh_title' => $value->pkh_title,
                    'pkh_date'  => date('d-m-Y', strtotime($value->pkh_date)),
                    'cs'  => $value->cs,
                ];
            }

            return ResponseFormatter::success([  
                'rincian_kegiatan' => $results,
            ]);

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' => 'Gagal Singkronkan data!'
            ]);
        }


    }

   

    public function getRincianKegiatanByProgramBulanan(Request $request)
    {
        try {

            $pkb_id = request()->pkbid;
            $pkb_uuid = request()->pkbuuid;

            $year  = request()->year;
            $month = request()->month;
            $week  = request()->week;

            $proker_bulanan = ProkerBulanan::select('pkb_title')->where('id', $pkb_id)->first();

            $rincian_kegiatan = [];
           
            // jika week ada 
            if ($week != '') {

                // get rincian kegitana by bulanan detail nya, juga perminggu nya
                $rincian_kegiatan = ProkerHarian::getProkerHarianByProkerBulananPerMinggu($year, $month, $pkb_uuid,$week);

            }else{
                
                 // get rincian kegitana by bulanan detail nya
                $rincian_kegiatan = ProkerHarian::getProkerHarianByProkerBulanan($pkb_uuid);

            }

            $results = [];
            foreach ($rincian_kegiatan as $key => $value) {
                $results[] = [
                    'pkh_date' => date('d-m-Y', strtotime($value->pkh_date)),
                    'pkh_title' => $value->pkh_title,
                    'name' => $value->name,
                ];
            }

            $data = [];

            if(!empty($results)) {
            
                for($i = 0; $i < count($results); $i++) {
    
                    $data[]     = array(
                        $i + 1,
                        $results[$i]['pkh_date'],
                        $results[$i]['pkh_title'],
                        $results[$i]['name'],
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

            return ResponseFormatter::success([  
                'proker_bulanan' => $proker_bulanan->pkb_title,
                'kegiatan' => $output,
                'pkb_uuid' => $pkb_uuid,
                'pkb_id' => $pkb_id
            ]);

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' => 'Gagal tampilkan data!'
            ]);
        }
    }

    public function getReportProgramPerMingguByBulan(Request $request)
    {
        try {

			$groupDivisionID = env('APP_GROUPDIV_MARKETING');

            $year  = request()->year;
            $month = request()->month;
            $week  = request()->week;


            // get program di proker bulanan per tahun per bulan
            $programs = ProkerBulanan::getProgramByDivisi($groupDivisionID,  $year , $month);
            $results = [];
            foreach($programs as $value){
                // Mulai dari awal bulan
                // $minggu1_start = Carbon::create($year, $month, 1)->startOfMonth()->startOfWeek(Carbon::MONDAY);
                // $minggu1_start = Carbon::create($year, $month, 1)->startOfMonth();
                // $minggu1_end = $minggu1_start->copy()->endOfWeek();

                //    // Hitung minggu berikutnya
                // $minggu2_start = $minggu1_end->copy()->addDay();
                // $minggu2_end   = $minggu2_start->copy()->endOfWeek();
                
                // $minggu3_start = $minggu2_end->copy()->addDay();
                // $minggu3_end = $minggu3_start->copy()->endOfWeek();
                
                // $minggu4_start = $minggu3_end->copy()->addDay();
                // $minggu4_end = $minggu4_start->copy()->endOfWeek();
                
                // $minggu5_start = $minggu4_end->copy()->addDay();
                // $minggu5_end = $minggu5_start->copy()->endOfWeek();

                //  // Pastikan minggu terakhir tidak melebihi akhir bulan
                //  $minggu5_end = $minggu5_end->endOfMonth();

                #get jenis pekerjaan di proker_bulanan_detail berdasarkan pkb_id nya
                $jenis_pekerjaan = ProkerHarian::getProkerHarianByProkerBulanan($value->uuid);

                // $weeks = DateFormat::getWeekStartEndDates($year, $month);


                // $minggu_1 = [];
                // $minggu_2 = [];
                // $minggu_3 = [];
                // $minggu_4 = [];
                // $minggu_5 = [];

                // get data pekerjaan harian berdasarkan program, tahun, bulan, dan minggu , hitung
                $aktivitas_harian_minggu_1 = ProkerHarian::getProkerHarianByProkerBulananPerMinggu($year, $month, $value->uuid,1);
                $aktivitas_harian_minggu_2 = ProkerHarian::getProkerHarianByProkerBulananPerMinggu($year, $month, $value->uuid,2);
                $aktivitas_harian_minggu_3 = ProkerHarian::getProkerHarianByProkerBulananPerMinggu($year, $month, $value->uuid,3);
                $aktivitas_harian_minggu_4 = ProkerHarian::getProkerHarianByProkerBulananPerMinggu($year, $month, $value->uuid,4);
                $aktivitas_harian_minggu_5 = ProkerHarian::getProkerHarianByProkerBulananPerMinggu($year, $month, $value->uuid,5);

                // foreach ($jenis_pekerjaan as $detail) {
                //     $detail_date = Carbon::parse($detail->pkh_date);

                //     $detail->pkh_date = date('d-m-Y', strtotime($detail->pkh_date));
            
                //     if ($detail_date->between($minggu1_start, $minggu1_end)) {
                //         $minggu_1[] = $detail;
                //     } elseif ($detail_date->between($minggu2_start, $minggu2_end)) {
                //         $minggu_2[] = $detail;
                //     } elseif ($detail_date->between($minggu3_start, $minggu3_end)) {
                //         $minggu_3[] = $detail;
                //     } elseif ($detail_date->between($minggu4_start, $minggu4_end)) {
                //         $minggu_4[] = $detail;
                //     } elseif ($detail_date->between($minggu5_start, $minggu5_end)) {
                //         $minggu_5[] = $detail;
                //     }
                // }

                // foreach ($jenis_pekerjaan as $detail) {
                //     $detail_date = new DateTime($detail->pkh_date);
                //     $detail->pkh_date = date('d-m-Y', strtotime($detail->pkh_date));
            
                //     if ($detail_date >= new DateTime($weeks[0]['start']) && $detail_date <= new DateTime($weeks[0]['end'])) {
                //         $minggu_1[] = $detail;
                //     } elseif (isset($weeks[1]) && $detail_date >= new DateTime($weeks[1]['start']) && $detail_date <= new DateTime($weeks[1]['end'])) {
                //         $minggu_2[] = $detail;
                //     } elseif (isset($weeks[2]) && $detail_date >= new DateTime($weeks[2]['start']) && $detail_date <= new DateTime($weeks[2]['end'])) {
                //         $minggu_3[] = $detail;
                //     } elseif (isset($weeks[3]) && $detail_date >= new DateTime($weeks[3]['start']) && $detail_date <= new DateTime($weeks[3]['end'])) {
                //         $minggu_4[] = $detail;
                //     } elseif (isset($weeks[4]) && $detail_date >= new DateTime($weeks[4]['start']) && $detail_date <= new DateTime($weeks[4]['end'])) {
                //         $minggu_5[] = $detail;
                //     }

                  
                // }
                        
                $results[] = [
                    'id' => $value->id,
                    'uuid' => $value->uuid,
                    'pkb_start_date' => $value->pkb_start_date,
                    'pkb_title' => $value->pkb_title,
                    'count_minggu_1' => count($aktivitas_harian_minggu_1),
                    'count_minggu_2' => count($aktivitas_harian_minggu_2),
                    'count_minggu_3' => count($aktivitas_harian_minggu_3),
                    'count_minggu_4' => count($aktivitas_harian_minggu_4),
                    'count_minggu_5' => count($aktivitas_harian_minggu_5),
                ];
            }

            $data = [];

            if(!empty($results)) {
            
                foreach($results as $i => $result){

                    $jml_per_proker_harian = $result['count_minggu_1'] + $result['count_minggu_2'] + $result['count_minggu_3'] + $result['count_minggu_4'] + $result['count_minggu_5'];

                    $data[]     = [
                        $i + 1,
                        $result['pkb_title'],
                        '<a href="#" class="btn btn-sm" data-pkbdid='.$result['id'].' data-minggu="1" data-uuid='.$result['uuid'].' >'.$result['count_minggu_1'].'</a>',
                        '<a href="#" class="btn btn-sm" data-pkbdid='.$result['id'].' data-minggu="2" data-uuid='.$result['uuid'].' >'.$result['count_minggu_2'].'</a>',
                        '<a href="#" class="btn btn-sm" data-pkbdid='.$result['id'].' data-minggu="3" data-uuid='.$result['uuid'].' >'.$result['count_minggu_3'].'</a>',
                        '<a href="#" class="btn btn-sm" data-pkbdid='.$result['id'].' data-minggu="4" data-uuid='.$result['uuid'].' >'.$result['count_minggu_4'].'</a>',
                        '<a href="#" class="btn btn-sm" data-pkbdid='.$result['id'].' data-minggu="5" data-uuid='.$result['uuid'].' >'.$result['count_minggu_5'].'</a>',
                        $jml_per_proker_harian
                    ];
            
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

            $jml_minggu_1 = collect($results)->sum(function($q){ return $q['count_minggu_1']; });
            $jml_minggu_2 = collect($results)->sum(function($q){ return $q['count_minggu_2']; });
            $jml_minggu_3 = collect($results)->sum(function($q){ return $q['count_minggu_3']; });
            $jml_minggu_4 = collect($results)->sum(function($q){ return $q['count_minggu_4']; });
            $jml_minggu_5 = collect($results)->sum(function($q){ return $q['count_minggu_5']; });

            $total_all_minggu = $jml_minggu_1 + $jml_minggu_2 + $jml_minggu_3 + $jml_minggu_4 + $jml_minggu_5;

            return ResponseFormatter::success([
               'bulan' =>  Months::monthName($month),
               'perminggu' => $output,
               'jml_minggu_1' => $jml_minggu_1,
               'jml_minggu_2' => $jml_minggu_2,
               'jml_minggu_3' => $jml_minggu_3,
               'jml_minggu_4' => $jml_minggu_4,
               'jml_minggu_5' => $jml_minggu_5,
               'total_all_minggu' => $total_all_minggu
            ]);

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' => 'Gagal tampilkan data!'
            ]);
        }
    }

    public function getDaftarKegitanHarianPerMinggu()
    {
        
        try {

            $year = request()->year;
            $month = request()->month;
            $pkb_uuid = request()->pkb_uuid;
            $week = request()->week;

            $proker_harian = ProkerHarian::getProkerHarianByProkerBulananPerMinggu($year, $month, $pkb_uuid,$week);

            $data = [];

            if(!empty($proker_harian)) {

                foreach($proker_harian as $i => $result){

                    $result->pkh_date = date('d-m-Y', strtotime($result->pkh_date));

                    $data[]     = [
                        $i + 1,
                        $result->pkh_date,
                        $result->pkbd_type,
                        $result->pkh_title,
                        $result->name,
                    ];
            
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

            return ResponseFormatter::success([
                'proker_harian' => $output
             ]);

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' => 'Gagal ambil data!'
            ]);
        }
    }

    public function getSasaran(Request $request)
    {
        try {

            $groupDivisionID = env('APP_GROUPDIV_MARKETING');

            $sasaran = ProkerBulanan::getSasaranMarketing($groupDivisionID);

            if ($request->has('q')) {
                $search = $request->q;
                $sasaran = ProkerBulanan::getSasaranSearchMarketing($groupDivisionID, $search);
            }
            
            return response()->json($sasaran);

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' => 'Gagal ambil data!'
            ]);
        }
    }

    public function getProgramKerjaBulananBySasaran(Request $request)
    {
        try {
            
            $fn  = new NumberFormat();

           $groupDivisionID = env('APP_GROUPDIV_MARKETING');

           $pkb_pkt_id = request()->idSasaran;
           $year       = request()->year;

            // get data program di proker bulanan by tahunan detail id
            $sasaran_umum = ProkerBulanan::getSasaranUmum($year, $pkb_pkt_id);
            $res_sasaran_umum = [];

            foreach ($sasaran_umum as $key => $value) {

                $programs = ProkerBulanan::getProgramByDivisi($groupDivisionID,  $year , $value->bulan);
                $res_programs = [];

                foreach($programs as $program){

                    // get jenis pekerjaan nya, dari table proker bulanan detail 
                    $jenis_pekerjaan = ProkerBulanan::getJenisPekerjaan($program->id);
                    // jumlahkan target per program
                    $sum_target = collect($jenis_pekerjaan)->sum(function($q){
                        return $q->pkbd_num_target;
                    });

                    $sum_hasil = collect($jenis_pekerjaan)->sum(function($q){
                        return $q->pkbd_num_result;
                    });

                    $res_programs[] = [
                        'sum_target' => $sum_target,
                        'sum_hasil' => $sum_hasil,
                    ];
                }

                $sum_target = collect($res_programs)->sum(function($q){
                    return $q['sum_target'];
                });

                // sum hasil
                $sum_hasil = collect($res_programs)->sum(function($q){
                    return $q['sum_hasil'];
                });

                // persentase
                $persentage_pencapaian_progam = $fn->persentage($sum_hasil,$sum_target);
                if ($persentage_pencapaian_progam !== null) {
                        $persentage_pencapaian_progam  = $fn->persen($persentage_pencapaian_progam);  
                }
                
                // get total pencapaian jamaah per bulan by tahun
                $umrah = MarketingTarget::getRealisasiUmrahPerBulanByTahun($year, $value->bulan);

                $res_sasaran_umum[] = [
                    'month_number' => $value->bulan,
                    'month_name' => Months::monthName($value->bulan),
                    'pencapaian_umrah' =>  $umrah->pencapaian,
                    'target_umrah' =>  $umrah->target,
                    'selisih_umrah' =>  $umrah->selisih,
                    'persentage_pencapaian_progam' =>  $persentage_pencapaian_progam,
                    'res_programs' => $res_programs
                ];
            }
            
            $data = [];

            if(!empty($res_sasaran_umum)) {
                foreach($res_sasaran_umum as $i => $result){

                     // persentase
                $persentage_pencapaian_umrah = $fn->persentage($result['pencapaian_umrah'],$result['target_umrah']);
                if ($persentage_pencapaian_umrah !== null) {
                        $persentage_pencapaian_umrah  = $fn->persen($persentage_pencapaian_umrah);  
                }

                    $data[]     = [
                        $i + 1,
                        '<a href="#" data-year='.$year.' data-month='.$result['month_number'].' onclick="showMonth(this)">'.$result['month_name'].'</a>',
                        $result['persentage_pencapaian_progam'].' %',
                        $result['pencapaian_umrah'],
                        $result['target_umrah'],
                        $result['selisih_umrah'],
                        $persentage_pencapaian_umrah.' %',
                    ];
            
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

            
            
            return ResponseFormatter::success([
                'sasaran_umum' => $output,
                'res_sasaran_umum' => $res_sasaran_umum
            ]);

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' => 'Gagal ambil data!'
            ]);
        }
    }

    public function getReportEvaluasiMarketing(Request $request)
	{
        try {
			
			$groupDivisionID = env('APP_GROUPDIV_MARKETING');

            $fn  = new NumberFormat();

			
			$year   = request()->year;
			$month  = request()->month;
			$week   = request()->week;

            $programs = ProkerBulanan::getProgramByDivisi($groupDivisionID,  $year , $month);


            $results = [];
            foreach($programs as $value){

                // get jenis pekerjaan nya, dari table proker bulanan detail 
                $jenis_pekerjaan = ProkerBulanan::getJenisPekerjaan($value->id);
                // jumlahkan target per program
                $sum_target = collect($jenis_pekerjaan)->sum(function($q){
                    return $q->pkbd_num_target;
                });

                $sum_hasil = collect($jenis_pekerjaan)->sum(function($q){
                    return $q->pkbd_num_result;
                });
               
                // // persentase per program 
                $persentage_pencapaian_progam = $fn->persentage($sum_hasil,$sum_target);
                if ($persentage_pencapaian_progam !== null) {
                        $persentage_pencapaian_progam  = $fn->persen($persentage_pencapaian_progam);  
                }

                $results[] = [
                    'id' => $value->id,
                    'uuid' => $value->uuid,
                    'pkb_start_date' => $value->pkb_start_date,
                    'pkb_title' => $value->program,
                    'jenis_pekerjaan' => $jenis_pekerjaan,
                    'count_jenis_pekerjaan' => count($jenis_pekerjaan),
                    'target' => $sum_target,
                    'hasil' => $sum_hasil,
                    // 'sum_hasil'  => $sum_hasil,
                    'persentage_pencapaian_progam' => $persentage_pencapaian_progam
                ];
            }



            if(!empty($results)) {
            
                foreach($results as $i => $result) {

                    $data[]     = array(
                        $i + 1,
                        $result['pkb_title'],
                        $result['persentage_pencapaian_progam'].' %',
                        '<a href="#" data-title='.$result['pkb_title'].' data-jenispekerjaan=\''.htmlspecialchars(json_encode($result['jenis_pekerjaan'])).'\' onclick="showJenisPekerjaan(this)">'.$result['hasil'].'</a>',
                        $result['target'],
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

             // total_target 
             $total_target = collect($results)->sum(function($q){
                return $q['target'];
            });

            $total_hasil = collect($results)->sum(function($q){
                return $q['hasil'];
            });

            // jika target 0, maka hasil persentase jadikan 0 %

            $persentage_total_pencapaian_progam = $fn->persentage($total_hasil,$total_target);
                if ($persentage_total_pencapaian_progam !== null) {
                        $persentage_total_pencapaian_progam  = $fn->persen($persentage_total_pencapaian_progam);  
                }
            // if ($total_target != 0) {

            // }else{

            //     $persentage_total_pencapaian_progam = "Target tidak ditentukan";
            // }
    
            return ResponseFormatter::success([
                'bulan' =>  Months::monthName($month),  
				'rencanakerja'  => $output,
                'total_target' => $total_target,
                'total_hasil' => $total_hasil,
                'persentage_total_pencapaian_progam' =>  $persentage_total_pencapaian_progam
            ]);

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' => 'Gagal tampilkan data!'
            ]);
        }
	}

    public function getAktivitasHarianByJenisPekerjaan()
    {
        try {

            #id proker bulanan detail 
            $pkbd_id = request()->pkbd_id; 

            $jenis_pekerjaan = ProkerBulanan::getProkerBulananDetailByid($pkbd_id);

            #get data aktivitas harian nya
            $proker_harian = ProkerHarian::getProkerHarianByProkerBulananAndBulananDetail($jenis_pekerjaan->uuid, $pkbd_id);

            $data = [];
            if(!empty($proker_harian)) {
            
                foreach($proker_harian as $i => $result) {

                    $data[]     = array(
                        $i + 1,
                        date('d-m-Y', strtotime($result->pkh_date)),
                        $result->pkh_title,
                        $result->name,
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

            
            return ResponseFormatter::success([
                    'jenis_pekerjaan' => $jenis_pekerjaan->pkbd_type,
                    'proker_harian' => $output,
                    'message' => 'Berhasil menampilkan data !'
                ]);

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' => 'Gagal ambil data!'
            ]);
        }

    }

    public function countingNumResult()
    {

        try {
            
            $groupDivisionID = env('APP_GROUPDIV_MARKETING');
            $year  = request()->year;
            $month = request()->month;
            
            // COUNTING REALISASI JENIS PEKERJAAN (TB PROKER BULANAN DETAIL) DENGAN MMENGHITUNG DARI AKTIVITAS HARIAN NYA
            // DATA BULAN JULI / 7

            // get data program by divisi marketing 
            $programs = ProkerBulanan::getProgramByDivisiNew($groupDivisionID,  $year , $month);
            
            $results  = [];
            foreach ($programs as $key => $value) {
                // get jenis pekerjaan nya di bulanan detail 
                $jenis_pekerjaan = ProkerBulanan::getJenisPekerjaan($value->id);
                
                
                $res_jenis_pekerjaan = [];
                foreach ($jenis_pekerjaan as $key_jenis => $jenis) {
                    
                    // mapping jenis pekerjaan untuk hitung realisasi, realisasi di dapat dari aktivitas harian nya
                    // get data where pkh_pkb_id bulan, dan detail bulan nya
                    $aktivitas_harian = ProkerHarian::getProkerHarianByProkerBulananAndBulananDetail($value->uuid, $jenis->id);

                    // Ambil pkb_id_update dari objek jenis pekerjaan  pertama, menyetel pkbd_id_update isinya adalah pkb_id objek pertama
                    
                    $res_jenis_pekerjaan[] = [
                        'id' => $jenis->id, 
                        'pkb_id' => $jenis->pkb_id, 
                        'pkb_id_update' => $value->first_id_program, 
                        'pkbd_type' => $jenis->pkbd_type,
                        'pkbd_num_target' => $jenis->pkbd_num_target,
                        'pkbd_num_result' => $jenis->pkbd_num_result,
                        'count_aktivitas_harian' => count($aktivitas_harian),
                        'aktivitas_harian' => $aktivitas_harian
                    ];

                    // update pkbd_num_result = $count_aktivitas_harian, menghitung jumlah kegiatan per jenis pekerjaan
                    DB::table('proker_bulanan_detail')->where('id', $jenis->id)->update([
                        'pkbd_num_result' => count($aktivitas_harian)
                    ]);

                    // UPDATE pkb_id dengan pkb_id yang pertama, dimaksudkan untuk mengelompokan satu program saja 
                    DB::table('proker_bulanan_detail')->where('id', $jenis->id)->update([
                        'pkb_id' => $value->first_id_program
                    ]);

                }

                $results[] = [
                    'id' => $value->id,
                    'uuid' => $value->uuid,
                    'pkb_start_date' => $value->pkb_start_date,
                    'progam' => $value->program,
                    'first_id_program' => $value->first_id_program, 
                    'jenis_pekerjaan' => $res_jenis_pekerjaan
                ];
            }

            return ResponseFormatter::success([
                'count_program' => count($results),
                'program' => $results,
                'message' => 'Berhasil counting data !'
            ]);

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' => 'Gagal counting data!'
            ]);
        }

    }

    public function generateDumyData()
    {
        try {
            
            $data2 = [];

            return ResponseFormatter::success([
                    'results' => $data2,
                    'message' => 'Berhasil generate dumy data !'
                ]);

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' => 'Gagal ambil data!'
            ]);
        }

    }

}
