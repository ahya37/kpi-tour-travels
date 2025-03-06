<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FinanceServices;
use App\Services\SysUmhajService;
use Dotenv\Repository\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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

    // 25 FEBRUARI 2025
    // NOTE : AMBIL PENGAJUAN KEUANGAN
    public function finance_umhaj_pengajuan_keuangan(Request $request)
    {
        $send_data   = [
            'bulan'     => $request->all()['selected_month'],
            'tahun'     => $request->all()['selected_year'],
        ];

        $get_data   = FinanceServices::get_pengajuan_keuangan_umhaj($send_data);

        $output     = [
            'success'   => $get_data['is_success'],
            'status'    => $get_data['status_code'],
            'message'   => $get_data['message'],
            'data'      => $get_data['data']
        ];

        return Response::json($output, $output['status']);
    }

    // NOTE : AMBIL PENGAJUAN KEUANGAN DETAIL
    public function finance_umhaj_pengajuan_keuangan_detail(Request $request)
    {
        $id_pengajuan   = $request->all()['id'];
        
        $get_data   = FinanceServices::get_pengajuan_keuangan_detail_umhaj($id_pengajuan);

        $output     = [
            'success'   => $get_data['is_success'],
            'status'    => $get_data['status_code'],
            'message'   => $get_data['message'],
            'data'      => $get_data['data'],
        ];

        return Response::json($output, $output['status']);
    }

    // NOTE : LIST MASTER KURS
    public function finance_master_currency(Request $request)
    {
        $send_data  = [
            'limit'     => $request->all()['limit'],
            'orderBy'   => $request->all()['sort'],
        ];

        $get_data   = FinanceServices::get_finance_currency_master($send_data);

        $output      = [
            'success'   => $get_data['is_success'],
            'status'    => $get_data['status_code'],
            'message'   => $get_data['message'],
            'data'      => $get_data['data'],
        ];

        return Response::json($output, $output['status']);
    }

    // NOTE : TRANS SIMPAN CURRENCY
    public function finance_master_currency_trans(Request $request, $trans_type)
    {
        $validator  = Validator::make($request->all(), [
            'kurs_start_date'   => 'required|date',
            'kurs_value_low'    => 'required',
            'kurs_value_high'   => 'required'
        ]);

        if($validator->fails())
        {
            $output     = [
                'success'   => false,
                'status'    => 422,
                'message'   => $validator->messages(),
                'data'      => [],
            ];
        } else {
            $send_data  = [
                'data'      => $request->all(),
                'type'      => $trans_type,
                'user_id'   => Auth::user()->id,
                'user_name' => Auth::user()->name,
                'ip_address'=> $request->ip(),   
            ];

            $do_simpan  = FinanceServices::trans_finance_currency_master($send_data);

            $output     = [
                'success'   => $do_simpan['is_success'],
                'status'    => $do_simpan['status_code'],
                'message'   => $do_simpan['message'],
                'data'      => $do_simpan['data'],  
            ];

            return Response::json($output, $output['status']);
        }

        return Response::json($output, $output['status']);
    }

    // NOTE : AMBIL DETAIL CURRENCY
    public function finance_mater_currency_detail(Request $request)
    {
        $curr_id    = $request->all()['curr_id'];
        
        $get_data   = FinanceServices::get_finance_currency_detail($curr_id);

        $output     = [
            'success'   => $get_data['is_success'],
            'status'    => $get_data['status_code'],
            'message'   => $get_data['message'],
            'data'      => $get_data['data'],
        ];

        return Response::json($output, $output['status']);
    }

    // 02 MARET 2025
    // NOTE : AMBIL DATA PEMBAYARAN HAJI
    public function finance_pembayaran_haji_list(Request $request)
    {
        $get_data   = FinanceServices::get_data_pembayaran_haji();

        $output     = [
            'success'   => $get_data['is_success'],
            'status'    => $get_data['status_code'],
            'message'   => $get_data['message'],
            'data'      => $get_data['data']
        ];

        return Response::json($output, $output['status']);
    }

    // NOTE : AMBIL DATA MEMBER
    public function master_get_data_member(Request $request)
    {
        // FOR SELECT2 PURPOSE
        $keyword    = $request->all()['keyword'];

        $get_data   = SysUmhajService::get_data_member($keyword);

        $output     = [
            "success"   => $get_data['is_success'],
            "status"    => $get_data['status_code'],
            "message"   => $get_data['message'],
            "data"      => $get_data['data'],
        ];

        return Response::json($output, $output['status']);
    }

    // NOTE : AMBIL NO DAFTAR SELECTED MEMBER ID
    public function finance_detail_jemaah_haji(Request $request)
    {
        $send_data  = [
            'jemaah_id' => $request->all()['member_id'],
            'haji_kode' => $request->all()['haji_kode'] == 'semua' ? '%' : $request->all()['haji_kode']
        ];

        $get_data   = FinanceServices::do_get_no_daftar_jemaah($send_data);

        $output     = [
            'success'   => $get_data['is_success'],
            'status'    => $get_data['status_code'],
            'message'   => $get_data['message'],
            'data'      => $get_data['data'],
        ];

        return Response::json($output, $output['status']);
    }
    
    // 03 MARET 2025
    // NOTE : SIMPAN DATA PEMBAYARAN HAJI
    public function finance_pembayaran_haji_simpan_haji($type, Request $request)
    {
        if($type == 'add') {
            $validator_rules    = [
                'jemaah_id' => 'required',
                'tour_code' => 'required',
            ];
        } else {
            $validator_rules    = [];
        }
        
        $validator  = Validator::make($request->all()['header'], $validator_rules);

        if($validator->fails()) {
            $output     = [
                'success'   => false,
                'status'    => 522,
                'message'   => $validator->errors(),
                'data'      => []
            ];
        } else {
            $send_data  = [
                'user_id'       => Auth::user()->id,
                'ip_address'    => $request->ip(),
                'data'          => $request->all(),
            ];
            
            $do_simpan  = FinanceServices::do_simpan_pembayaran_haji($type, $send_data);
            
            $output     = [
                'success'   => $do_simpan['is_success'],
                'status'    => $do_simpan['status_code'],
                'message'   => $do_simpan['message'],
                'data'      => $do_simpan['data']
            ];
        }

        return Response::json($output, $output['status']);
    }

    // 04 MARET 2025
    // NOTE : GET DATA FINANCE PEMBAYARAN JEMAAH
    public function finance_pembayaran_detail_haji_jemaah(Request $request)
    {
        $trans_id   = $request->all()['trans_id'];

        $get_data   = FinanceServices::do_get_data_pembayaran_detail_haji_jemaah($trans_id);

        $output     = [
            'status'    => $get_data['status_code'],
            'success'   => $get_data['is_success'],
            'message'   => $get_data['message'],
            'data'      => $get_data['data'],
        ];

        return Response::json($output, $output['status']);
    }

    // 06 MARET 2025
    private function master_get_data_jemaah_haji($year)
    {
        $get_data   = FinanceServices::get_data_report_haji_payment($year);

        if(count($get_data['data']) > 0) {
            $data_header    = $get_data['data']['header'];
            $data_detail    = $get_data['data']['detail'];

            // BUAT DETAI MENJADI ARRAY
            for($i = 0; $i < count($data_detail); $i++) {
                $arr_detail[]   = [
                    'trans_id'              => $data_detail[$i]->hj_trans_id,
                    'seq'                   => $data_detail[$i]->hj_seq,
                    'payment_date'          => $data_detail[$i]->hj_payment_date,
                    'payment_total'         => $data_detail[$i]->hj_payment_amount,
                    'payment_method'        => $data_detail[$i]->hj_payment_method == 'tf' ? 'Transfer' : 'Cash',
                    'payment_bank_account'  => $data_detail[$i]->bank_name,
                    'payment_bank_account_number'   => $data_detail[$i]->bank_account_number,
                    'payment_currency'      => $data_detail[$i]->hj_payment_currency
                ];
            }

            for($i = 0; $i < count($data_header); $i++) {
                $trans_id       = $data_header[$i]->hj_trans_id;
                $jemaah_name    = $data_header[$i]->hj_trans_member_name;
                $no_bpih        = $data_header[$i]->hj_no_bpih;
                $hj_paket       = $data_header[$i]->hj_paket;

                $detail_pembayaran  = array_filter($arr_detail, function($item) use ($trans_id) {
                    return $item['trans_id'] == $trans_id;
                });

                $arr_pembayaran[]  = [
                    'trans_id'      => $trans_id,
                    'jemaah_name'   => $jemaah_name,
                    'no_bpih'       => $no_bpih,
                    'hj_paket'      => $hj_paket,
                    'detail_bayar'  => array_values($detail_pembayaran)
                ];
            }

            $output     = [
                'success'   => $get_data['is_success'],
                'status'    => $get_data['status_code'],
                'message'   => $get_data['message'],
                'data'      => $arr_pembayaran,
            ];
        } else {
            $output     = [
                'success'   => false,
                'status'    => 404,
                'message'   => 'Tidak Ada',
                'data'      => [],
            ];
        }

        return $output;
    }
    // NOTE : DEMO REPORT PEMBAYARAN HAJI
    public function finance_report_pembayaran_detail_jemaah_demo(Request $request, $jenis)
    {
        $tahun_cari     = $request->all()['tahun_cari'];
        $data_jemaah    = $this->master_get_data_jemaah_haji($tahun_cari);
        if($jenis == 'view') {
            return view('divisi.finance.pembayaran.pembayaran_haji.report', $data_jemaah);
        } else if($jenis == 'pdf') {
            $pdf    = PDF::loadView('divisi/finance/pembayaran/pembayaran_haji/report', $data_jemaah)->setPaper('a4', 'landscape');
            return $pdf->stream();
        }
    }

    public function finance_report_pembayaran_detail_jemaah_excel($tahun)
    {
        $data_jemaah    = $this->master_get_data_jemaah_haji($tahun);

        $spreadsheet = new Spreadsheet;
        $sheet      = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Detail Pembayaran Haji Jemaah');

        $sheet->setCellValue('A1', 'Pembayaran Jemaah Haji Tahun ' . $tahun);

        $sheet->mergeCells('A1:H2');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:H2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A1:H2')->getFont()->setSize(16)->setBold(true);
        // $sheet->setCellValue('A1', 'No');
        // $sheet->setCellValue('B1', 'Nama');
        // $sheet->setCellValue('C1', 'Trans ID');
        // for($i = 0; $i < count($data_jemaah['data']); $i++) {
        //     $trans_id   = $data_jemaah['data'][$i]['trans_id'];
        //     $nama       = $data_jemaah['data'][$i]['jemaah_name'];

        //     $sheet->setCellValue('A' . $i + 2, $i + 1);
        //     $sheet->setCellValue('B' . $i + 2, $nama);
        //     $sheet->setCellValue('C' . $i + 2, $trans_id);
        // }

        $write  = new Xlsx($spreadsheet);

        $file_name  = 'Test_Excel.xlsx';
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment;filename=".$file_name);
        $write->save("php://output");
        exit();
    }
}
