<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FinanceServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class FinanceController extends Controller
{
    protected $title    =  "ERP Percik Tours | ";
    // 17 FEBRUARI 2025
    // NOTE : DASHBOARD MASTER FINANCE
    public function finance_master_dashboard()
    {
        $data_view  = [
            "title"     => $this->title . " Finance Dashboard Master",
            "sub_title" => "Finance Dashboard Master",
            "data"      => []
        ];

        return view('divisi.finance.master.dashboard', $data_view);
    }
    
    // NOTE : VIEW COA
    public function finance_master_coa()
    {
        $data_view  = [
            "title"     => $this->title . " Finance Master COA",
            "sub_title" => "Finance Master COA (Chart of Accounts)",
            "data"      => [],
        ];

        return view('divisi.finance.master.master_coa.index', $data_view);
    }

    // 18 FEBRUARI 2025
    // NOTE : AMBI LIST COA
    public function finance_list_coa(Request $request)
    {
        $coa_id     = $request->all()['coa_id'] == 'semua' ? '%' : $request->all()['coa_id'];
        $get_data   = FinanceServices::finance_master_coa($coa_id);

        if($get_data['status'] == 'berhasil') {
            $output     = [
                'status'    => 201,
                'success'   => true,
                'message'   => $get_data['status'],
                'data'      => $get_data['data'],
            ];
        } else if($get_data['status'] == 'gagal') {
            $output     = [
                'status'    => 500,
                'success'   => false,
                'message'   => $get_data['status'],
                'data'      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    // NOTE : SIMPAN COA BARU / EDIT
    public function finance_save_coa(Request $request, $jenis)
    {
        $validator  = Validator::make($request->all(), [
            'coa_parent'        => 'required',
            'coa_id_parent'     => 'required',
            'coa_id_child'      => 'required|numeric|min:0',
            'coa_description'   => 'required',
        ]);

        if($validator->fails()) {
            $output     = [
                'success'   => false,
                'status'    => 422,
                'message'   => $validator->errors(),
                'data'      => []
            ];
        } else {
            $send_data  = [
                'user_id'   => Auth::user()->id,
                'ip_address'=> $request->ip(),
                'today'     => date('Y-m-d H:i:s'),
                'data'      => $request->all(),
                'type'      => $jenis,
            ];
            $do_simpan  = FinanceServices::do_simpan_coa($send_data);

            $output     = [
                'success'   => $do_simpan['is_success'],
                'status'    => $do_simpan['status_code'],
                'message'   => $do_simpan['message'],
                'data'      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }
}
