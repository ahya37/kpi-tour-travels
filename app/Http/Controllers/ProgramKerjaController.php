<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BaseService;
use App\Services\ProgramKerjaService;
use Illuminate\Support\Facades\Validator;
use Response;

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
            'sub_title' => 'Dashboard Program Kerja Tahunan'
        ];

        return view('master/programKerja/tahunan/index', $data);
    }

    public function ambilDataProkerTahunan($id)
    {
        $getData    = ProgramKerjaService::getDataProkerTahunan($id);
        if(!empty($getData)) {
            for($i = 0; $i < count($getData); $i++) {
                $data[]     = array(
                    $i + 1,
                    $getData[$i]->pkt_title,
                    $getData[$i]->pkt_year,
                    $getData[$i]->total_program,
                    "<button type='button' class='btn btn-sm btn-primary' value='" . $getData[$i]->uid . "' title='Edit Program Kerja'><i class='fa fa-edit'></i></button>"
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

    public function simpanDataProkerTahunan($jenis, Request $request)
    {
        if($jenis == 'add') {
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
                $doSimpan   = ProgramKerjaService::doSimpanProkerTahunan($request->all()['sendData']);
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
    }
    // BULANAN
    public function indexBulanan()
    {
        $data   = [
            'title'     => 'Master Program Kerja',
            'sub_title' => 'Dashboard Program Kerja Bulanan'
        ];

        return view('master/programKerja/bulanan/index', $data);
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
