<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class MasterProgram extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Canvasing','division_group_id' => 'GJisR86CsXDgOsU8xILF1715064101','created_by' => '1','updated_by' => '1'],
            ['name' => 'Sosial Media','division_group_id' => 'GJisR86CsXDgOsU8xILF1715064101','created_by' => '1','updated_by' => '1'],
        ];

        foreach($data as $value){
            DB::table('master_program')->insert($value);
        }
    }
}
