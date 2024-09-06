<?php

namespace App\Http\Controllers;

date_default_timezone_set('Asia/Jakarta');

use Illuminate\Http\Request;
use App\Services\BaseService;
use App\Services\TarikDataService;

class TarikDataController extends Controller
{
    public function tarik_data_absensi()
    {
        date_default_timezone_set('Asia/Jakarta');
        $today  = date('Y-m-d');
        // $today  = "2024-09-05";

        // GET DATA
        $getData    = TarikDataService::get_tarik_data_presensi($today);
        var_dump($getData);die();

        $output     = [
            "success"   => true,
            "status"    => 200,
            "message"   => $getData['message'],
        ];
        
        echo $getData['message']."<br/>";
        echo "<a href='/dashboard'>Kembali Ke Halamam Utama</a>";
    }
}
