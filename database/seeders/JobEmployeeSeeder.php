<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JobEmployee;
use Illuminate\Support\Str;

class JobEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data  = [
            ['id' => Str::random(30), 'employee_id' => 'sAyRiyfMzWjfqoYNXaRIWjWQdRmYAc', 'sub_division_id' => 'BsvCGT8lxRiS6cnYyWYEDLtb2N2Vak','group_division_id' => 'GJisR86CsXDgOsU8xILF1715064101','created_by' => 0,'updated_by' => 0]
        ];

        foreach ($data as  $value) {
            JobEmployee::create($value);
        }
    }
}
