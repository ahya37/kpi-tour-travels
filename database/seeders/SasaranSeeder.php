<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProkerTahunan;
use DB;

class SasaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['pkt_id' => 42,'pktd_seq' => 1, 'pktd_title' => 'Pencapaian', 'pktd_target' => 2500,'created_by' => '1','updated_by' => '1'],
            ['pkt_id' => 42,'pktd_seq' => 2, 'pktd_title' => 'Agen', 'pktd_target' => 50,'created_by' => '1','updated_by' => '1'],
        ];

        foreach($data as $value){
           DB::table('proker_tahunan_detail')->insert($value);
        }
    }
}
