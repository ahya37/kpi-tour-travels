<?php 

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use DB;

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
}