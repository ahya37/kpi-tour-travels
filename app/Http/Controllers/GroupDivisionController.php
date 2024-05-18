<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GroupDivisionService;
use App\Http\Requests\GroupDivisionRequest;

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
        
        echo json_encode($output);
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

        echo json_encode($output);
    }

    public function modalGetDataGroupDivisions($cari)
    {
        $data   = $this->getDataGroupDivisions($cari);

        if(!empty($data)) {
            $output     = array(
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
                "status"        => 500,
                "message"       => "Terjadi Kesalahan",
                "description"   => "Data Gagal Dimuat",
                "data"          => []
            );
        }

        echo json_encode($output);
    }

    public function storeDataEditGroupDivisions(GroupDivisionRequest $request, $id)
    {
        $data   = array(
            "gdID"      => $id,
            "gdName"    => $request->validated()['group_division_name'],
        );

        $doUpdate   = GroupDivisionService::doUpdateGroupDivisions($data);

        if($doUpdate == 'berhasil') {
            $output     = array(
                "status"        => 200,
                "message"       => "Berhasil",
                "description"   => "Berhasil Update Data Group Division",
            );
        } else {
            $output     = array(
                "status"        => 500,
                "message"       => "Terjadi Kesalahan",
                "description"   => "Gagal Update Data Group Division"

            );
        }

        echo json_encode($output);
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
