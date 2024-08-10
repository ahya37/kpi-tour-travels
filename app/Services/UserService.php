<?php 

namespace App\Services;

use App\Helpers\LogHelper;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

date_default_timezone_set('Asia/Jakarta');

class UserService {

    public static function store($request)
    {
        $user = User::create([
            'name'     => $request['name'],
            'email'    => $request['email'],
            'password' => bcrypt($request['password'])
        ]);

        //assign roles to user
        return $user->assignRole($request['roles']);
    }

    public static function DoGetDataTableUserLog($idUser)
    {
        $query = DB::select(
            "
            SELECT  *,
                    CASE
                        WHEN log_type = '1' THEN 'Tambah Data'
                        WHEN log_type = '2' THEN 'Edit Data'
                        WHEN log_type = '3' THEN 'Hapus Data'
                        WHEN log_type = '4' THEN 'Error Sistem'
                    END as type_trans,   
                    CASE
                        WHEN log_type = '1' THEN 'label-success'
                        WHEN log_type = '2' THEN 'label-primary'
                        WHEN log_type = '3' THEN 'label-warning'
                        WHEN log_type = '4' THEN 'label-danger'
                    END as type_trans_color
            FROM    log_activity
            WHERE   log_user_id = '$idUser'
            ORDER BY log_date_time DESC
            "
        );

        return $query;
    }

    public static function doGetUserData($data)
    {
        date_default_timezone_set('Asia/Jakarta');

        $user_id    = $data['user_id'];
        $user_role  = $data['user_roles'];
        $curr_month = date('m');
        $curr_year  = date('Y');

        // GET USER PROFILES
        $query_user_profiles    = DB::select(
            "
            SELECT 	a.id as emp_id,
                    a.name as emp_name,
                    c.name as emp_group_div_name,
                    d.name as emp_sub_div_name,
				    a.pict_dir
            FROM 	employees a
            JOIN 	job_employees b ON b.employee_id = a.id
            JOIN 	group_divisions c ON b.group_division_id = c.id
            JOIN 	sub_divisions d ON (b.sub_division_id = d.id AND d.division_group_id = c.id)
            WHERE 	user_id = '$user_id'
            "
        );

        $query_total_act    = DB::select(
            "
            SELECT 	SUM(total_proker.total_keseluruhan) as grand_total_keseluruhan,
                    SUM(total_proker.total_bulan_ini) as grand_total_bulan_ini,
                    SUM(total_proker.total_bulan_lalu) as grand_total_bulan_lalu
            FROM 		(
                        SELECT 	COUNT(a.id) as total_keseluruhan,
                                0 as total_bulan_ini,
                                0 as total_bulan_lalu,
                                a.created_by
                        FROM 	proker_bulanan a
                        WHERE 	a.pkb_is_active = 't'
                        AND 	EXTRACT(YEAR FROM a.pkb_start_date) = EXTRACT(YEAR FROM CURRENT_DATE)
                        GROUP BY a.created_by

                        UNION

                        SELECT 	0 as total_keseluruhan,
                                count(a.id) as total_bulan_ini,
                                0 as total_bulan_lalu,
                                a.created_by
                        FROM 	proker_bulanan a
                        WHERE 	a.pkb_is_active = 't'
                        AND 	EXTRACT(YEAR FROM a.pkb_start_date) = EXTRACT(YEAR FROM CURRENT_DATE)
                        AND 	EXTRACT(MONTH FROM a.pkb_start_date) = EXTRACT(MONTH FROM CURRENT_DATE)
                        GROUP BY a.created_by

                        UNION

                        SELECT 	0 as total_keseluruhan,
                                0 as total_bulan_ini,
                                count(a.id) as total_bulan_lalu,
                                a.created_by
                        FROM 	proker_bulanan a
                        WHERE 	a.pkb_is_active = 't'
                        AND 	EXTRACT(YEAR FROM a.pkb_start_date) = EXTRACT(YEAR FROM DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
                        AND 	EXTRACT(MONTH FROM a.pkb_start_date) = EXTRACT(MONTH FROM DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
                        GROUP BY a.created_by
            ) AS total_proker
            WHERE 	total_proker.created_by = '$user_id'
            "
        );

        $output     = [
            "user_data"         => !empty($query_user_profiles[0]) ? $query_user_profiles[0] : '',
            "user_act_total"    => !empty($query_total_act[0]) ? $query_total_act[0] : '',
        ];

        return $output;
    }

    public static function doUploadProfilePicture($data)
    {
        DB::beginTransaction();

        $data_where     = array(
            "user_id"   => $data['user_id'],
        );

        $data_update    = array(
            "pict_dir"  => $data['storage_path']."/".$data['custom_name'],
            // "pict_dir"  => 'test',
            "updated_by"    => $data['user_id'],
            "updated_at"    => date('Y-m-d H:i:s')
        );
    
        DB::table('employees')->where($data_where)->update($data_update);

        try {
            DB::commit();
            LogHelper::create('edit', 'Berhasil Merubah Profile Picture User Id : '.$data['user_id'], $data['ip']);
            $output     = array(
                "status"    => "berhasil",
                "errMsg"    => ""
            );
        } catch(\Exception $e) {
            DB::rollBack();

            LogHelper::create('error_system', 'Gagal Merubah Profile Picture', $data['ip']);
            
            $output     = array(
                'status'    => 'gagal',
                'errMsg'    => $e->getMessage(),
            );
        }

        return $output;
    }

    public static function doGetDataUser($id_user)
    {
        $query  = DB::select(
            "
            SELECT  *
            FROM    employees
            WHERE   user_id = '$id_user'
            "
        );

        return $query;
    }
}