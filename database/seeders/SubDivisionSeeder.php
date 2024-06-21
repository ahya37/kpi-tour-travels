<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubDivision;
use Illuminate\Support\Str;

class SubDivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['id' => Str::random(30),'division_group_id' => 'GJisR86CsXDgOsU8xILF1715064101','created_by' => 0,'updated_by' => 0]
        ];

        foreach ($data as $value) {
            SubDivision::create($value);
        }
    }
}
