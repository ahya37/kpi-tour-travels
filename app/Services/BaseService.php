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
    public static function doGetCurrentSubDivision($role, $userID)
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

    public static function doAbsen($data)
    {
        date_default_timezone_set('Asia/Jakarta');
        DB::beginTransaction();

        $today      = date('Y-m-d');
        $user_id    = $data['data']['prs_user_id'];
        $jenis      = $data['data']['prs_status'];
        $ip         = $data['ip'];
        
        if($jenis == 'masuk')
        {
            $do_check       = DB::select(
                "
                SELECT  *
                FROM    tm_presence
                WHERE   prs_date = '$today'
                AND     prs_user_id  = '$user_id'
                "
            );
            if(count($do_check) < 1) {
                 // INSERT TO TABLE
                $data_insert    = [
                    "prs_date"          => $data['data']['prs_date'],
                    "prs_user_id"       => $data['data']['prs_user_id'],
                    "prs_in_time"       => $data['data']['prs_start_time'],
                    "prs_in_file"       => $data['data_url'],
                    "prs_in_location"   => $data['data']['prs_lat'].", ".$data['data']['prs_long'],
                    "created_by"        => $data['data']['prs_user_id'],
                    "created_at"        => date('Y-m-d H:i:s'),
                    "updated_by"        => $data['data']['prs_user_id'],
                    "updated_at"        => date('Y-m-d H:i:s'),
                ];

                DB::table('tm_presence')->insert($data_insert);
            } else {
                DB::rollBack();
                $output     = [
                    "status"    => "duplikat",
                    "errMsg"    => "Absen 2x dalam satu hari tidak diperbolehkan",
                ];

                return $output;
            }
        } else if($jenis == 'keluar') {
            // CHECK DATA
            $do_check   = DB::select(
                "
                SELECT 	*
                FROM 	tm_presence
                WHERE 	prs_date = '$today'
                AND 	prs_in_time IS NOT NULL
                AND 	prs_out_time IS NULL
                "
            );

            if(count($do_check) > 0) {
                $data_where     = [
                    "prs_date"      => $data['data']['prs_date'],
                    "prs_user_id"   => $data['data']['prs_user_id'],
                ];

                $data_update    = [
                    "prs_out_time"      => $data['data']['prs_end_time'],
                    "prs_out_file"      => $data['data_url'],
                    "prs_out_location"  => $data['data']['prs_lat'].", ".$data['data']['prs_long'],
                    "updated_by"        => $data['data']['prs_user_id'],
                    "updated_at"        => date('Y-m-d H:i:s'),
                ];

                DB::table('tm_presence')
                    ->where($data_where)
                    ->whereNull('prs_out_time')
                    ->update($data_update);
            } else {
                DB::rollBack();
                $output     = [
                    "status"    => "duplikat",
                    "errMsg"    => "Absen 2x dalam satu hari tidak diperbolehkan",
                ];

                return $output;
            }
        }

        try {
            DB::commit();
            if($jenis == 'masuk') {
                LogHelper::create('add', 'Berhasil Absen Masuk', $ip);
                $output     = [
                    "status"    => "berhasil",
                    "errMsg"    => ""
                ];
            } else if($jenis == 'keluar') {
                LogHelper::create('add', 'Berhasil Absen Keluar', $ip);
                $output     = [
                    "status"    => "berhasil",
                    "errMsg"    => "",
                ];
            }
        } catch(\Exception $e) {
            DB::rollBack();
            if($jenis == 'masuk') {
                LogHelper::create('error_system', 'Gagal Absen Masuk', $ip);
            } else if($jenis == 'keluar') {
                LogHelper::create('error_system', 'Gagal Absen Keluar', $ip);
            }
            Log::channel('daily')->error($e->getMessage());
            $output     = [
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            ];
        }

        return $output;
    }

    public static function doGetPresenceToday()
    {
        date_default_timezone_set('Asia/Jakarta');
        
        $today      = date('Y-m-d');
        $user_id    = Auth::user()->id;
        
        return DB::table('tm_presence')->where(['prs_date' => $today, 'prs_user_id' => $user_id])->get();
    }
}