<?php 

namespace App\Services;

use App\Helpers\LogHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
date_default_timezone_set('Asia/Jakarta');

class FinanceServices
{
    // 17 FEBRUARI 2025
    // NOTE : AMBIL FINANCE MASTER COA W/OUT FILTER
    public static function finance_master_coa($coa_id)
    {
        $query  = DB::table('fin_mas_coa')
                    ->select('coa_id', 'coa_level', 'coa_parent', 'coa_desc')
                    ->where('coa_id', 'like', $coa_id.'%')
                    ->get();

        try {
            $output     = [
                'status'    => 'berhasil',
                'message'   => 'Berhasil Mengambil Data COA',
                'data'      => $query,
            ];
        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());

            $output     = [
                'status'    => 'gagal',
                'message'   => 'Gagal Mengambil Data Master COA',
                'data'      => [],
            ];
        }

        return $output;
    }

    // 19 FEBRUARI 2025
    // NOTE : SIMPAN MASTER COA 
    public static function do_simpan_coa($data)
    {
        DB::beginTransaction();

        $data_coa   = $data['data'];
        $type       = $data['type'];
        $user_id    = $data['user_id'];
        $ip_address = $data['ip_address'];
        $today      = $data['today'];

        if($type == 'add') {
            // CHECK APAKAH ADA ID YANG SAMA?
            $check_coa      = DB::table('fin_mas_coa')->where('coa_id', '=', $data_coa['coa_id_parent'].$data_coa['coa_id_child'])->get();

            if(count($check_coa) < 1) {
                $data_insert    = [
                    'coa_id'    => $data_coa['coa_id_parent'].$data_coa['coa_id_child'],
                    'coa_desc'  => $data_coa['coa_description'],
                    'coa_level' => $data_coa['coa_id_level'],
                    'coa_parent'=> str_replace('.', '', $data_coa['coa_id_parent']),
                    'created_by'=> $user_id,
                    'updated_by'=> $user_id,
                    'created_at'=> $today,
                    'updated_at'=> $today,
                ];
    
                DB::table('fin_mas_coa')->insert($data_insert);
    
                try {
                    $output     = [
                        'is_success'    => true,
                        'status_code'   => 201,
                        'message'       => 'Berhasil Menambahkan Data CoA Baru ' . $data_insert['coa_id'],
                        'data'          => $data_insert['coa_id'],
                        'errMsg'        => '',
                    ];
                    DB::commit();
    
                    LogHelper::create('add', $output['message'], $ip_address);
                } catch (\Exception $e) {
                    $output     = [
                        'is_success'    => false,
                        'status_code'   => 500,
                        'message'       => 'Internal Server Error',
                        'data'          => [],
                        'errMsg'        => $e->getMessage(),
                    ];
                    Log::channel('daily')->error($e->getMessage());
                    LogHelper::create('error_system', $output['message'], $ip_address);
                }
            } else {
                DB::rollback();
                $output     = [
                    'is_success'    => false,
                    'status_code'   => 401,
                    'message'       => 'Kode CoA : ' . $data_coa['coa_id_parent'].$data_coa['coa_id_child'] . ' Telah Tersedia Pada Sistem',
                    'data'          => [],
                    'errMsg'        => '',  
                ];
            }

            return $output;
        }
    }

    // 21 FEBRUARI 2025
    // NOTE : AMBIL LIST MASTER BANK
    public static function get_finance_list_bank()
    {
        $query  = DB::table('fin_mas_bank')->orderBy('bank_id', 'asc')->get();

        try {
            if(count($query) > 0 ) {
                $output     = [
                    'is_success'    => true,
                    'status_code'   => 200,
                    'message'       => 'Berhasil Mengambil Data List Bank',
                    'data'          => [
                        'total_data'    => count($query),
                        'data'          => $query,
                    ]
                ];
            } else {
                $output     = [
                    'is_success'    => true,
                    'status_code'   => 404,
                    'message'       => 'Tidak Ada Data Bank Pada Sistem',
                    'data'          => [
                        'total_data'    => 0,
                        'data'          => []
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            $output     = [
                'is_success'    => false,
                'status_code'   => 500,
                'message'       => 'Internal Server Error ' . $e->getMessage(),
                'data'          => []
            ];
        }

        return $output;
    }

    // NOTE : AMBIL LIST BANK ACCOUNT
    public static function get_finance_list_account_bank()
    {
        $query  = DB::table('fin_mas_bank_account as a')
                    ->join('fin_mas_bank as b', 'a.bank_id', '=', 'b.bank_id')
                    ->select(
                            'a.bank_account_id as account_id',
                            'a.bank_account_number as account_number',
                            'b.bank_name as account_bank_name',
                            'a.coa_id',
                            'a.bank_account_currency as account_currency',
                            'a.is_active as account_is_active',
                            )
                    ->get();

        try {
            if(count($query) > 0 ) {
                $output     = [
                    'is_success'    => true,
                    'status_code'   => 200,
                    'message'       => 'Berhasil Mengambil Data List Account Bank',
                    'data'          => [
                        'total_data'    => count($query),
                        'data'          => $query,
                    ]
                ];
            } else {
                $output     = [
                    'is_success'    => true,
                    'status_code'   => 404,
                    'message'       => 'Tidak Ada Data Account Bank Pada Sistem',
                    'data'          => [
                        'total_data'    => 0,
                        'data'          => []
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            $output     = [
                'is_success'    => false,
                'status_code'   => 500,
                'message'       => 'Internal Server Error ' . $e->getMessage(),
                'data'          => []
            ];
        }

        return $output;
    }

    // NOTE : GET SELECTED BANK ACCOUNT
    public static function get_data_selected_bank_account($bank_account_id)
    {
        $query  = DB::table('fin_mas_bank_account')
                    ->select('bank_id', 'coa_id', 'bank_account_number', 'bank_account_currency')
                    ->where('bank_account_id', '=', $bank_account_id)
                    ->get();
        
        try {
            if(count($query) > 0) {
                $output     = [
                    'is_success'    => true,
                    'status_code'   => 200,
                    'message'       => 'Berhasi Mengambil Data Bank Account ID : ' . $bank_account_id,
                    'data'          => $query[0],
                ];
            } else {
                $output     = [
                    'is_success'    => false,
                    'status_code'   => 404,
                    'message'       => 'Gagal Mengambil Data Bank Account ID : ' . $bank_account_id,
                    'data'          => []
                ];
            }
        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            
            $output     = [
                'is_success'    => false,
                'status_code'   => 500,
                'message'       => 'Internal Server Error',
                'data'          => []
            ];
        }

        return $output;
    }
    // NOTE : TRANSACTION BANK ACCOUNT
    public static function do_save_bank_account($type, $data)
    {
        $ip_address     = $data['ip_address'];
        $user_id        = $data['user_id'];
        
        $bank_id        = $data['data']['acb_bank_id'];
        $coa_id         = $data['data']['acb_coa_id'] == 999 ? null : $data['data']['acb_coa_id'];
        $currency       = $data['data']['acb_currency'];
        $account_bank   = $data['data']['acb_bank_account'];

        DB::beginTransaction();

        if($type == 'add') {
            $data_simpan    = [
                'bank_id'               => $bank_id,
                'coa_id'                => $coa_id,
                'bank_account_number'   => $account_bank,
                'bank_account_currency' => $currency,
                'is_active'             => 't',
                'created_by'            => $user_id,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_by'            => $user_id,
                'updated_at'            => date('Y-m-d H:i:s'),
            ];

            DB::table('fin_mas_bank_account')->insert($data_simpan);

            try {
                DB::commit();

                $output     = [
                    'is_success'    => true,
                    'status_code'   => 200,
                    'message'       => 'Berhasil Menyimpan Data Bank Account Baru',
                    'data'          => [],
                ];
                
                LogHelper::create('add', $output['message'], $ip_address);
            } catch (\Exception $e) {
                DB::rollBack();

                $output     = [
                    'is_success'    => false,
                    'status_code'   => 500,
                    'message'       => 'Internal Server Error',
                    'data'          => []
                ];

                Log::channel('daily')->error($e->getMessage());
                LogHelper::create('error_system', $output['message'], $ip_address);
            }
        } else if($type == 'edit') {
            $data_where     = [
                'bank_account_id'   => $data['data']['acb_id'],
            ];

            $data_updpate   = [
                'bank_id'   => $data['data']['acb_bank_id'],
                'coa_id'    => $data['data']['acb_coa_id'] == 999 ? null : $data['data']['acb_coa_id'],
                'bank_account_number'   => $data['data']['acb_bank_account'],
                'bank_account_currency' => $data['data']['acb_currency'],
            ];

            DB::table('fin_mas_bank_account')->where($data_where)->update($data_updpate);
            
            try {
                DB::commit();

                $output     = [
                    'is_success'    => true,
                    'status_code'   => 201,
                    'message'       => 'Berhasil Mengubah Data Bank Account ID : ' . $data['data']['acb_id'],
                    'data'          => [],
                ];

                LogHelper::create('edit', $output['message'], $ip_address);
            } catch (\Exception $e) {
                DB::rollBack();

                $output     = [
                    'is_success'    => false,
                    'status_code'   => 500,
                    'message'       => 'Internal Server Error',
                    'data'          => []
                ];
                
                Log::channel('daily')->error($e->getMessage());
                LogHelper::create('error_system', $output['message'], $ip_address);
            }
        }

        return $output;
    }

    // 25 FEBRUARI 2025
    // NOTE : GET DATA PENGAJUAN KEUANGAN SUMMARY
    public static function get_pengajuan_keuangan_umhaj($data)
    {
        $tahun  = $data['tahun'];
        $bulan  = $data['bulan'];

        // GET DATA FROM UMHAJ
        $query  = DB::connection('umhaj_percik')
                    ->table('uang as a')
                    ->join('uang_detail as b', 'a.ID', '=', 'b.IDUANG')
                    ->select(
                            'a.ID as pengajuan_id',
                            'a.NOMOR as pengajuan_nomor_surat',
                            'a.NAMA as pengaju_nama',
                            'a.TGL as pengajuan_tanggal',
                            DB::raw('UPPER(a.UNTUK) as pengajuan_deskripsi'),
                            DB::raw('COUNT(b.ID) as total_item'),
                            DB::raw('SUM(b.JUMLAH) as total_pengajuan'),
                            'a.CURRENCY as pengajuan_mata_uang'
                            )
                    ->where(DB::raw('EXTRACT(YEAR FROM a.TGL)'), '=', $tahun)
                    ->where(DB::raw('EXTRACT(MONTH FROM a.TGL)'), '=', $bulan)
                    ->groupBy('a.ID', 'a.NOMOR', 'a.UNTUK', 'a.TGL', 'a.CURRENCY', 'a.NAMA')
                    ->orderBy('a.CREATED_DATE', 'desc')
                    ->get();
        
        try {
            if(count($query) > 0) {
                $output     = [
                    'status_code'   => 201,
                    'is_success'    => true,
                    'message'       => 'Berhasil Mengambil Data Pengajuan Keuangan Bulan ' . $bulan . ' Tahun ' . $tahun,
                    'data'          => $query,
                ];
            } else {
                $output     = [
                    'status_code'   => 404,
                    'is_success'    => true,
                    'message'       => 'Tidak Ada Data Pengajuan Keuangan Pada Bulan ' . $bulan . ' Tahun ' . $tahun,
                    'data'          => []
                ];
            }
        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());

            $output     = [
                'status_code'   => 500,
                'is_success'    => false,
                'message'       => 'Internal Server Error',
                'data'          => []
            ];
        }

        return $output;
    }

    // NOTE : GET DATA PENGAJUAN KEUANGAN HEADER & DETAIL
    public static function get_pengajuan_keuangan_detail_umhaj($id_pengajuan)
    {
        $query_header = DB::connection('umhaj_percik')
                            ->table('uang as a')
                            ->join('uang_detail as b', 'a.ID', '=', 'b.IDUANG')
                            ->select(
                                    'a.NOMOR as pengajuan_nomor',
                                    'a.NAMA as pengaju_nama',
                                    'a.UNTUK as pengajuan_deskripsi',
                                    'a.JENISBAYAR as pengajuan_metode',
                                    'a.REKENING as pengajuan_rekening',
                                    'a.FILELOKASI as pengajuan_file',
                                    'a.TGL as pengajuan_tanggal',
                                    'a.JENISBAYAR as pengajuan_metode',
                                    'a.CURRENCY as pengajuan_mata_uang',
                                    DB::raw('SUM(b.JUMLAH) as pengajuan_total')
                                    )
                            ->where('a.ID', '=', $id_pengajuan)
                            ->groupBy('a.NOMOR', 'a.NAMA', 'a.UNTUK', 'a.JENISBAYAR', 'a.REKENING', 'a.FILELOKASI', 'a.TGL', 'a.JENISBAYAR', 'a.CURRENCY')
                            ->get();

        $query_detail   = DB::connection('umhaj_percik')
                            ->table('uang_detail as a')
                            ->join('uang as b', 'a.IDUANG', '=', 'b.id')
                            ->select(
                                    'a.ID as pengajuan_detail_id',
                                    'a.URAIAN as pengajuan_detail_deskripsi',
                                    'a.JUMLAH as pengajuan_detail_jumlah',
                                    'b.CURRENCY as pengajuan_detail_currency'
                                    )
                            ->where('IDUANG', '=', $id_pengajuan)
                            ->get();
        
        try {
            if(count($query_header) > 0) {
                $output     = [
                    'is_success'    => true,
                    'status_code'   => 201,
                    'message'       => 'Berhasil Mengambil Data Pengajuan Keuangan',
                    'data'          => [
                        'header'    => $query_header,
                        'detail'    => $query_detail,
                    ]
                ];
            } else {
                $output     = [
                    'is_success'    => false,
                    'status_code'   => 404,
                    'message'       => 'Data Pengajuan Keuangan Tidak Ditemukan',
                    'data'          => [
                        'header'        => [],
                        'detail'        => [],
                    ],
                ];
            }
        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            
            $output     = [
                'is_success'    => false,
                'status_code'   => 500,
                'message'       => 'Internal Server Error',
                'data'          => [],
            ];
        }

        return $output;
    }

    // NOTE : GET DATA CURRENCY LIST
    public static function get_finance_currency_master($data)
    {
        $limit      = $data['limit'];
        $order_by   = $data['orderBy'];

        $query      = DB::table('fin_mas_currency')
                        ->select('id', 'curr_start_date', 'curr_to_value_low', 'curr_to_value_high',)
                        ->orderBy('curr_start_date', $order_by)
                        ->limit($limit)
                        ->get();

        try {
            if(count($query) > 0) {
                $output     = [
                    'is_success'    => true,
                    'status_code'   => 200,
                    'message'       => 'Berhasil Mengambil Data Kurs',
                    'data'          => $query,
                ];
            } else {
                $output     = [
                    'is_success'    => true,
                    'status_code'   => 404,
                    'message'       => 'Data Kurs Tidak Ditemukan',
                    'data'          => []
                ];
            }
        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());

            $output     = [
                'is_success'    => false,
                'status_code'   => 500,
                'message'       => 'Internal Server Error',
                'data'          => []
            ];
        }

        return $output;
    }
    
    // 26 FEBRUARI 2025
    // NOTE : TRANS FINANCE MASTER CURRENCY
    public static function trans_finance_currency_master($data)
    {
        $ip_address     = $data['ip_address'];
        $user_id        = $data['user_id'];
        $user_name      = $data['user_name'];
        $type           = $data['type'];
        $curr_data      = $data['data'];
        $today          = date('Y-m-d H:i:s');

        DB::beginTransaction();
        DB::connection('umhaj_percik')->beginTransaction();

        if($type == 'add') {
            // INSERT KE FIN MASTER CURR
            $data_insert    = [
                'curr_from'         => 'USD',
                'curr_to'           => 'IDR',
                'curr_from_value'   => 1,
                'curr_to_value_low' => $curr_data['kurs_value_low'],
                'curr_to_value_high'=> $curr_data['kurs_value_high'],
                'curr_start_date'   => $curr_data['kurs_start_date'],
                'curr_end_date'     => $curr_data['kurs_start_date'],
                'curr_note'         => '',
                'created_by'        => $user_id,
                'created_date'      => $today,
                'updated_by'        => $user_id,
                'updated_date'      => $today
            ];

            DB::table('fin_mas_currency')->insert($data_insert);

            // INSERT TO UMHAJ
            $data_insert_umhaj  = [
                'TERTINGGI'     => $curr_data['kurs_value_high'],
                'TERENDAH'      => $curr_data['kurs_value_low'],
                'TANGGAL'       => $curr_data['kurs_start_date'],
                'CREATED_BY'    => $user_name,
                'CREATED_DATE'  => $today,
            ];

            DB::connection('umhaj_percik')->table('kurs')->insert($data_insert_umhaj);

            try {
                DB::commit();
                DB::connection('umhaj_percik')->commit();

                $output     = [
                    'is_success'    => true,
                    'status_code'   => 201,
                    'message'       => 'Berhasil Menambahkan Kurs Tanggal : ' . $curr_data['kurs_start_date'],
                    'data'          => [],
                ];

                LogHelper::create('add', $output['message'], $ip_address);
            } catch (\Exception $e) {
                DB::rollBack();
                DB::connection('umhaj_percik')->rollBack();
                
                $output     = [
                    'is_success'    => false,
                    'status_code'   => 500,
                    'message'       => 'Internal Server Error',
                    'data'          => []
                ];
                Log::channel('daily')->error($e->getMessage());
                LogHelper::create('error_system', $output['message'], $ip_address);
            }

        } else if($type == 'edit') {
            $data_where     = [
                'id'    => $curr_data['kurs_id'],
            ];
            
            $data_update    = [
                'curr_to_value_low' => $curr_data['kurs_value_low'],
                'curr_to_value_high'=> $curr_data['kurs_value_high'],
            ];

            DB::table('fin_mas_currency')->where($data_where)->update($data_update);

            // GET ID KURS FROM UMHAJ
            $get_kursID_umhaj   = DB::connection('umhaj_percik')->table('kurs')->select('ID')->where('TANGGAL', '=', $curr_data['kurs_start_date'])->get();
            if(count($get_kursID_umhaj) > 0) {
                $kursID_umhaj   = $get_kursID_umhaj[0]->ID;

                $data_where_umhaj   = [
                    'ID'    => $kursID_umhaj
                ];

                $data_update_umhaj  = [
                    'TERTINGGI' => $curr_data['kurs_value_low'],
                    'TERENDAH'  => $curr_data['kurs_value_high'],
                    'UPDATED_BY'=> $user_name,
                    'UPDATED_DATE'  => $today
                ];

                DB::connection('umhaj_percik')->table('kurs')->where($data_where_umhaj)->update($data_update_umhaj);
            }

            try {
                DB::commit();
                DB::connection('umhaj_percik')->commit();

                $output     = [
                    'is_success'    => true,
                    'status_code'   => 201,
                    'message'       => 'Berhasil Mengubah Data Currency',
                    'data'          => [],
                ];
                LogHelper::create('edit', $output['message'], $ip_address);
            } catch (\Exception $e) {
                DB::rollback();
                DB::connection('umhaj_percik')->rollBack();

                $output     = [
                    'is_success'    => false,
                    'status_code'   => 500,
                    'message'       => 'Internal Server Error',
                    'data'          => [],
                ];

                Log::channel('daily')->error($e->getMessage());
                LogHelper::create('error_system', $output['message'], $ip_address);
            }
        }

        return $output;
    }

    // NOTE : AMBIL CURRENCY DETAIL
    public static function get_finance_currency_detail($id)
    {
        $query  = DB::table('fin_mas_currency')    
                ->select('id', 'curr_start_date', 'curr_to_value_low', 'curr_to_value_high')
                ->where('id', '=', $id)
                ->get();

        try {
            if(count($query) > 0) {
                $output     = [
                    'is_success'    => true,
                    'status_code'   => 200,
                    'message'       => 'Berhasil Mengambil Data Currency',
                    'data'          => $query
                ];
            } else {
                $output     = [
                    'is_success'    => false,
                    'status_code'   => 404,
                    'message'       => 'Tidak Ada Data Currency dengan ID ' . $id,
                    'data'          => []
                ];
            }
        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());

            $output     = [
                'is_success'    => false,
                'status_code'   => 500,
                'message'       => 'Internal Server Error',
                'data'          => [],
            ];
        }

        return $output;
    }

    // NOTE : GET LIST HAJI DATA BY JEMAAH ID
    public static function do_get_no_daftar_jemaah($data)
    {
        $jemaah_id  = $data['jemaah_id'];
        $haji_kode  = $data['haji_kode'];

        $query  = DB::connection('umhaj_percik')
                    ->table('haji')
                    ->select('ID as haji_id', 'ID_MEMBER as jemaah_id', 'NAMA as jemaah_nama', 'NO_DAFTAR as no_daftar', 'JENIS_UMRAH as kode_keberangkatan', 'TGL_BERANGKAT as tgl_keberangkatan', 'ROOM as haji_paket', 'SPPH as no_spph', 'TGL_SPPH as tgl_spph', 'BPIH_NO as no_bpih', 'BPIH_TGL as tgl_bpih')
                    ->where('ID_MEMBER', '=', $jemaah_id)
                    ->where('JENIS_UMRAH', 'LIKE', $haji_kode)
                    ->orderBy('ID', 'asc')
                    ->get();

        try {
            if(count($query) > 0) {
                // CONDITION
                $price  = 0;
                for($i = 0; $i < count($query); $i++) {
                    switch ($query[$i]->haji_paket) {
                        case 'Quad' : 
                            $price  = 17500;
                        break;
                        case 'Triple' :
                            $price  = 18500;
                        break;
                        case 'Quad' :
                            $price  = 20000;
                        break;
                    }
                    $condition_data[]     = [
                        "haji_id"               => $query[$i]->haji_id,
                        "haji_paket"            => $query[$i]->haji_paket,
                        "haji_harga"            => $price,
                        "jemaah_id"             => $query[$i]->jemaah_id,
                        "jemaah_nama"           => $query[$i]->jemaah_nama,
                        "kode_keberangkatan"    => $query[$i]->kode_keberangkatan,
                        "no_bpih"               => $query[$i]->no_bpih,
                        "no_daftar"             => $query[$i]->no_daftar,
                        "no_spph"               => $query[$i]->no_spph,
                        "tgl_bpih"              => $query[$i]->tgl_bpih == "0000-00-00" ? "" : date('d/M/Y', strtotime($query[$i]->tgl_bpih)),
                        "tgl_keberangkatan"     => $query[$i]->tgl_keberangkatan == "0000-00-00" ? "" : date('d/M/Y', strtotime($query[$i]->tgl_keberangkatan)),
                        "tgl_spph"              => $query[$i]->tgl_spph == "0000-00-00" ? "" : date('d/M/Y', strtotime($query[$i]->tgl_spph)),
                    ];
                }
                $output     = [
                    'is_success'    => true,
                    'status_code'   => 200,
                    'message'       => 'Berhasil Mengambil Data Haji',
                    'data'          => $condition_data,
                ];
            } else {
                $output     = [
                    'is_success'    => true,
                    'status_code'   => 404,
                    'message'       => 'Tidak Ada Data Haji',
                    'data'          => []
                ];
            }
        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());

            $output     = [
                'is_success'    => false,
                'status_code'   => 500,
                'message'       => 'Gagal Mengambil Data Haji',
                'data'          => []
            ];
        }

        return $output;
    }

    // NOTE : SIMPAN TRANS PEMBAYARAN HAJI
    public static function do_simpan_pembayaran_haji($type, $data)
    {
        $ip_address         = $data['ip_address'];
        $user_id            = $data['user_id'];
        $haji_data_header   = $data['data']['header'];
        $haji_data_detail   = $data['data']['detail'];
        $today              = date('Y-m-d');

        DB::beginTransaction();
        
        if($type == 'add') {
            // GENERATE TRANS ID
            $query_trans_id     = DB::table('fin_trans_haji')
                                    ->select(DB::raw("CAST(SUBSTRING_INDEX(hj_trans_id, '-', -1) AS UNSIGNED) AS last_trans_number"))
                                    ->where(DB::raw('EXTRACT(YEAR FROM hj_create_date)'), '=', date('Y', strtotime($today)))
                                    ->where(DB::raw('EXTRACT(MONTH FROM hj_create_date)'), '=', date('m', strtotime($today)))
                                    ->orderBy(DB::raw("CAST(SUBSTRING_INDEX(hj_trans_id, '-', -1) AS UNSIGNED)"), 'desc')
                                    ->limit(1)
                                    ->get();
            
            if(count($query_trans_id) > 0) {
                $last_number_trans_hj   = $query_trans_id[0]->last_trans_number;
                $new_number_trans_hj    = (int) $last_number_trans_hj + 1;
                $haji_trans_id      = "HJ/" . date('Ym', strtotime($today)) . "-". str_pad($new_number_trans_hj, 4, 0, STR_PAD_LEFT);
            } else {
                $haji_trans_id      = "HJ/" . date('Ym', strtotime($today)) . "-0001";
            }

            // SIMPAN DATA HEADER
            $insert_data_header = [
                'hj_trans_id'           => $haji_trans_id,
                'hj_create_date'        => $today,
                'hj_trans_member_id'    => $haji_data_header['jemaah_id'],
                'hj_trans_member_name'  => $haji_data_header['jemaah_nama'],
                'hj_tour_code'          => $haji_data_header['tour_code'],
                'hj_no_daftar'          => $haji_data_header['no_daftar'],
                'hj_tgl_daftar'         => $haji_data_header['tgl_daftar'],
                'hj_no_bpih'            => $haji_data_header['no_bpih'],
                'hj_paket'              => $haji_data_header['paket'],
                'hj_est_berangkat'      => $haji_data_header['estimasi_berangkat'],
                'created_by'            => $user_id,
                'created_date'          => date('Y-m-d H:i:s'),
                'updated_by'            => $user_id,
                'updated_date'          => date('Y-m-d H:i:s'),
            ];
            DB::table('fin_trans_haji')->insert($insert_data_header);

            // SIMPAN DATA DETAIL
            if(count($haji_data_detail) > 0) {
                for($i = 0; $i < count($haji_data_detail); $i++) {
                    $insert_data_detail     = [
                        'hj_trans_id'           => $haji_trans_id,
                        'hj_seq'                => $haji_data_detail[$i]['seq'],
                        'hj_payment_date'       => $haji_data_detail[$i]['tgl_bayar'],
                        'hj_payment_method'     => $haji_data_detail[$i]['metode_bayar'],
                        'hj_bank_account_id'    => $haji_data_detail[$i]['no_rekening'],
                        'hj_payment_amount'     => $haji_data_detail[$i]['jml_bayar'],
                        'hj_payment_currency'   => $haji_data_detail[$i]['mata_uang'],
                        'hj_payment_note'       => '',
                        'created_by'            => $user_id,
                        'created_date'          => date('Y-m-d H:i:s'),
                        'updated_by'            => $user_id,
                        'updated_date'          => date('Y-m-d H:i:s'),
                    ];
                    DB::table('fin_trans_haji_detail')->insert($insert_data_detail);
                }
            }

            try {
                $output     = [
                    'status_code'   => 200,
                    'is_success'    => true,
                    'message'       => 'Berhasil Menambahkan Data Pembayaran Haji',
                    'data'          => [],
                ];

                DB::commit();

                LogHelper::create('add', $output['message'] . ' ID : ' . $haji_trans_id, $ip_address);
            } catch (\Exception $e) {
                $output     = [
                    'status_code'   => 500,
                    'is_success'    => false,
                    'message'       => 'Gagal Menambahkan Data Pembayaran Haji',
                    'data'          => []
                ];
                
                DB::rollBack();
                
                Log::channel('daily')->error($e->getMessage());
                LogHelper::create('error_system', $output['message'], $ip_address);
            }
        } else if($type == 'edit') {
            // DELETE DETAIL
            DB::table('fin_trans_haji_detail')->where('hj_trans_id', '=', $haji_data_header['trans_id'])->delete();

            // INSERT DETAIL
            for($i = 0; $i < count($haji_data_detail); $i++) {
                $data_insert_detail     = [
                    'hj_trans_id'           => $haji_data_header['trans_id'],
                    'hj_seq'                => $haji_data_detail[$i]['seq'],
                    'hj_payment_date'       => $haji_data_detail[$i]['tgl_bayar'],
                    'hj_payment_method'     => $haji_data_detail[$i]['metode_bayar'],
                    'hj_bank_account_id'    => $haji_data_detail[$i]['no_rekening'],
                    'hj_payment_amount'     => $haji_data_detail[$i]['jml_bayar'],
                    'hj_payment_currency'   => $haji_data_detail[$i]['mata_uang'],
                    'hj_payment_note'       => '',
                    'created_by'            => $user_id,
                    'created_date'          => date('Y-m-d H:i:s'),
                    'updated_by'            => $user_id,
                    'updated_date'          => date('Y-m-d H:i:s') 
                ];

                DB::table('fin_trans_haji_detail')->insert($data_insert_detail);
            }

            try {
                DB::commit();

                $output     = [
                    'is_success'    => true,
                    'status_code'   => 201,
                    'message'       => 'Berhasil Mengubah Data Pembayaran Haji',
                    'data'          => []
                ];

                LogHelper::create('edit', $output['message'] . ' ID : ' . $haji_data_header['trans_id'], $ip_address);
            } catch (\Exception $e) {
                DB::rollBack();
                $output     = [
                    'is_success'    => false,
                    'status_code'   => 500,
                    'message'       => 'Gagal Mengubah Data Pembayaran Haji',
                    'data'          => []
                ];

                Log::channel('daily')->error($e->getMessage());
                LogHelper::create('error_system', $output['message'], $ip_address);
            }
        }

        return $output;
    }

    // NOTE : AMBIL PEMBAYARAN HAJI
    public static function get_data_pembayaran_haji()
    {
        $query  = DB::table('fin_trans_haji as a')
                    ->join('fin_trans_haji_detail as b', 'a.hj_trans_id', '=', 'b.hj_trans_id')
                    ->select(
                            'a.hj_trans_id as trans_id', 
                            'a.hj_trans_member_id as jemaah_id', 
                            'a.hj_trans_member_name as jemaah_name', 
                            'a.hj_tour_code as tour_code', 
                            'a.hj_no_daftar as no_daftar', 
                            'a.hj_tgl_daftar as tgl_daftar', 
                            'a.hj_no_bpih as no_bpih', 
                            'a.hj_paket as jemaah_pkg', 
                            DB::raw("SUM(b.hj_payment_amount) as total_payment"),
                            'a.hj_est_berangkat as est_berangkat'
                            )
                    ->groupBy('a.hj_trans_id', 'a.hj_trans_member_id', 'a.hj_trans_member_name', 'a.hj_tour_code', 'a.hj_no_daftar', 'a.hj_tgl_daftar', 'a.hj_no_bpih', 'a.hj_paket', 'a.hj_est_berangkat')
                    ->orderBy('a.hj_trans_id', 'desc')
                    ->get();
        
        try {
            if(count($query) > 0) {
                $output     = [
                    'is_success'    => true,
                    'status_code'   => 200,
                    'message'       => 'Berhasil Mengambil Data Pembayaran Haji',
                    'data'          => $query,
                ];
            } else {
                $output     = [
                    'is_success'    => true,
                    'status_code'   => 404,
                    'message'       => 'Tidak Ada Data Pembayaran Haji',
                    'data'          => []
                ];
            }
        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());

            $output     = [
                'is_success'    => true,
                'status_code'   => 500,
                'message'       => 'Gagal Mengambil Data Pembayaran Haji',
                'data'          => []
            ];
        }

        return $output;
    }

    // 04 MARET 2025
    // NOTE : AMBIL DATA PEMBAYARAN HAJI
    public static function do_get_data_pembayaran_detail_haji_jemaah($trans_id)
    {
        // HEADER
        $query_header   = DB::table('fin_trans_haji as a')
                            ->select(
                                    'a.hj_trans_id', 
                                    'a.hj_tgl_daftar', 
                                    'a.hj_trans_member_id', 
                                    'a.hj_trans_member_name', 
                                    'a.hj_tour_code', 
                                    'a.hj_no_daftar', 
                                    'a.hj_tgl_daftar', 
                                    'a.hj_no_bpih', 
                                    'a.hj_paket', 
                                    'a.hj_est_berangkat',
                                    DB::raw('SUM(b.hj_payment_amount) as hj_total_payment')
                                )
                            ->join('fin_trans_haji_detail as b', 'a.hj_trans_id', '=', 'b.hj_trans_id')
                            ->where('a.hj_trans_id', '=', $trans_id)
                            ->groupBy('a.hj_trans_id', 'a.hj_tgl_daftar', 'a.hj_trans_member_id', 'a.hj_trans_member_name', 'a.hj_tour_code', 'a.hj_no_daftar', 'a.hj_tgl_daftar', 'a.hj_no_bpih', 'a.hj_paket')
                            ->get();
                            
        $query_detail   = DB::table('fin_trans_haji_detail')
                            ->select('hj_seq as seq', 'hj_payment_method as payment_method', 'hj_payment_date as payment_date', 'hj_payment_currency as payment_curr', 'hj_payment_amount as payment_amount', 'hj_bank_account_id as bank_account_id')
                            ->where('hj_trans_id', '=', $trans_id)
                            ->get();
        try {
            if(count($query_header) > 0 && count($query_detail) > 0) {
                switch ($query_header[0]->hj_paket) {
                    case 'Double' :
                        $room_price     = 20000;
                    break;
                    case 'Triple' :
                        $room_price     = 18500;
                    break;
                    case 'Quad' :
                        $room_price     = 17500;
                    break;
                    default :
                        $room_price     = 0;
                }

                $header     = [
                    'payment_id'            => $query_header[0]->hj_trans_id,
                    'payment_date'          => !empty($query_header[0]->hj_tgl_daftar) ? date('d/M/Y', strtotime($query_header[0]->hj_tgl_daftar)) : "",
                    'jemaah_id'             => $query_header[0]->hj_trans_member_id,
                    'jemaah_name'           => $query_header[0]->hj_trans_member_name,
                    'tour_code'             => $query_header[0]->hj_tour_code,
                    'no_daftar'             => $query_header[0]->hj_no_daftar,
                    'tgl_daftar'            => !empty($query_header[0]->hj_tgl_daftar) ? date('d/M/Y', strtotime($query_header[0]->hj_tgl_daftar)) : "",
                    'no_bpih'               => $query_header[0]->hj_no_bpih,
                    'jemaah_pkg'            => $query_header[0]->hj_paket,
                    'jemaah_total_bayar'    => $query_header[0]->hj_total_payment, 
                    'jemaah_harga_paket'    => $room_price,
                    'jemaah_status_bayar'   => $query_header[0]->hj_total_payment < $room_price ? 'Kurang Bayar' : ($query_header[0]->hj_total_payment > $room_price ? 'Lebih Bayar' : 'Lunas'),
                    'estimasi_keberangkatan'=> $query_header[0]->hj_est_berangkat,
                ];

                $output     = [
                    'is_success'    => true,
                    'status_code'   => 200,
                    'message'       => 'Berhasil Mengambil Data Pembayaran Haji',
                    'data'          => [
                        'header'        => $header,
                        'detail'        => $query_detail,
                    ],
                ];
            } else {
                $output     = [
                    'is_success'    => false,
                    'status_code'   => 404,
                    'message'       => 'Tidak Ada Data Pembayaran Haji',
                    'data'          => [
                        'header'        => [],
                        'detail'        => [],
                    ]
                ];
            }

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());

            $output     = [
                'is_success'    => false,
                'status_code'   => 500,
                'message'       => 'Gagal Mengambil Data Pembayaran Haji',
                'data'          => [
                    'header'        => [],
                    'detail'        => [],
                ]
            ];
        }

        return $output;
    }

    // NOTE : AMBIL REPORT PEMBAYARAN HAJI
    public static function get_data_report_haji_payment($tahun_cari)
    {
        $query_header   = DB::table('fin_trans_haji')
                            ->select('hj_trans_id', 'hj_trans_member_name', 'hj_no_bpih', 'hj_paket')
                            ->where('hj_est_berangkat', '=', $tahun_cari)
                            ->orderBy('hj_trans_id', 'asc')
                            ->get();
        
        $query_detail   = DB::table('fin_trans_haji as a')
                            ->join('fin_trans_haji_detail as b', 'a.hj_trans_id', '=', 'b.hj_trans_id')
                            ->leftJoin('fin_mas_bank_account as c', 'b.hj_bank_account_id', '=', 'c.bank_account_id')
                            ->leftJoin('fin_mas_bank as d', 'c.bank_id', '=', 'd.bank_id')
                            ->select('a.hj_trans_id', 'b.hj_seq', 'b.hj_payment_date', 'b.hj_payment_method', 'b.hj_bank_account_id', 'b.hj_payment_amount', 'b.hj_payment_currency', 'd.bank_name', 'c.bank_account_number')
                            ->where('a.hj_est_berangkat', '=', $tahun_cari)
                            ->orderBy('a.hj_trans_id', 'asc')
                            ->orderBy('b.hj_seq', 'asc')
                            ->get();

        try {
            if(count($query_header) > 0) {
                $output     = [
                    'is_success'    => true,
                    'status_code'   => 200,
                    'message'       => 'Berhasil Megambil Data Pembayaran Haji Tahun : ' . $tahun_cari,
                    'data'          => [
                        'header'    => $query_header,
                        'detail'    => $query_detail,  
                    ]
                ];
            } else {
                $output      = [
                    'is_success'    => true,
                    'status_code'   => 404,
                    'message'       => 'Tidak Ada Data Pembayaran Haji Tahun : ' . $tahun_cari,
                    'data'          => [
                        'header'    => [],
                        'detail'    => [],
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());

            $output     = [
                'is_success'    => false,
                'status_code'   => 500,
                'message'       => 'Gagal Mengambil Data Pembayraran Haji',
                'data'          => [
                    'header'    => [],
                    'detail'    => []
                ]
            ];
        }

        return $output;
    }
}

?>