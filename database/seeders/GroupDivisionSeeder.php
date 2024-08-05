<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GroupDivision;
use Illuminate\Support\Str;

class GroupDivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['id' => Str::random(20).time(),'name' => 'Marketing', 'created_by' => 1,'updated_by' => 1,'updated_at' => date('Y-m-d H:i:s')],
            ['id' => Str::random(20).time(),'name' => 'Operasional', 'created_by' => 1,'updated_by' => 1,'updated_at' => date('Y-m-d H:i:s')],
        ];

        foreach ($data as $value) {
            GroupDivision::create($value);
        }
    }
}
