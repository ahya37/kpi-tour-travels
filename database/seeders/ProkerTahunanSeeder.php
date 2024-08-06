<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProkerTahunan;

class ProkerTahunanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'pkt_title' => 'Program Kerja Marketing',
                'pkt_description' => 'Program Kerja Marketing',
                'pkt_year' => date('Y-m-d'),
                'pkt_pic_job_employee_id' => 'f55a29b2133e11efa095645aede842',
                'group_division_id' => 'GJisR86CsXDgOsU8xILF1715064101',
                'parent_id'  => 0,
                'created_by' => 1, 
                'updated_by' => 1,
            ],
            [
                'pkt_title' => 'Membuat Iklan Digital Marketing',
                'pkt_description' => 'Membuat Iklan Digital Marketing',
                'pkt_year' => date('Y-m-d'),
                'pkt_pic_job_employee_id' => 'f55a29b2133e11efa095645aede842',
                'group_division_id' => 'GJisR86CsXDgOsU8xILF1715064101',
                'parent_id'  => 0,
                'created_by' => 0, 
                'updated_by' => 1,
            ]
        ];

        foreach ($data as $key => $value) {
            ProkerTahunan::create($value);
        }
    }
}
