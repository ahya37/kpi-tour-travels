<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BaseService;
use App\Services\ProgramKerjaService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Response;

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

    // TAHUNAN
    public function indexTahunan()
    {
        $data   = [
            'title'     => 'Master Program Kerja',
            'sub_title' => 'List Program Kerja - Tahunan'
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
            "uid"               => request()->id,
            "groupDivisionID"   => $request->all()['groupDivisionID'],
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
            $doSimpan   = ProgramKerjaService::doSimpanProkerTahunan($request->all()['sendData'], $jenis);
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
            'sub_title' => 'Program Kerja Bulanan'
        ];

        return view('master/programKerja/bulanan/index', $data);
    }

    public function getProkerBulananAll(Request $request)
    {
        $getData    = ProgramKerjaService::getProkerBulananAll($request->all()['sendData']['cari']);
        
        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "data"      => [
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
        $doSimpan   = ProgramKerjaService::doSimpanProkerBulanan($request->all()['sendData']);
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
                "success"       => gagal,
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

    // HARIAN
    public function indexHarian()
    {
        $data   = [
            'title'     => 'Master Program Kerja',
            'sub_title' => 'Dashboard Program Kerja Harian'
        ];

        return view('master/programKerja/harian/index', $data);
    }

    public function testUpload(Request $request)
    {
        // // nama file
        // echo 'File Name: '.$file->getClientOriginalName();
        // echo '<br>';

        //         // ekstensi file
        // echo 'File Extension: '.$file->getClientOriginalExtension();
        // echo '<br>';

        //         // real path
        // echo 'File Real Path: '.$file->getRealPath();
        // echo '<br>';

        //         // ukuran file
        // echo 'File Size: '.$file->getSize();
        // echo '<br>';

        //         // tipe mime
        // echo 'File Mime Type: '.$file->getMimeType();

        // menyimpan data file yang diupload ke variabel $file
		$file = $request->file('file');
        // $namaFile       = time()."_".$file->getClientOriginalName();
        // $tujuanUpload   = 'storage/data-files';
        // $path           = $tujuanUpload."/".$namaFile;
        $path       = $file->getClientOriginalName();
        // $file->move($tujuanUpload, $namaFile);

        return $path;
    }

    public function dataProkerBulanan(Request $request)
    {
        $sendData   = [
            "rolesName"     => Auth::user()->getRoleNames()[0] == 'admin' ? '%' : Auth::user()->getRoleNames()[0],
            "currentDate"   => date('Y-m-d'),
            "pkb_uuid"      => $request->all()['sendData']['pkb_uuid'],
        ];
        $getData    = ProgramKerjaService::getProkerBulanan($sendData);
        if(!empty($getData))
        {
            $header     = [];
            $detail     = [];

            // PISAHIN ARRAY
            $keyArray   = [];
            $tempArray  = [];
            for($i = 0; $i < count($getData); $i++) {
                if(!in_array($getData[$i]->pkb_uuid, $keyArray)) {
                    $keyArray[$i]   = $getData[$i]->pkb_uuid;
                    $tempArray[]  = $getData[$i];
                }
            }

            // INSERT KE HEADER
            // print("<pre>".print_r($tempArray, true)."</pre>");die();
            for($j = 0; $j < count($tempArray); $j++) {
                $header[]   = array(
                    "pkb_uuid"  => $tempArray[$j]->pkb_uuid,
                    "pkb_title" => $tempArray[$j]->pkb_title,
                    "pkb_date"  => $tempArray[$j]->pkb_date,
                );
            }

            // INSERT KE DETAIL
            for($k = 0; $k < count($getData); $k++) {
                $detail[]   = array(
                    "pkb_detail"=> $getData[$k]->pkb_type_detail,
                );
            }

            $output     = array(
                "success"   => true,
                "status"    => 200,
                "data"      => [
                    "header"    => $header,
                    "detail"    => $sendData['pkb_uuid'] == '%' ? [] : $detail
                ],
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "data"      => [
                    "header"    => [],
                    "detail"    => [],
                ],
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
}
