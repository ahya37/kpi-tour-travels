<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\EmployeeService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Auth;
use Illuminate\Auth\Events\Validated;

class EmployeesController extends Controller
{
    public function index() {
        $data   = [
            'title'     => 'ERP Percik Tours | List System User',
            'sub_title' => 'List System User',
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
                    "<button type='button' class='btn btn-sm btn-primary' title='Info' value=".$getData[$i]->employee_id." onclick='show_modal(`modal_form_employees`, `edit`, this.value)'><i class='fa fa-info-circle'></i></button>"
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

    public function getDataEmployeesDetail(Request $request)
    {
        $idEmployee     = $request->all()['sendData']['idEmployee'];
        $getData        = $this->getDataEmployeeAll($idEmployee);

        if(!empty($getData)) {
            $data   = [
                "employee_id"           => $getData[0]->employee_id,
                "employee_name"         => $getData[0]->employee_name,
                "group_division_id"     => $getData[0]->group_division_id,
                "sub_division_id"       => $getData[0]->sub_division_id,
                "roles_id"              => $getData[0]->role_id,
                "roles_name"            => $getData[0]->role_name,
                "employee_email"        => $getData[0]->employee_email,
            ];

            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Ambil Data Employees",
                "data"      => $data,
            );
        } else {
            $data   = [];
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "message"   => "Data Employee Gagal Diambil",
                "data"      => $data,
            );
        }

        return Response::json($output, $output['status']);
    }

    public function getDataDivisionGlobal()
    {
        $cari       = request()->sendData; 
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

    public function saveDataEmployee(Request $request, $jenis)
    {
        if($jenis == 'add') {
            $rules  = [
                'employee_name'             => 'required',
                'employee_group_division'   => 'required',
                'employee_role'             => 'required',
            ];
        } else {
            $rules  = [];
        }
        $validator  = Validator::make($request->all()['sendData'], $rules);

        if($validator->fails()) {
            $output     = [
                'success'   => false,
                'status'    => 422,
                'message'   => 'Periksa Kembali Inputan',
                'data'      => $validator->errors(),
            ];
        } else {
            $send_data  = [
                'data'      => $request->all()['sendData'],
                'user_id'   => Auth::user()->id,
                'ip_address'=> $request->ip(),
            ];

            $do_simpan  = EmployeeService::doSaveDataEmployee($send_data, $jenis);

            $output     = [
                'success'   => $do_simpan['is_success'],
                'status'    => $do_simpan['status_code'],
                'data'      => $do_simpan['data'],
                'message'   => $do_simpan['message'],
            ];
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

    public function data_employee_global()
    {
        $getData    = EmployeeService::do_get_data_employee_global();

        $output     = [
            "status"    => 200,
            "success"   => true,
            "message"   => "Berhasil Mengambil Data Employee",
            "data"      => $getData,
        ];

        return Response::json($output, $output['status']);
    }

    // 28 FEBRUARI 2025
    // NOTE : GET DATA EMPLOYEES ALL W/ OPTION
    public function getDataEmployee(Request $request)
    {
        $get_data   = EmployeeService::do_get_data_employee();

        return Response::json($get_data, $get_data['status']);
    }

    // 27 MARET 2025
    // NOTE : DASHBOARD MASTER JABATAN
    public function dashboard_master_jabatan()
    {
        $view_data  = [
            'title'     => 'ERP Percik Tours | Master Jabatan',
            'sub_title' => 'Dashboard Master Jabatan'
        ];

        return view('master.index', $view_data);
    }

    // 02 APRIL 2025
    // NOTE : SIMPAN ROLE BARU
    public function dashboard_master_role_trans($jenis, Request $request) {
        // VALIDATE INPUT
        $validator  = Validator::make($request->all(), ['fr_name' => 'required|string']);

        if($validator->fails()) {
            $output     = [
                'success'   => false,
                'status'    => 522,
                'message'   => 'Periksa Kembali Inputan',
                'data'      => $validator->errors(),
            ];
        } else {
            $send_data  = [
                'ip_address'    => $request->ip(),
                'user_id'       => Auth::user()->id,
                'data'          => $request->all(),
            ];

            $do_simpan          = EmployeeService::do_simpan_role($jenis, $send_data);

            $output             = [
                'success'       => $do_simpan['is_success'],
                'status'        => $do_simpan['status_code'],
                'message'       => $do_simpan['message'],
                'data'          => $do_simpan['data'],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function dashboard_master_role_get_by_id($id)
    {
        $get_data   = EmployeeService::get_data_role_by_id($id);

        $output             = [
            'success'       => $get_data['is_success'],
            'status'        => $get_data['status_code'],
            'message'       => $get_data['message'],
            'data'          => $get_data['data'],
        ];

        return Response::json($output, $output['status']);
    }
}
