<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SubDivisionService;
use App\Services\BaseService;
use Illuminate\Support\Facades\Validator;
use Response;

class SubDivisionController extends Controller
{
    //

    public function index()
    {
        $data    = array(
            'title'     => 'Master Sub Division',
            'sub_title' => 'List Of Sub Division',
        );

        return view('master/subDivision/index', $data);
    }

    public function getDataTableSubDivision(Request $request)
    {
        $value  = [
            'keyword'   => $request->all()['cari'],
        ];

        $getData   = SubDivisionService::dataSubDivision($value);
        if(!empty($getData)) {
            for($i = 0; $i < count($getData); $i++) {
                $subID          = $getData[$i]->sub_division_id;
                $subName        = $getData[$i]->sub_division_name;
                $groupName      = $getData[$i]->group_division_name;
                $subCreatedAt   = $getData[$i]->created_at;
                $btnEdit        = "<button type='button' class='btn btn-sm btn-primary' value='".$subID."' onclick='show_modal(`modalForm`, `edit`, this.value)' title='Ubah Data'><i class='fa fa-edit'></i></button>";
                $btnDelete      = "<button type='button' class='btn btn-sm btn-danger' value='".$subID."' onclick='show_modal(`modalSubDivisionDelete`, this.value)' title='Hapus Data'><i class='fa fa-trash'></i></button>";
                $data[]     = array(
                    $i + 1,
                    $subName,
                    $groupName,
                    $subCreatedAt,
                    $btnEdit,
                );
            }
        } else {
            $data   = [];
        }

        $output  = array(
            "draw"      => 1,
            "data"      => $data,
        );

        return Response::json($output, 200);
    }

    public function simpanDataSubDivision($jenis, Request $request)
    {
        $rules          = [
            "groupDivisionID"   => 'required',
            "subDivisionName"   => 'required',
        ];
        $doValidate     = Validator::make($request->all()['sendData'], $rules);

        if($doValidate->fails()) {
            $output     = array(
                "success"   => false,
                "status"    => 400,
                "alert"     => [
                    "icon"  => 'error',
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => "Coba cek kembali form penginputan",
                        "errMsg"    => $doValidate->getMessageBag(),
                    ],
                ]
            );
        } else {
            $doSimpan   = SubDivisionService::doSimpanDataSubDivision($request->all()['sendData'], $jenis);
            if($doSimpan['status'] == 'berhasil') {
                $output     = array(
                    "success"   => true,
                    "status"    => 200,
                    "alert"     => [
                        "icon"      => "success",
                        "message"   => [
                            "title"     => "Berhasil",
                            "text"      => $jenis == 'add' ? "Berhasil Menyimpan Data Sub Division" : "Berhasil Merubah Data Sub Division",
                            "errMsg"    => null,
                        ],
                    ],
                );
            } else if($doSimpan['status'] == 'gagal') {
                $output     = array(
                    "success"   => false,
                    "status"    => 500,
                    "alert"     => [
                        "icon"      => "error",
                        "message"   => [
                            "title"     => "Terjadi Kesalahan",
                            "text"      => "Sistem sedang gangguan, silahkan tunggu dan coba kembali..",
                            "errMsg"    => $doSimpan['errMsg'],
                        ],
                    ],
                );
            }
        }

        return Response::json($output, $output['status']);
    }

    public function getDataGroupDivision()
    {
        $get_data   = BaseService::getDataGroupDivision();
        // var_dump($get_data);die();
        if(!empty($get_data)) {
            for($i = 0; $i < count($get_data); $i++) {
                $data[]     = array(
                    "groupDivisionID"   => $get_data[$i]->id,
                    "groupDivisionName" => $get_data[$i]->name,
                );
            }

            $output     = array(
                "success"   => true,
                "status"    => 200,
                "data"      => $data,
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

    private function getDataSubDivisionAll($cari)
    {
        $data   = [
            'keyword'   => $cari
        ];
        return SubDivisionService::dataSubDivision($data);
    }

    public function getDataSubDivision(Request $request)
    {
        $getData    = $this->getDataSubDivisionAll($request->all()['sendData']['subDivisionID']);
        
        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Data Berhasil Diambil",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "message"   => "Data Gagal Ditemukan",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);        
    }
}
