<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;
use Illuminate\Support\Str;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['id' => Str::random(20).time(),'name' => 'Marwah','product_id' => 'CJ4RBWESy3VdDpOzUhTv1715066992', 'created_by' => 1,'updated_by' => 1,'updated_at' => date('Y-m-d H:i:s')],
            ['id' => Str::random(20).time(),'name' => 'Multazam','product_id' => 'CJ4RBWESy3VdDpOzUhTv1715066992', 'created_by' => 1,'updated_by' => 1,'updated_at' => date('Y-m-d H:i:s')],
            ['id' => Str::random(20).time(),'name' => 'Ramadhan','product_id' => 'CJ4RBWESy3VdDpOzUhTv1715066992', 'created_by' => 1,'updated_by' => 1,'updated_at' => date('Y-m-d H:i:s')],
            ['id' => Str::random(20).time(),'name' => 'Reguler','product_id' => 'CJ4RBWESy3VdDpOzUhTv1715066992', 'created_by' => 1,'updated_by' => 1,'updated_at' => date('Y-m-d H:i:s')],
            ['id' => Str::random(20).time(),'name' => 'Safa','product_id' => 'CJ4RBWESy3VdDpOzUhTv1715066992', 'created_by' => 1,'updated_by' => 1,'updated_at' => date('Y-m-d H:i:s')],
            ['id' => Str::random(20).time(),'name' => 'Zamzam','product_id' => 'CJ4RBWESy3VdDpOzUhTv1715066992', 'created_by' => 1,'updated_by' => 1,'updated_at' => date('Y-m-d H:i:s')],
            ['id' => Str::random(20).time(),'name' => 'Raudhah','product_id' => 'CJ4RBWESy3VdDpOzUhTv1715066992', 'created_by' => 1,'updated_by' => 1,'updated_at' => date('Y-m-d H:i:s')],
        ];

        foreach ($data as $value) {
            Program::create($value);
        }
    }
}
