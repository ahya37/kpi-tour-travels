<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Hash;

date_default_timezone_set('Asia/Jakarta');

class ProgramKerjaService
{
    public function doSimpanProkerTahunan($data)
    {
        var_dump($data);die();
    }
}