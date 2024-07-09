<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProkerBulanan as Program;

class ProkerProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['uuid' => '6Urrzb3p3NmXZTaAqDjg6XFzWwOPN1', 'pkb_title' => 'Canvasing Program Bulananan','pkb_pkt_id' => '39d3ec3f-7073-4c5a-9ab6-92d4db1e001d | ','master_program_id' => 1,'pkb_employee_id' => 'DoLQWf9qzutJi21kviTqk3ZBybyLbb','created_by' => '1','updated_by' => '1'],
            ['uuid' => '6Urrzb3p3NmXZTaAqDjg6XFzWwOPNo', 'pkb_title' => 'Sosial Media Program Bulanan','pkb_pkt_id' => '39d3ec3f-7073-4c5a-9ab6-92d4db1e001d | ','master_program_id' => 2,'pkb_employee_id' => 'DoLQWf9qzutJi21kviTqk3ZBybyLbb','created_by' => '1','updated_by' => '1'],
        ];

        foreach($data as $value){
            Program::create($value);
        }
    }
    
}
