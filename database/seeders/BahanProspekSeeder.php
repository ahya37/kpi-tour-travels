<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AlumniProspekMaterial;
use Str;

class BahanProspekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
        $data = [
            ['id' => Str::random(20).time(), 'job_employee_id' => 'f55a29b2133e11efa095645aede842','members' => 0, 'label' => 'Bahan Prospek Alumni (05-2024)', 'periode' => 1,'is_sinkronisasi' => '0','notes' => '','created_by' => '1','updated_by' => '1','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')],
            ['id' => Str::random(20).time(), 'job_employee_id' => 'f55a2a7a133e11efa095645aede843','members' => 0, 'label' => 'Bahan Prospek Alumni (05-2024)', 'periode' => 1,'is_sinkronisasi' => '0','notes' => '','created_by' => '1','updated_by' => '1','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')],
            ['id' => Str::random(20).time(), 'job_employee_id' => 'f55a2ad4133e11efa095645aede844','members' => 0, 'label' => 'Bahan Prospek Alumni (05-2024)', 'periode' => 1,'is_sinkronisasi' => '0','notes' => '','created_by' => '1','updated_by' => '1','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')],
            ['id' => Str::random(20).time(), 'job_employee_id' => 'f55a2b10133e11efa095645aede845','members' => 0, 'label' => 'Bahan Prospek Alumni (05-2024)', 'periode' => 1,'is_sinkronisasi' => '0','notes' => '','created_by' => '1','updated_by' => '1','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')],
            ['id' => Str::random(20).time(), 'job_employee_id' => 'oX7eQ82LAg28c4IThPmE2okirHsvEg','members' => 0, 'label' => 'Bahan Prospek Alumni (05-2024)', 'periode' => 1,'is_sinkronisasi' => '0','notes' => '','created_by' => '1','updated_by' => '1','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')],
        ];
 
        foreach ($data as $key => $value) {
            AlumniProspekMaterial::create($value);
        }
    }
    

}
