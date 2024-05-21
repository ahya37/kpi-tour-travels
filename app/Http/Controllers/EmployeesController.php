<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\EmployeeService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class EmployeesController extends Controller
{
    public function index() {
        $data   = [
            'title'     => 'Master Employees',
            'sub_title' => 'List of Master Employees',
            'is_active' => '1',
        ];
        return view('master/employees/index', $data);
    }

    private function getDataEmployeeAll($cari)
    {
        return EmployeeService::getDataEmployee($cari);
    }

    public function getDataTableEmployee(Request $request)
    {
        $getData    = $this->getDataEmployeeAll($request->all()['cari']);
        if(!empty($getData)) {
            for($i = 0; $i < count($getData); $i++)
            {
                $data[]     = array(
                    $i + 1,
                    $getData[$i]->employee_name,
                    $getData[$i]->group_division_name." (".$getData[$i]->sub_division_name.")",
                    "<button type='button' class='btn btn-sm btn-primary' title='Info'><i class='fa fa-info-circle'></i></button>"
                );
            }
        } else {
            $data   = [];
        }
        $output     = array(
            "draw"  => "1",
            "data"  => $data,
        );

        return Response::json($output, 200);
    }

    public function getDataDivisionGlobal($cari)
    {
        $getData    = EmployeeService::ambilDataDivisionGlobal($cari);
        
        if(!empty($getData))
        {
            $output     = array(
                'success'   => true,
                'status'    => 200,
                'alert'     => [
                    'icon'      => 'success',
                    'message'   => [
                        'title' => 'Berhasil',
                        'text'  => 'Berhasil Mengambil Data Divisi',
                    ],
                ],
                'data'      => $getData,
            );
        } else {
            $output     = array(
                'success'       => false,
                'status'        => 500,
                'alert'         => [
                    'icon'      => 'error',
                    'message'   => [
                        'title' => 'Terjadi Kesalahan',
                        'text'  => 'Gagal Mengambil Data Divisi'
                    ],
                ],
                'data'          => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function saveDataEmployee(Request $request)
    {
        $rules  = [
            "empNama"   => 'required',
            "empGDID"   => 'required',
            "empRole"   => 'required'
        ];
        $validator  = Validator::make($request->all()['sendData'], $rules);

        if($validator->fails()) {
            $output     = array(
                'success'   => false,
                'status'    => 500,
                'alert'     => [
                    'icon'  => 'error',
                    'message'   => [
                        'title' => 'Terjadi Kesalahan',
                        'text'  => 'Data Gagal Disimpan',
                        'errMsg'=> $validator->getMessageBag()->toArray()
                    ],
                ],
            );
        } else {
            $simpanData     = EmployeeService::doSaveDataEmployee($request->all()['sendData']);
            if($simpanData == 'berhasil') {
                $output     = array(
                    'success'   => true,
                    'status'    => 200,
                    'alert'     => [
                        'icon'      => 'success',
                        'message'   => [
                            'title'     => 'Berhasil',
                            'text'      => 'Berhasil Menambahkan Employee Baru',
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
                            'text'  => 'Sistem sednag gangguan, silahkan tunggu dan coba lagi..'
                        ],
                    ],
                );
            }
        }

        return Response::json($output, $output['status']);

    }

    public function getDataRoles()
    {
        $getData    = EmployeeService::getData('roles','%');

        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil",
                "description"   => "Berhasil Ambil Data",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "message"   => "Terjadi Kesalahan",
                "description"   => "Gagal ambil data",
                "data"          => [],
            );
        }

        return Response::json($output, $output['status']);
    }
}
