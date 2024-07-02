<?php 
namespace App\Helpers;
use Illuminate\Support\Facades\Log;

class LogFile {

    public static function error($message)
    {
        if (env('APP_DEBUG')) {
            Log::channel('stderr')->error($message);
        }else{
            Log::channel('daily')->error($message);
        }
    }
}