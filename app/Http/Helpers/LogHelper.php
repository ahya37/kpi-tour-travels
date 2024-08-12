<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
date_default_timezone_set('Asia/Jakarta');


class LogHelper {
    public static function create($type, $desc, $ip, $log_user_id = null)
    {
        if($type == 'add') {
            $type   = "1";
        } else if($type == 'edit') {
            $type   = "2";
        } else if($type == 'delete')  {
            $type   = "3";
        } else if($type == 'error_system') {
            $type   = "4";
        } else {
            $type   = "0";
        }

        $dataSimpan     = array(
            "log_desc"          => $desc,
            "log_date_time"     => date('Y-m-d H:i:s'),
            "log_user_id"       => Auth::user()->id ?? $log_user_id,
            "log_type"          => $type,
            "log_ip_address"    => $ip,
        );

        DB::table('log_activity')->insert($dataSimpan);
    }
}
?>