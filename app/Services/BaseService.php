<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use Log;

class BaseService 
{
	public static function getDataGroupDivision()
    {
        $role   = Auth::user()->getRoleNames()[0] == ('admin' || 'umum') ? '%' : Auth::user()->getRoleNames()[0];
        
        $query  = DB::select(
            "
            SELECT  gd.*
            FROM    group_divisions gd
            JOIN    roles r ON gd.roles_id = r.id
            WHERE   r.name LIKE '$role'
            "
        );

        return $query;
    }

    public static function getDataEmployeeByGroupDivision($groupDivisionID)
    {
        $query  = DB::table('job_employees as a')
                    ->select('a.group_division_id','c.name as group_division_name','a.employee_id','b.name as employee_name')
                    ->join('employees as b','a.employee_id','=', 'b.id')
                    ->join('group_divisions as c','a.group_division_id','=','c.id')
                    ->join('roles as d', 'c.roles_id','=','d.id')
                    ->where('group_division_id','=', $groupDivisionID)
                    ->orderBy('c.name','ASC')
                    ->get()
                    ->toArray();
        return $query;
    }

    public static function doGetGroupDivisionWRole()
    {
        $query = DB::select(
            "
            SELECT 	a.id as gd_id,
                    a.name as gd_name,
                    b.id as role_id,
                    b.name as role_name
            FROM 	group_divisions a
            JOIN 	roles b ON a.roles_id = b.id
            ORDER BY a.name ASC
            "
        );
        
        return $query;
    }

    public static function doGetProgramUmrah($data, $program_name)
    {
        $valueCari  = $data->all()['sendData']['cari'];

        $query      = DB::select(
            "
            SELECT 	a.id as program_id,
                    a.name as program_name,
                    a.product_id as product_id,
                    b.name as product_name
            FROM 	programs a
            JOIN 	products b ON a.product_id = b.id
            WHERE 	a.id LIKE '$valueCari'
            AND 	(a.product_id LIKE '$program_name' or lower(b.name) LIKE '$program_name')

            UNION

            SELECT 	a.id as program_id,
                    a.name as program_name,
                    a.product_id as product_id,
                    b.name as product_name
            FROM 	programs a
            JOIN 	products b ON a.product_id = b.id
            WHERE 	a.id LIKE '$valueCari'
            AND 	(a.product_id LIKE 'Haji' or lower(b.name) LIKE 'Haji')
            "
        );

        return $query;
    }

    // 25 JUNI 2024
    // NOTE : GET DATA FOR doGetCurrentSubDivision
    public static function doGetCurrentSubDivision($role = '', $userID = '')
    {
        $query  = DB::select(
            "
            SELECT 	LOWER(c.name) AS sub_division_name
            FROM 	employees a
            JOIN 	job_employees b ON a.id = b.employee_id
            JOIN	sub_divisions c ON b.sub_division_id = c.id
            WHERE 	a.user_id = '$userID'
            "
        );

        return $query;
    }

    public static function doGetMasterProgram()
    {
        return DB::select(
            "
            SELECT  *
            FROM    master_program
            ORDER BY id ASC
            "
        );
    }

    public static function doGetPresenceToday()
    {
        date_default_timezone_set('Asia/Jakarta');
        
        $today      = date('Y-m-d');
        $user_id    = Auth::user()->id;
        
        return DB::table('tm_presence')->where(['prs_date' => $today, 'prs_user_id' => $user_id])->get();
    }

    // 22 MARET 2025
    // NOTE : ABSENSI V2
    public static function do_simpan_absensi($jenis, $data_absen)
    {
        $ip_address     = $data_absen['ip_address'];
        $today          = $data_absen['today'];
        $user_id        = $data_absen['user_id'];
        $data           = $data_absen['data_absen'];

        if($jenis == 'masuk') {
            // CHECK APAKAH SUDAH ADA ABSEN MASUK / BELOM
            $check_absen_masuk  = DB::table('tm_presence')
                                    ->where('prs_user_id', '=', $user_id)
                                    ->where('prs_date', '=', date('Y-m-d', strtotime($today)))
                                    ->get();

            if(count($check_absen_masuk) > 0) {
                $output     = [
                    'status_code'   => 422,
                    'is_success'    => false,
                    'message'       => 'Anda Sudah Absen Masuk',
                    'data'          => []
                ];
            } else {
                DB::beginTransaction();
                // SIMPAN ABSENSI
                $data_insert_absen  = [
                    'prs_date'          => $data['prs_date'],
                    'prs_user_id'       => $data['prs_user_id'],
                    'prs_in_time'       => $data['prs_start_time'],
                    'prs_in_location'   => $data['prs_lat'] . ', ' . $data['prs_long'],
                    'created_by'        => $data['prs_user_id'],
                    'created_at'        => $today,
                    'updated_by'        => $data['prs_user_id'],
                    'updated_at'        => $today,
                ];

                DB::table('tm_presence')->insert($data_insert_absen);

                try {
                    DB::commit();

                    $output     = [
                        'is_success'    => true,
                        'status_code'   => 201,
                        'message'       => 'Berhasil Absen Masuk',
                        'data'          => [],
                    ];

                    LogHelper::create('add', $output['message'] . ' Tanggal : ' . date('Y-m-d', strtotime($today)), $ip_address);
                } catch (\Exception $e) {
                    DB::rollBack();

                    $output     = [
                        'is_success'    => false,
                        'status_code'   => 500,
                        'message'       => 'Gagal Absen Masuk',
                        'data'          => []
                    ];

                    Log::channel('daily')->error($e->getMessage());
                    LogHelper::create('error_system', $output['message'] . ' Tanggal ' . date('Y-m-d', strtotime($today)), $ip_address);
                }
            }
        } else if($jenis == 'keluar') {
            // CHECK APAKAH SUDAH ADA JAM MASUK?
            $query_check    = DB::table('tm_presence')
                                ->where('prs_user_id', '=', $user_id)
                                ->where('prs_date', '=', date('Y-m-d', strtotime($today)))
                                ->whereNotNull('prs_in_time')
                                ->get();

            if(count($query_check) > 0) {
                DB::beginTransaction();
                // UPDATE DATA ABSEN
                $data_where_absen   = [
                    'prs_date'      => $data['prs_date'],
                    'prs_user_id'   => $data['prs_user_id'],
                ];
                
                $data_update_absen  = [
                    'prs_out_time'      => $data['prs_start_time'],
                    'prs_out_location'  => $data['prs_lat'] . ', ' . $data['prs_long'],
                    'updated_by'        => $data['prs_user_id'],
                    'updated_at'        => $today,
                ];

                DB::table('tm_presence')->where($data_where_absen)->whereNotNull('prs_in_time')->update($data_update_absen);

                try {
                    DB::commit();
                    
                    $output     = [
                        'is_success'    => true,
                        'status_code'   => 201,
                        'message'       => 'Berhasil Absen Keluar',
                        'data'          => []
                    ];
                    LogHelper::create('edit', $output['message'] . ' Tanggal : ' . date('Y-m-d', strtotime($today)), $ip_address);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::channel('daily')->error($e->getMessage());

                    $output     = [
                        'is_success'    => false,
                        'status_code'   => 500,
                        'message'       => 'Gagal Absen Keluar',
                        'data'          => []
                    ];

                    LogHelper::create('error_system', $output['message'] . ' Tanggal : ' . date('Y-m-d', strtotime($today)), $ip_address);
                }
            } else {
                $output     = [
                    'is_success'    => false,
                    'status_code'   => 422,
                    'message'       => 'Anda Belum Absen Masuk',
                    'data'          => [],
                ];
            }
        }

        return $output;
    }
}