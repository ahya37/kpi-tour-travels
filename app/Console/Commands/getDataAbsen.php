<?php

namespace App\Console\Commands;

use App\Helpers\LogHelper;
use App\Services\TarikDataService;
use Illuminate\Console\Command;

date_default_timezone_set('Asia/Jakarta');

class getDataAbsen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-data-absen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CronJob untuk mengambil Data Absen';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today  = date('Y-m-d');
        $get_data   = TarikDataService::get_tarik_data_presensi($today);
        LogHelper::cronjob('add', $get_data['message'], "Tarik Data Presensi");
        
        $output     = "[".date('Y-m-d H:i:s')."] ".$get_data['message']."";

        var_dump($output);
    }
}
