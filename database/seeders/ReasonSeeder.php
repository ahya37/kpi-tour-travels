<?php

namespace Database\Seeders;

use App\Models\Reason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['id' => Str::random(20).time(),'name' => 'Tidak ada Wa', 'created_by' => 1,'updated_by' => 1,'updated_at' => date('Y-m-d H:i:s')],
            ['id' => Str::random(20).time(),'name' => 'Tidak jawab sama sekali', 'created_by' => 1,'updated_by' => 1,'updated_at' => date('Y-m-d H:i:s')],
            ['id' => Str::random(20).time(),'name' => 'Tidak ada dana', 'created_by' => 1,'updated_by' => 1,'updated_at' => date('Y-m-d H:i:s')],
            ['id' => Str::random(20).time(),'name' => 'Belum ada rencana', 'created_by' => 1,'updated_by' => 1,'updated_at' => date('Y-m-d H:i:s')],
        ];

        foreach ($data as $value) {
            Reason::create($value);
        }
    }
}
