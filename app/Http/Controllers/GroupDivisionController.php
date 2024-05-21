<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GroupDivisionService;
use App\Http\Requests\GroupDivisionRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class GroupDivisionController extends Controller
{
    public function index() {
        $data   = [
            'title'     => 'Master Group Division',
            'sub_title' => 'List of Group Division',
        ];
        

        return view('/master/groupDivision/index', $data);
    }

    private function getDataGroupDivisions($cari)
    {
        $get_data   = GroupDivisionService::ListGroupDivision($cari);
        return $get_data;
    }

    public function tableGroupDivision($cari)
    {
        $draw   = "1";

        $get_data   = $this->getDataGroupDivisions($cari);
        
        if(!empty($get_data)) {
            for($i = 0; $i < count($get_data); $i++) {
                $data[]     = array(
                    $i + 1,
                    $get_data[$i]->name,
                    $get_data[$i]->created_at,
                    "<button type='text' class='btn btn-sm btn-primary' value='".$get_data[$i]->id."' onclick='show_modal(`modal_edit_division`, this.value)' title='Edit Data'><i class='fa fa-edit'></i></button>&nbsp<button type='text' class='btn btn-danger btn-sm' value='".$get_data[$i]->id."' onclick='show_modal(`modal_hapus_data`, this.value)' title='Hapus Data'><i class='fa fa-trash'></i></button>",
                );
            }
        } else {
            $data   = [];
        }

        $output     = array(
            "draw"  => $draw,
            "data"  => $data,
        );
        return Response::json($output, 200);
    }

    public function storeDataGroupDivision(Request $request)
    {
        $request->validate(['group_division_name'   => 'required']);
        $data_simpan    = GroupDivisionService::doSimpanGroupDivisions($request->all());

        if($data_simpan == 'berhasil') {
            $output     = array(
                "status"    => 200,
                "message"   => "Berhasil",
                "description"   => "Berhasil Menambahkan Group Division Baru",
            );
        } else {
            $output     = array(
                "status"    => 500,
                "message"   => "Terjadi Kesalahan",
                "description"   => "Sistem sedang gangguan, silahkan tunggu beberapa saat.."
            );
        }

        return Response::json($output, $output['status']);
    }

    public function modalGetDataGroupDivisions($cari)
    {
        $data   = $this->getDataGroupDivisions($cari);

        if(!empty($data)) {
            $output     = array(
                "success"       => true,
                "status"        => 200,
                "message"       => "Berhasil Diambil",
                "description"   => "Data Berhasil Dimuat",
                "data"          => [
                    "gdID"      => $data[0]->id,
                    "gdName"    => $data[0]->name,
                ]
            );
        } else {
            $output     = array(
                "success"       => false,
                "status"        => 500,
                "message"       => "Terjadi Kesalahan",
                "description"   => "Data Gagal Dimuat",
                "data"          => []
            );
        }
        return Response::json($output, $output['status']);
    }

    public function storeDataEditGroupDivisions(Request $request)
    {
        $rules  = [
            "group_division_name"   => "required",
            "group_division_id"     => "required",
        ];
        $validator  = Validator::make($request->all()['dataSimpan'], $rules);

        if($validator->fails()) {
            $output     = array(
                'success'   => false,
                'status'    => 404,
                'alert'     => [
                    'icon'  => 'error',
                    'message'   => [
                        'title'     => 'Terjadi Kesalahan',
                        'text'      => 'Silahkan cek kembali inputan yang ada',
                        'errMsg'    => $validator->getMessageBag()->toArray(),
                    ],
                ],  
            );
        } else {
            $dataUpdate = [
                "gdID"      => $request->all()['dataSimpan']['group_division_id'],
                "gdName"    => $request->all()['dataSimpan']['group_division_name'],
            ];
            $doSimpan   = GroupDivisionService::doUpdateGroupDivisions($dataUpdate);
            if($doSimpan == 'berhasil') {
                $output     = array(
                    'success'       => true,
                    'status'        => 200,
                    'alert'         => [
                        'icon'      => 'success',
                        'message'   => [
                            'title' => 'Berhasil',
                            'text'  => 'Berhasil Update Data Grup Divisi',
                        ],
                    ],
                );
            } else {
                $output     = array(
                    'success'       => false,
                    'status'        => 500,
                    'alert'         => [
                        'icon'      => 'error',
                        'message'   => [
                            'title' => 'Terjadi Kesalahan',
                            'text'  => 'Gagal Update Data Grup Divisi',
                        ],
                    ],
                );
            }
            
            return Response::json($output, $output['status']);
        }
    }

    public function deleteDataGroupDivisions($id)
    {
        $data   = array(
            "gdID"  => $id,
        );

        $doDelete   = GroupDivisionService::doDeleteGroupDivisions($data);

        if($doDelete    == 'berhasil') {
            $output     = array(
                "status"    => "200",
                "message"   => "Berhasil"
            );
        } else {
            $output     = array(
                "status"    => 500,
                "message"   => "Terjadi Kesalahan",
            );
        }

        echo json_encode($output);
    }
}
