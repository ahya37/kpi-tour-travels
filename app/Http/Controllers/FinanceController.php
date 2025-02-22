<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FinanceServices;
use Dotenv\Repository\RepositoryInterface;
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

    // MENU DIVISI PEMBAYARAN
    public function finance_pembayaran_dashboard(Request $request)
    {
        $data_view  = [
            "title"     => $this->title . "Finance Pembayaran Dashboard",
            "sub_title" => "Pembayaran Dashboard",
            "data"      => [],
        ];

        return view('divisi.finance.pembayaran.dashboard', $data_view);
    }

    // 21 FEBRUARI 2025
    // NOTE : MASTER BANK
    public function finance_master_bank()
    {
        $data_view  = [
            "title"         => $this->title . "Master Bank",
            "sub_title"     => "Finance Master Bank",
        ];

        return view('divisi.finance.master.master_bank.index', $data_view);
    }

    // NOTE : LIST BANK
    public function finance_list_bank(Request $request)
    {
        $get_data   = FinanceServices::get_finance_list_bank();

        $output     = [
            "success"   => $get_data['is_success'],
            "status"    => $get_data['status_code'],
            "message"   => $get_data['message'],
            "data"      => $get_data['data'],
        ];

        return Response::json($output, $output['status']);
    }

    // NOTE : DASHBOARD BANK ACCOUNT
    public function finance_bank_account()
    {
        $data_view  = [
            'title'         => $this->title . "Master Bank Account",
            'sub_title'     => "Finance - Master Akun Bank",
            "data"          => [],
        ];

        return view('divisi.finance.master.master_bank_account.index', $data_view);
    }

    // NOTE : LIST BANK ACCOUNT
    public function finance_list_bank_account(Request $request)
    {
        $get_data   = FinanceServices::get_finance_list_account_bank();

        $output     = [
            "success"   => $get_data['is_success'],
            "status"    => $get_data['status_code'],
            "message"   => $get_data['message'],
            "data"      => $get_data['data'],
        ];

        return Response::json($output, $output['status']);
    }

    // 22 FEBRUARI 2025
    // NOTE : AMBIL DATA BANK ACCOUNT SESUAI ID
    public function finance_selected_bank_account(Request $request)
    {
        $bank_account_id    = $request->all()['acb_id'];

        $get_data           = FinanceServices::get_data_selected_bank_account($bank_account_id);

        $output         = [
            'success'   => $get_data['is_success'],
            'status'    => $get_data['status_code'],
            'message'   => $get_data['message'],
            'data'      => $get_data['data'],
        ];

        return Response::json($output, $output['status']);
    }

    // NOTE : SIMPAN DATA MASTER BANK ACCOUNT
    public function finane_save_bank_account(Request $request, $type)
    {
        // VALIDATE
        $check  = Validator::make($request->all(), [
            'acb_bank_id'   => 'required',
            'acb_coa_id'    => 'required', 
            'acb_currency'  => 'required',
            'acb_bank_account'  => 'required|numeric',
        ]);

        if($check->fails()) {
            $output     = [
                'success'   => false,
                'status'    => 422, 
                'message'   => $check->errors(),
                'data'      => [],
            ];
        } else {
            $data_simpan    = [
                'ip_address'    => $request->ip(),
                'user_id'       => Auth::user()->id,
                'data'          => $request->all(),
            ];

            $do_simpan      = FinanceServices::do_save_bank_account($type, $data_simpan);

            $output         = [
                'success'   => $do_simpan['is_success'],
                'status'    => $do_simpan['status_code'],
                'message'   => $do_simpan['message'],
                'data'      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }
}
