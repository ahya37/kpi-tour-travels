<?php

namespace App\Http\Controllers;

use App\Services\DivisiService;
use Illuminate\Http\Request;
use App\Services\FinanceServices;
use App\Services\SysUmhajService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Dotenv\Repository\RepositoryInterface;
use File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Storage;

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

        if(count($get_data['data']['header']) > 0) {
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

        $file_name  = time() . 'Laporan Pembayaran Haji ' . $tahun . '.xlsx';
        $writer     = new Xlsx($spreadsheet);
        $file_path  = public_path('storage/data-files/laporan_haji/');

        if(!File::exists($file_path)) {
            File::makeDirectory($file_path, 0755, true);
        }
        $writer->save($file_path.$file_name);

        try {
            $output     = [
                'success'   => true,
                'status'    => 200,
                'message'   => 'Berhasil Generate Excel File : ' . $file_name,
                'data'      => []
            ];
        } catch (\Exception $e) {
            $output     = [
                'success'   => false,
                'status'    => 522,
                'message'   => $e->getMessage(),
                'data'      => []
            ];
        }

        return Response::json($output, $output['status']);
        
        // header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        // header("Content-Disposition: attachment;filename=".$file_name);
        // $write->save("php://output");
    }
    
    public function finance_report_pembayaran_haji(Request $request, $jenisfile)
    {
        $tahun_cari     = $request->all()['tahun_cari'];
        $data_jemaah    = $this->master_get_data_jemaah_haji($tahun_cari);

        if($jenisfile == 'excel') {
            $spreadsheet = new Spreadsheet;
            // SHEET DETAIL PEMBAYARAN UMRAH
            $sheet      = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Detail Pembayaran Haji Jemaah');
            $sheet->setCellValue('A1', 'Pembayaran Jemaah Haji Tahun ' . $tahun_cari);
            $sheet->mergeCells('A1:F2');
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A1:F2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A1:F2')->getFont()->setSize(12)->setBold(true);
            // SPACER
            $sheet->setCellValue('A3', '');

            // LOOP DATA JEMAAH
            
            $num_sheet_awal     = 4;
            for($i = 0; $i < count($data_jemaah['data']); $i++) {
                $data_jemaah_haji   = $data_jemaah['data'];
                $data_bayar_haji    = $data_jemaah_haji[$i]['detail_bayar'];
                $total_bayar_haji   = 0;

                // HEADER
                $sheet->setCellValue('A' . $num_sheet_awal, 'Nama');
                $sheet->setCellValue('B' . $num_sheet_awal, $data_jemaah_haji[$i]['jemaah_name']);
                $sheet->mergeCells('B' . $num_sheet_awal . ':F' . $num_sheet_awal . '');
                $sheet->getStyle('A' . $num_sheet_awal . ':F' . $num_sheet_awal)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A' . $num_sheet_awal)->getFont()->setSize(11)->setBold(true);

                $sheet->setCellValue('A' . $num_sheet_awal + 1, 'Paket');
                $sheet->setCellValue('B' . $num_sheet_awal + 1, $data_jemaah_haji[$i]['hj_paket']);
                $sheet->mergeCells('B' . $num_sheet_awal + 1 . ':F' . $num_sheet_awal + 1 . '');
                $sheet->getStyle('A' . $num_sheet_awal + 1 . ':F' . $num_sheet_awal + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A' . $num_sheet_awal + 1)->getFont()->setSize(11)->setBold(true);

                $sheet->setCellValue('A' . $num_sheet_awal + 2, 'No. BPIH');
                $sheet->setCellValue('B' . $num_sheet_awal + 2, $data_jemaah_haji[$i]['no_bpih']);
                $sheet->mergeCells('B' . $num_sheet_awal + 2 . ':F' . $num_sheet_awal + 2 . '');
                $sheet->getStyle('A' . $num_sheet_awal + 2 . ':F' . $num_sheet_awal + 2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('B' . $num_sheet_awal + 2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A' . $num_sheet_awal + 2)->getFont()->setSize(11)->setBold(true);


                $sheet->setCellValue('A' . $num_sheet_awal + 3, 'Pembayaran Ke');
                $sheet->setCellValue('B' . $num_sheet_awal + 3, 'Tgl. Bayar');
                $sheet->setCellValue('C' . $num_sheet_awal + 3, 'Metode Pembayaran');
                $sheet->setCellValue('D' . $num_sheet_awal + 3, 'Nama Bank');
                $sheet->setCellValue('E' . $num_sheet_awal + 3, 'No. Rekening');
                $sheet->setCellValue('F' . $num_sheet_awal + 3, 'Jml. Bayar');
                $sheet->getStyle('A' . $num_sheet_awal + 3 . ':F' . $num_sheet_awal + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A' . $num_sheet_awal + 3 . ':F' . $num_sheet_awal + 3)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $num_sheet_awal + 3 . ':F' . $num_sheet_awal + 3)->getFont()->setSize(11)->setBold(true);
                // DETAIL
                for($j = 0; $j < count($data_bayar_haji); $j++) {
                    $detail_seq     = $j + 1;

                    $sheet->setCellValue('A' . $num_sheet_awal + 3 + $detail_seq, $data_bayar_haji[$j]['seq']);
                    $sheet->setCellValue('B' . $num_sheet_awal + 3 + $detail_seq, date('d-m-Y', strtotime($data_bayar_haji[$j]['payment_date'])));
                    $sheet->setCellValue('C' . $num_sheet_awal + 3 + $detail_seq, $data_bayar_haji[$j]['payment_method']);
                    $sheet->setCellValue('D' . $num_sheet_awal + 3 + $detail_seq, $data_bayar_haji[$j]['payment_bank_account']);
                    $sheet->setCellValue('E' . $num_sheet_awal + 3 + $detail_seq, $data_bayar_haji[$j]['payment_bank_account_number']);
                    $sheet->setCellValue('F' . $num_sheet_awal + 3 + $detail_seq, number_format($data_bayar_haji[$j]['payment_total'], 2));

                    $sheet->getStyle('A' . $num_sheet_awal + 3 + $detail_seq)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E' . $num_sheet_awal + 3 + $detail_seq)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle('F' . $num_sheet_awal + 3 + $detail_seq)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    // BORDER
                    $sheet->getStyle('A' . $num_sheet_awal + 3 + $detail_seq . ':F' . $num_sheet_awal + 3 + $detail_seq)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                    $total_bayar_haji    += $data_bayar_haji[$j]['payment_total'];
                }
                // TOTAL
                $sheet->setCellValue('A' . $num_sheet_awal + 4 + count($data_bayar_haji), 'Total');
                $sheet->setCellValue('F' . $num_sheet_awal + 4 + count($data_bayar_haji), number_format($total_bayar_haji, 2));
                $sheet->getStyle('A' . $num_sheet_awal + 4 + count($data_bayar_haji) . ':F' . $num_sheet_awal + 4 + count($data_bayar_haji))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('A' . $num_sheet_awal + 4 + count($data_bayar_haji))->getFont()->setSize(11)->setBold(true);
                $sheet->mergeCells('A' . $num_sheet_awal + 4 + count($data_bayar_haji) . ':E' . $num_sheet_awal + 4 + count($data_bayar_haji));
                $sheet->getStyle('A' . $num_sheet_awal + 4 + count($data_bayar_haji) . ':F' . $num_sheet_awal + 4 + count($data_bayar_haji))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                // SPACER
                $sheet->setCellValue('A' . $num_sheet_awal + 5 + count($data_bayar_haji), '');

                $num_sheet_awal     += 6 + count($data_bayar_haji);
            }

            // AUTO WIDTH
            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }

            // SHEET SUMMARY DATA
            $sheet2     = $spreadsheet->createSheet();
            $sheet2->setTitle('Rekap Pembayaran Haji');
            
            $sheet2->setCellValue('A1', 'Rekap Pembayaran Haji ' . $tahun_cari);
            $sheet2->mergeCells('A1:G2');
            $sheet2->getStyle('A1:G2')->getFont()->setSize(12)->setBold(true);
            $sheet2->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet2->getStyle('A1:G2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            $sheet2->setCellValue('A4', 'No');
            $sheet2->setCellValue('B4', 'Nama');
            $sheet2->setCellValue('C4', 'Tahun Daftar');
            $sheet2->setCellValue('D4', 'Paket');
            $sheet2->setCellValue('E4', 'No. BPIH');
            $sheet2->setCellValue('F4', 'Total Bayar');
            $sheet2->setCellValue('G4', 'Sisa Bayar');

            $sheet2->getStyle('A4:G4')->getFont()->setSize(11)->setBold(true);
            $sheet2->getStyle('A4:G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle('A4:G4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet2->getStyle('A4:G4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            $rekap_start_cell = 5;
            $grand_total_pembayaran     = 0;
            $grand_total_sisa           = 0;
            for($i = 0; $i < count($data_jemaah['data']); $i++) {
                $rekap_next_cell    = $rekap_start_cell + $i;
                $rekap_header       = $data_jemaah['data'][$i];
                $rekap_detail       = $rekap_header['detail_bayar'];

                $rekap_total_bayar  = 0;

                switch ($rekap_header['hj_paket']) {
                    case 'Double' :
                        $harga_paket    = 20000;
                    break;
                    case 'Triple' : 
                        $harga_paket    = 18500;
                    break;
                    case 'Quad' :
                        $harga_paket    = 17500;
                    break;
                    default     :
                        $harga_paket    = 0;
                }

                for($j = 0; $j < count($rekap_detail); $j++) {
                    $rekap_detail_bayar     = $rekap_detail[$j]['payment_total'];
                    
                    $rekap_total_bayar      += $rekap_detail_bayar;
                }

                $rekap_sisa_bayar   = $rekap_total_bayar - $harga_paket;

                $sheet2->setCellValue('A' . $rekap_next_cell, $i + 1);
                $sheet2->setCellValue('B' . $rekap_next_cell, $rekap_header['jemaah_name']);
                $sheet2->setCellValue('C' . $rekap_next_cell, date('Y', strtotime($rekap_header['detail_bayar'][0]['payment_date'])));
                $sheet2->setCellValue('D' . $rekap_next_cell, $rekap_header['hj_paket']);
                $sheet2->setCellValue('E' . $rekap_next_cell, $rekap_header['no_bpih']);
                $sheet2->setCellValue('F' . $rekap_next_cell, number_format($rekap_total_bayar, 2));
                $sheet2->setCellValue('G' . $rekap_next_cell, number_format($rekap_sisa_bayar, 2));

                // BORDER
                $sheet2->getStyle('A' . $rekap_next_cell . ':G' . $rekap_next_cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                // STYLE
                $sheet2->getStyle('A' . $rekap_next_cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle('C' . $rekap_next_cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle('E' . $rekap_next_cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet2->getStyle('F' . $rekap_next_cell . ':G' . $rekap_next_cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $grand_total_pembayaran     += $rekap_total_bayar;
                $grand_total_sisa           += $rekap_sisa_bayar;
                $rekap_start_cell + $i;
            }

            $sheet2->setCellValue('A' . count($data_jemaah['data']) + 5, 'Total');
            $sheet2->getStyle('A' . count($data_jemaah['data']) + 5)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet2->setCellValue('F' . count($data_jemaah['data']) + 5, number_format($grand_total_pembayaran, 2));
            $sheet2->getStyle('F' . count($data_jemaah['data']) + 5)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet2->setCellValue('G' . count($data_jemaah['data']) + 5, number_format($grand_total_sisa, 2));
            $sheet2->getStyle('G' . count($data_jemaah['data']) + 5)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet2->mergeCells('A' . count($data_jemaah['data']) + 5 . ':E' . count($data_jemaah['data']) + 5);
            $sheet2->getStyle('A' . count($data_jemaah['data']) + 5 . ':G' . count($data_jemaah['data']) + 5)->getFont()->setSize(11)->setBold(true);
            $sheet2->getStyle('A' . count($data_jemaah['data']) + 5 . ':G' . count($data_jemaah['data']) + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // HARGA PAKET
            $sheet2->setCellValue('I1', 'Double');
            $sheet2->setCellValue('I2', 'Triple');
            $sheet2->setCellValue('I3', 'Quad');
            $sheet2->setCellValue('J1', number_format(20000, 2));
            $sheet2->setCellValue('J2', number_format(18500, 2));
            $sheet2->setCellValue('J3', number_format(17500, 2));

            // AUTO WIDTH
            foreach ($sheet2->getColumnIterator() as $column) {
                $sheet2->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }

            // MEMBUAT CELL 1 MENJADI ACTIVE SHEET
            $spreadsheet->setActiveSheetIndex(0);

            if(count($data_jemaah['data']) > 0) {
                // SIMPAN
                $file_name  = time() . '_Laporan_Pembayaran_Haji_' . $tahun_cari . '.xlsx';
                $writer     = new Xlsx($spreadsheet);
                $file_path  = public_path('storage/data-files/laporan_haji/');

                if(!File::exists($file_path)) {
                    File::makeDirectory($file_path, 0755, true);
                }
                $writer->save($file_path.$file_name);

                $output     = [
                    'success'   => true,
                    'status'    => 200,
                    'message'   => 'Berhasil Generate Excel File : ' . $file_name,
                    'data'      => [
                        'data_url'  => 'storage/data-files/laporan_haji/' . $file_name,
                    ],
                ];
            } else {
                $output     = [
                    'success'   => false,
                    'status'    => 404,
                    'message'   => 'Tidak Ada Data Pembayaran Haji',
                    'data'      => [
                        'data_url'  => ''
                    ]
                ];
            }

            return Response::json($output, $output['status']);
        }
    }

    // NOTE : AFTER GENERATE EXCEL, THEN DELETE FILE FROM STORAGE WEB
    public function finance_delete_report_pembayaran_haji(Request $req)
    {
        $file_path  = public_path($req['file_path']);

        if(file_exists($file_path)) {
            unlink($file_path);
            return Response::json('berhasil', 200);
        } else {
            return Response::json('gagal', 500);
        }
    }

    // 14 MARET 2025
    // NOTE : AMBIL LIST TOUR CODE
    public function master_tour_code($kode)
    {
        $tour_code  = $kode == 'semua' ? '%' : $kode;
        
        $get_data   = FinanceServices::get_data_tour_code($tour_code);

        $output     = [
            'success'   => $get_data['is_success'],
            'status'    => $get_data['status_code'],
            'data'      => $get_data['data'],
            'message'   => $get_data['message'],
        ];

        return Response::json($output, $output['status']);
    }

    // NOTE : SIMPAN PENGAJUAN KEUANGAN
    public function finance_simpan_pengajuan_keuangan(Request $request)
    {
        // CHECK INPUTAN
        if(!empty($request->all()['header']['pgj_tr_tour_code'])) {
            // VALIDATE ERROR
            $validation     = [
                'pgj_tr_category'       => 'required',
                'pgj_tr_cat_short_desc' => 'required',
            ];
        } else {
            $validation     = [];
        }
        $validator  = Validator::make($request->all()['header'], $validation);

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
    
            $do_simpan  = FinanceServices::do_simpan_pengajuan_keuangan($send_data);
    
            $output     = [
                'success'   => $do_simpan['is_success'],
                'status'    => $do_simpan['status_code'],
                'message'   => $do_simpan['message'],
                'data'      => $do_simpan['data']
            ];
        }
        
        return Response::json($output, $output['status']);
    }

    // 17 MARET 2025
    // NOTE : AMBIL DATA HPP FINANCE BY TOUR CODE
    public function finance_hpp_get_data(Request $request)
    {
        $tour_code  = $request->all()['tour_code'];

        $get_data   = FinanceServices::get_data_hpp_tour_code($tour_code);

        $output     = [
            'success'   => $get_data['is_success'],
            'status'    => $get_data['status_code'],
            'message'   => $get_data['message'],
            'data'      => $get_data['data']
        ];

        return Response::json($output, $output['status']);
    }

    // NOTE : SIMPAN DATA HPP FINANCE
    public function finance_hpp_save_data(Request $request)
    {
        $send_data  = [
            'ip_address'    => $request->ip(),
            'user_id'       => Auth::user()->id,
            'header'        => $request->all()['header'],
            'detail'        => $request->all()['detail'],
        ];

        $do_simpan  = FinanceServices::do_save_hpp_data($send_data);

        $output     = [
            'success'       => $do_simpan['is_success'],
            'status_code'   => $do_simpan['status_code'],
            'message'       => $do_simpan['message'],
            'data'          => $do_simpan['data']
        ];

        return Response::json($output, $output['status_code']);
    }

    // 18 MARET 2025
    // NOTE : DOWNLOAD FILE EXCEL
    public function finance_hpp_report(Request $request)
    {
        $bulan  = $request->all()['bulan'];
        $tahun  = $request->all()['tahun'];
        $tab    = str_repeat(" ", 10);

        // GET DATA TOUR CODE BY BULAN
        $get_tour_code  = FinanceServices::get_tour_code_by_year_month($tahun, $bulan);
        $get_umrah_payment  = FinanceServices::get_payment_umrah_by_year_month($tahun, $bulan);

        if(count($get_tour_code['data']) > 0) {
            // GROUPING TOUR CODE
            for($i = 0; $i < count($get_tour_code['data']); $i++) {
                if($i == 0) {
                    $temp_tour_code[]   = [
                        'tour_code'         => $get_tour_code['data'][$i]->tour_code,
                        'depature_date'     => $get_tour_code['data'][$i]->depature_date,
                        'seat_take'         => $get_tour_code['data'][$i]->seat_take,
                        'program_name'      => $get_tour_code['data'][$i]->program_name,
                    ];
                } else {
                    if($get_tour_code['data'][$i]->tour_code != $get_tour_code['data'][$i-1]->tour_code) {
                        $temp_tour_code[]   = [
                            'tour_code'         => $get_tour_code['data'][$i]->tour_code,
                            'depature_date'     => $get_tour_code['data'][$i]->depature_date,
                            'seat_take'         => $get_tour_code['data'][$i]->seat_take,
                            'program_name'      => $get_tour_code['data'][$i]->program_name,
                        ];
                    }
                }
            }

            // TEMP PAYMENT
            if(count($get_umrah_payment['data']) > 0) {
                for($i = 0; $i < count($get_umrah_payment['data']); $i++) {
                    $temp_umrah_payment[]   = [
                        'tour_code'     => $get_umrah_payment['data'][$i]->tour_code,
                        'category'      => $get_umrah_payment['data'][$i]->category,
                        'total_amount'  => $get_umrah_payment['data'][$i]->grand_total_amount
                    ];
                }
            } else {
                $temp_umrah_payment   = [];
            }

            $spreadsheet    = new Spreadsheet;
            $sheet_summary  = $spreadsheet->getActiveSheet();

            $sheet_summary->getStyle('A1:J46')->getFont()->setName('Book Antiqua');

            $sheet_summary->setTitle('Summary Laba Rugi ' . $bulan . " " . $tahun);

            for($i = 0; $i < count($temp_tour_code); $i++) {
                $tour_code      = $temp_tour_code[$i]['tour_code'];
                $depature_date  = $temp_tour_code[$i]['depature_date'];
                $seat_take      = $temp_tour_code[$i]['seat_take'];
                $program_name   = $temp_tour_code[$i]['program_name'];

                $data_payment   = array_filter($temp_umrah_payment, function($payment) use($tour_code){
                    return $payment['tour_code'] === $tour_code;
                });

                if($seat_take > 0) {
                    // BUAT SPREADSHEET
                    $data_tour_code[]   = [
                        'tour_code'         => $tour_code,
                        'depature_date'     => $depature_date,
                        'seat_take'         => $seat_take,
                        'program_name'      => $program_name,
                        'detail_payment'    => array_values($data_payment)
                    ];
                }
            }
            
            // DAPATKAN A - Z DALAM LOOP
            $seq = 0;
            for($i = 65; $i <= 90; $i++) {
                $data_character[]   = [
                    'char_seq'  => $seq++,
                    'character' => chr($i)
                ];
            }

            $total_tour_code    = count($data_tour_code);
            $get_merge_char     = array_filter($data_character, function($char) use($total_tour_code){
                return $char['char_seq'] == (int) $total_tour_code + 1;
            });

            // HEADER
            $sheet_summary->setCellValue('A1', 'PT. PERCIKAN IMAN TOURS & TRAVEL');
            $sheet_summary->mergeCells('A1:' . array_values($get_merge_char)[0]['character'] . '1');
            $sheet_summary->setCellValue('A2', 'RINCIAN PENDAPATAN & BIAYA UMRAH BULAN ' . date('F', $bulan) . ' ' . $tahun);
            $sheet_summary->mergeCells('A2:' . array_values($get_merge_char)[0]['character'] . '2');

            
            $sheet_summary->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet_summary->getStyle('A1:A2')->getFont()->setSize(11)->setBold(true);


            $sheet_summary->setCellValue('A4', 'NAMA PERKIRAAN');
            $sheet_summary->setCellValue('A7', 'Pendapatan Umrah : ');
            $sheet_summary->setCellValue('A8', $tab . 'Pendapatan Umrah');
            $sheet_summary->setCellValue('A9', $tab . 'Airport & Handling');
            $sheet_summary->setCellValue('A10', $tab . 'Surat Mahram');
            $sheet_summary->setCellValue('A11', $tab . 'Pendapatan Tambahan Biaya Paket (Visa, Asuransi, dan lain-lain');
            $sheet_summary->setCellValue('A12', $tab . 'Pendapatan Kereta Cepat');
            $sheet_summary->setCellValue('A13', $tab . 'Lain-Lain');
            $sheet_summary->setCellValue('A14', 'Jumlah');

            $sheet_summary->setCellValue('A16', 'Biaya Umrah : ');
            $sheet_summary->setCellValue('A17', $tab . 'Biaya Tiket');
            $sheet_summary->setCellValue('A18', $tab . 'Biaya Perjalanan & Fee Pembimbing');
            $sheet_summary->setCellValue('A19', $tab . 'Biaya Manasik Umrah');
            $sheet_summary->setCellValue('A20', $tab . 'Biaya Additional Tours & Ekstra Tip');
            $sheet_summary->setCellValue('A21', $tab . 'Biaya Land Arrangment');
            $sheet_summary->setCellValue('A22', $tab . 'Biaya Tiket Museum');
            $sheet_summary->setCellValue('A23', $tab . 'Biaya Perlengkapan Jemaah');
            $sheet_summary->setCellValue('A24', $tab . 'Biaya Administrasi Jemaah');
            $sheet_summary->setCellValue('A25', $tab . 'Biaya Akomodasi & Transportasi (Airport & Handling)');
            $sheet_summary->setCellValue('A26', $tab . 'Biaya Mahram');
            $sheet_summary->setCellValue('A27', $tab . 'Biaya Hotel');
            $sheet_summary->setCellValue('A28', $tab . 'Biaya Visa & LA Tours');
            $sheet_summary->setCellValue('A29', $tab . 'Biaya Fee Bendera & Pengurusan');
            $sheet_summary->setCellValue('A30', $tab . 'Biaya Visa');
            $sheet_summary->setCellValue('A31', $tab . 'Biaya Insentif Umrah Pembimbing');
            $sheet_summary->setCellValue('A32', $tab . 'Biaya Insentif Karyawan');
            $sheet_summary->setCellValue('A33', $tab . 'Biaya Biometrik');
            $sheet_summary->setCellValue('A34', $tab . 'Biaya Visa Asuransi');
            $sheet_summary->setCellValue('A35', $tab . 'Biaya Karantina');
            $sheet_summary->setCellValue('A36', $tab . 'Biaya PCR');
            $sheet_summary->setCellValue('A37', $tab . 'Biaya Lokal');
            $sheet_summary->setCellValue('A38', $tab . 'Biaya Kereta Cepat');
            $sheet_summary->setCellValue('A39', $tab . 'Biaya Lain-Lain');
            $sheet_summary->setCellValue('A40', $tab . 'Biaya Paket');
            $sheet_summary->setCellValue('A41', $tab . 'Biaya Lain-Lain (spanduk & obat-obatan');

            $sheet_summary->setCellValue('A43', 'Jumlah');

            $sheet_summary->setCellValue('A45', 'Laba (Rugi) Kotor');
            $sheet_summary->setCellValue('A46', 'Laba (Rugi) Kotor Per Pax');

            // STYLE HEADER
            $sheet_summary->getStyle('A4')->getFont()->setSize(11)->setBold(true);
            $sheet_summary->getStyle('A7')->getFont()->setSize(11)->setBold(true);
            $sheet_summary->getStyle('A14')->getFont()->setSize(11)->setBold(true);
            $sheet_summary->getStyle('A16')->getFont()->setSize(11)->setBold(true);
            $sheet_summary->getStyle('A43')->getFont()->setSize(11)->setBold(true);
            $sheet_summary->getStyle('A45')->getFont()->setSize(11)->setBold(true);
            $sheet_summary->getStyle('A46')->getFont()->setSize(11)->setBold(true);
            
            $sheet_summary->mergeCells('A4:A5');
            $sheet_summary->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet_summary->getStyle('A4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet_summary->getStyle('A14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet_summary->getStyle('A43')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // FIXED WIDTH
            $total_biaya_tiket          = 0;
            $total_biaya_perjalanan     = 0;
            $total_biaya_la             = 0;
            $total_biaya_manasik        = 0;
            $total_biaya_tiket_museum   = 0;
            $total_biaya_perlengkapan   = 0;
            $total_biaya_asuransi       = 0;
            $total_biaya_akomodasi      = 0;
            $total_biaya_handling       = 0;
            $total_biaya_kereta_cepat   = 0;
            $total_biaya_paket          = 0;
            $total_biaya_lain_lain      = 0;

            $sheet_summary->getColumnDimension('A')->setWidth(50);
            for($i = 0; $i < count($data_tour_code); $i++) {
                $get_merge_char_1   = array_filter($data_character, function($char) use($i){
                    return $char['char_seq'] == (int) $i + 1;
                });

                $char       = array_merge($get_merge_char_1)[0]['character'];
                $sheet_summary->getColumnDimension($char)->setWidth(26);

                $sheet_summary->setCellValue($char.'4', "UMRAH " . strtoupper($data_tour_code[$i]['program_name']));
                $sheet_summary->setCellValue($char.'5', $data_tour_code[$i]['seat_take'] . " PAX");
                $sheet_summary->setCellValue($char."6", "UMRAH " . strtoupper(date('d M Y', strtotime($data_tour_code[$i]['depature_date']))));

                $sheet_summary->getStyle($char."4:".$char."6")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet_summary->getStyle($char."4:".$char."6")->getFont()->setBold(true);
                
                $detail_payment_umrah   = $data_tour_code[$i]['detail_payment'];
                
                $grand_total_biaya_umrah    = 0;

                if(count($detail_payment_umrah) > 0) {
                    for($j = 0; $j < count($detail_payment_umrah); $j++) {
                        $total_amount   = !empty($detail_payment_umrah[$j]['total_amount']) ? $detail_payment_umrah[$j]['total_amount'] : 0;
                        $grand_total_biaya_umrah     += $total_amount;
                        switch($detail_payment_umrah[$j]['category']) {
                            case 'tiket' :
                                $sheet_summary->setCellValue($char . '17', $total_amount);
                                $total_biaya_tiket    = $total_biaya_tiket + $total_amount;
                            break;
                            case 'fee_pembimbing' :
                                $sheet_summary->setCellValue($char . '18', $total_amount);
                                $total_biaya_perjalanan     = $total_biaya_perjalanan + $total_amount;
                            break;
                            case 'manasik' :
                                $sheet_summary->setCellValue($char . '19', $total_amount);
                                $total_biaya_manasik    = $total_biaya_manasik + $total_amount;
                            break;
                            case 'la' :
                                $sheet_summary->setCellValue($char . '21', $total_amount);
                                $total_biaya_la     = $total_biaya_la + $total_amount;
                            break;
                            case 'tiket_museum' :
                                $sheet_summary->setCellValue($char . '22', $total_amount);
                                $total_biaya_tiket_museum   = $total_biaya_tiket_museum + $total_amount;
                            break;
                            case 'perlengkapan_jemaah' :
                                $sheet_summary->setCellValue($char . '23', $total_amount);
                                $total_biaya_perlengkapan   = $total_biaya_perlengkapan + $total_amount;
                            break;
                            case 'asuransi' :
                                $sheet_summary->setCellValue($char . '24', $total_amount);
                                $total_biaya_asuransi   = $total_biaya_asuransi + $total_amount;
                            break;
                            case 'akomodasi' :
                                $sheet_summary->setCellValue($char . '25', $total_amount);
                                $total_biaya_akomodasi  = $total_biaya_akomodasi + $total_amount;
                            break;
                            case 'handling' :
                                $sheet_summary->setCellValue($char . '37', $total_amount);
                                $total_biaya_handling   = $total_biaya_handling + $total_amount;
                            break;
                            case 'kereta_cepat' :
                                $sheet_summary->setCellValue($char . '38', $total_amount);
                                $total_biaya_kereta_cepat   = $total_biaya_kereta_cepat + $total_amount;
                            break;
                            case 'paket' :
                                $sheet_summary->setCellValue($char . '40', $total_amount);
                                $total_biaya_paket  = $total_biaya_paket + $total_amount;
                            break;
                            case 'lain-lain' :
                                $sheet_summary->setCellValue($char . '41', $total_amount);
                                $total_biaya_lain_lain  = $total_biaya_lain_lain + $total_amount;
                            break;
                        }
                    }
                }
                $sheet_summary->setCellValue($char . '43', $grand_total_biaya_umrah);
                $sheet_summary->getStyle($char . '8:' . $char . '46')->getNumberFormat()->setFormatCode('_-"Rp"* #,##0.00_-;-"Rp"* #,##0.00_-;_-"Rp"* "-"??_-;_-@_-');
            }
            // TOTAL
            $sheet_summary->setCellValue(array_values($get_merge_char)[0]['character'].'4', 'Total');
            $sheet_summary->mergeCells(array_values($get_merge_char)[0]['character'] . '4:' .array_values($get_merge_char)[0]['character'].'5');
            $sheet_summary->getStyle(array_values($get_merge_char)[0]['character'].'4')->getFont()->setSize(11)->setBold(true);
            $sheet_summary->getStyle(array_values($get_merge_char)[0]['character'].'4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet_summary->getStyle(array_values($get_merge_char)[0]['character'].'4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            // FORMAT RP
            $sheet_summary->getStyle(array_values($get_merge_char)[0]['character'] . '8:' . array_values($get_merge_char)[0]['character'] . '46')->getNumberFormat()->setFormatCode('_-"Rp"* #,##0.00_-;-"Rp"* #,##0.00_-;_-"Rp"* "-"??_-;_-@_-');

            $sheet_summary->setCellValue(array_values($get_merge_char)[0]['character'] . '17', $total_biaya_tiket);
            $sheet_summary->setCellValue(array_values($get_merge_char)[0]['character'] . '18', $total_biaya_perjalanan);
            $sheet_summary->setCellValue(array_values($get_merge_char)[0]['character'] . '19', $total_biaya_manasik);
            $sheet_summary->setCellValue(array_values($get_merge_char)[0]['character'] . '21', $total_biaya_la);
            $sheet_summary->setCellValue(array_values($get_merge_char)[0]['character'] . '22', $total_biaya_tiket_museum);
            $sheet_summary->setCellValue(array_values($get_merge_char)[0]['character'] . '23', $total_biaya_perlengkapan);
            $sheet_summary->setCellValue(array_values($get_merge_char)[0]['character'] . '24', $total_biaya_asuransi);
            $sheet_summary->setCellValue(array_values($get_merge_char)[0]['character'] . '25', $total_biaya_akomodasi);
            $sheet_summary->setCellValue(array_values($get_merge_char)[0]['character'] . '37', $total_biaya_handling);
            $sheet_summary->setCellValue(array_values($get_merge_char)[0]['character'] . '38', $total_biaya_kereta_cepat);
            $sheet_summary->setCellValue(array_values($get_merge_char)[0]['character'] . '40', $total_biaya_paket);
            $sheet_summary->setCellValue(array_values($get_merge_char)[0]['character'] . '41', $total_biaya_lain_lain);
            $sheet_summary->setCellValue(array_values($get_merge_char)[0]['character'] . '43', $total_biaya_tiket + $total_biaya_perjalanan + $total_biaya_manasik + $total_biaya_la + $total_biaya_tiket_museum + $total_biaya_perlengkapan + $total_biaya_asuransi + $total_biaya_akomodasi + $total_biaya_handling + $total_biaya_kereta_cepat + $total_biaya_paket + $total_biaya_lain_lain);


            // HIDDEN ROW
            $sheet_summary->getRowDimension(10)->setVisible(false);
            $sheet_summary->getRowDimension(11)->setVisible(false);
            $sheet_summary->getRowDimension(20)->setVisible(false);
            $sheet_summary->getRowDimension(26)->setVisible(false);
            $sheet_summary->getRowDimension(27)->setVisible(false);
            $sheet_summary->getRowDimension(28)->setVisible(false);
            $sheet_summary->getRowDimension(29)->setVisible(false);
            $sheet_summary->getRowDimension(30)->setVisible(false);
            $sheet_summary->getRowDimension(31)->setVisible(false);
            $sheet_summary->getRowDimension(32)->setVisible(false);
            $sheet_summary->getRowDimension(33)->setVisible(false);
            $sheet_summary->getRowDimension(34)->setVisible(false);
            $sheet_summary->getRowDimension(35)->setVisible(false);
            $sheet_summary->getRowDimension(36)->setVisible(false);
            $sheet_summary->getRowDimension(39)->setVisible(false);

            // SET WIDTH
            $sheet_summary->getColumnDimension(array_values($get_merge_char)[0]['character'])->setWidth(25);

            // DEFAULT ACTIVE SHEET
            $spreadsheet->setActiveSheetIndex(0);

            // SIMPAN FILE
            $file_name  = time() . "_Laporan_Laba_Rugi_Umrah_" . date('F', strtotime($bulan)) ."-". $tahun . ".xlsx";
            $writer     = new Xlsx($spreadsheet);
            $file_path  = public_path('storage/data-files/laporan_finance/');

            if(!File::exists($file_path)) {
                File::makeDirectory($file_path, 0755, true);
            }

            $writer->save($file_path.$file_name);

            try {
                $output     = [
                    'success'   => true,
                    'status'    => 201,
                    'message'   => 'Berhasil Download File Laba Rugi',
                    'data'      => [
                        'data_url'  => 'storage/data-files/laporan_finance/' . $file_name,
                    ],
                ];
            } catch (\Exception $e) {
                $output     = [
                    'success'   => false,
                    'status'    => 400,
                    'message'   => 'Gagal Download File Laba Rugi',
                    'data'      => []
                ];
            }

        } else {
            $output     = [
                'success'   => $get_tour_code['is_success'],
                'status'    => $get_tour_code['status_code'],
                'message'   => 'Tidak Ada Data Yang Bisa Didownload',
                'data'      => $get_tour_code['data'],
            ];
        }
        
        return Response::json($output, $output['status']);
    }

    public function finance_delete_hpp_report(Request $request)
    {
        $file_path  = public_path($request['file_path']);

        if(file_exists($file_path)) {
            unlink($file_path);
            return Response::json('berhasil', 200);
        } else {
            return Response::json('gagal', 500);
        }
    }
}
