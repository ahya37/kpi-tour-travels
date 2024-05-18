<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SubDivisionService;
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
                $btnEdit        = "<button type='button' class='btn btn-sm btn-primary' value='".$subID."' onclick='show_modal(`modalSubDivisionEdit`, this.value)' title='Ubah Data'><i class='fa fa-edit'></i></button>";
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

    public function getDataGroupDivision(Request $request)
    {
        if(count($request->all()) < 2) {
            $cari   = '%';
        } else {
            $cari   = $request->all()['cari'];
        }

        $get_data   = SubDivisionService::getDataGroupDivision($cari);
        
        return $get_data;
    }

    public function saveDataSubDivision(Request $request)
    {
        // VALIDATED
        $validator  = Validator::make($request->all(),['gdID'   => 'required', 'sdName' => 'required']);
        if($validator->fails()) {
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray(),
            ), 400);
        } else {
            $data           = [
                "gdID"      => $request->all()['gdID'],
                "sdName"    => $request->all()['sdName'],
            ];
            $sendData   = SubDivisionService::postDataSubDivisionNew($data);
            // $sendData       = 'berhasil';
            if($sendData == 'berhasil') {
                return Response::json(array(
                    'success'   => true,
                    'status'    => 200,
                    'alert'     => [
                        'icon'  => 'success',
                        'message'   => [
                            'title' => 'Berhasil',
                            'text'  => 'Berhasil Menyimpan Data Sub Division'
                        ],
                    ],
                ), 200);
            } else {
                return Response::json(array(
                    'success'   => false,
                    'status'    => 500,
                    'alert'     => [
                        'icon'      => 'error',
                        'message'   => [
                            'title' => 'Terjadi Kesalahan',
                            'text'  => 'Sistem Sedang Gangguan, Silahkan Tunggu beberapa saat..',
                        ],
                    ]
                ), 500);
            }
        }
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
        $value  = array(
            "id_sub"    => $request->all()['idSub'],
        );

        $getData    = $this->getDataSubDivisionAll($value['id_sub']);
        
        if(!empty($getData))
        {
            return Response::json(array(
                'success'   => true,
                'status'    => 200,
                'message'   => [
                    'title' => 'Berhasil',
                    'text'  => 'Berhasil Mengambil Data'
                ],
                'data'      => $getData,
            ), 200);
        } else {
            return Response::json(array(
                'success'   => true,
                'status'    => 500,
                'message'   => [
                    'title' => 'Terjadi Kesalahan',
                    'text'  => 'Gagal Mengambil Data',
                ],
                'data'      => [],
            ), 500);
        }
    }

    public function saveEditDataSubDivision(Request $request)
    {
        $rules  = [
            'gdID'      => 'required',
            'sdID'      => 'required',
            'sdName'    => 'required',
        ];
        $validator  = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            return Response::json([
                'success'   => false,
                'status'    => 500,
                'alert'     => [
                    'icon'      => 'error',
                    'message'   => [
                        'title' => 'Terjadi Kesalahan',
                        'text'  => 'Data Tidak Disimpan, silahkan cek kembali inputan..',
                    ],
                ],
            ], 500);
        } else {
            $dataSimpan       = $request->all();
            $doSimpan       = SubDivisionService::postDataSubDivisionEdit($dataSimpan);
            if($doSimpan == 'berhasil') {
                $output     = [
                    'success'   => true,
                    'status'    => 200,
                    'alert'     => [
                        'icon'      => 'success',
                        'message'   => [
                            'title' => 'Berhasil',
                            'text'  => 'Data Berhasil Disimpan'
                        ],
                    ],
                ];
            } else {
                $output     = [
                    'success'   => false,
                    'status'    => 500,
                    'alert'     => [
                        'icon'  => 'error',
                        'message'   => [
                            'title' => 'Terjadi Kesalahan',
                            'text'  => 'Data Gagal Disimpan',
                        ],
                    ],
                ];
            }

            return Response::json($output, $output['status']);
        }
        
    }
}
