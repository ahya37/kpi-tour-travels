<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProkerBulanan as Program;
use Str;

class ProkerProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $data = [
            ['uuid' => Str::random(20).time(), 'pkb_title' => 'CANVASING','pkb_pkt_id' => 'xHbIdsjiNe0uyHvpJ7fkHbMIx6OaJH | 1','pkb_start_date' => '2024-01-01','master_program_id' => 54,'pkb_employee_id' => 'DoLQWf9qzutJi21kviTqk3ZBybyLbb','created_by' => '1','updated_by' => '1'],
            ['uuid' => Str::random(20).time(), 'pkb_title' => 'SOSIALISASI','pkb_pkt_id' => 'xHbIdsjiNe0uyHvpJ7fkHbMIx6OaJH | 1','pkb_start_date' => '2024-01-01','master_program_id' => 50,'pkb_employee_id' => 'DoLQWf9qzutJi21kviTqk3ZBybyLbb','created_by' => '1','updated_by' => '1'],
            ['uuid' => Str::random(20).time(), 'pkb_title' => 'MAINTENANCE AGEN','pkb_pkt_id' => 'xHbIdsjiNe0uyHvpJ7fkHbMIx6OaJH | 1','pkb_start_date' => '2024-01-01','master_program_id' => 15,'pkb_employee_id' => 'DoLQWf9qzutJi21kviTqk3ZBybyLbb','created_by' => '1','updated_by' => '1'],
            ['uuid' => Str::random(20).time(), 'pkb_title' => 'VISIT','pkb_pkt_id' => 'xHbIdsjiNe0uyHvpJ7fkHbMIx6OaJH | 1','pkb_start_date' => '2024-01-01','master_program_id' => 52,'pkb_employee_id' => 'DoLQWf9qzutJi21kviTqk3ZBybyLbb','created_by' => '1','updated_by' => '1'],
        ];

        foreach($data as $value){
            Program::create($value);
        }
    }
    
}
