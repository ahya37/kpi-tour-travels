<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TarikDataService;
use Illuminate\Support\Facades\Http;

class TarikDataUmrah extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tarikdata:tourcode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $current_year   = date('Y', strtotime(now()));
        
        $api_url        = env('API_PERCIK_V2');
        $get_data_api   = Http::get($api_url . "/api/umhaj/master/jadwal_umrah?tahun=" . $current_year);

        if($get_data_api->status() >= 200 && $get_data_api->status() < 300) {
            $data_api   = $get_data_api->json('data');

            $do_simpan  = TarikDataService::doSimpanUmrahNoLogin($data_api);

            if($do_simpan['status'] == 'berhasil') {
                info($do_simpan['message'] . " " . now());
            } else {
                info($do_simpan['message'] . " " . now());
            }
            
        } else {
            info('Tidak Ada Data Umrah Pada Tahun ' . $current_year);
        }
    }
}
