<?php 

namespace App\Services;

use App\Helpers\LogHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
}

?>