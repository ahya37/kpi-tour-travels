<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class JenisPekerjaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['pkb_id' => 116,'pkbd_type' => 'Canvasing Bandung Raya', 'pkbd_num_target' => 5,'created_by' => '1','updated_by' => '1'],
            ['pkb_id' => 117,'pkbd_type' => 'Posting IG', 'pkbd_num_target' => 3,'created_by' => '1','updated_by' => '1']
        ];

        foreach ($data as $key => $value) {
            DB::table('proker_bulanan_detail')->insert($value);
        }
    }
}
