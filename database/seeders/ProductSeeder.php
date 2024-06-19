<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['id' => Str::random(20).time(),'name' => 'Haji', 'created_by' => 1,'updated_by' => 1,'updated_at' => date('Y-m-d H:i:s')],
            ['id' => Str::random(20).time(),'name' => 'Umrah', 'created_by' => 1,'updated_by' => 1,'updated_at' => date('Y-m-d H:i:s')],
            ['id' => Str::random(20).time(),'name' => 'Tour Muslim', 'created_by' => 1,'updated_by' => 1,'updated_at' => date('Y-m-d H:i:s')],
        ];

        foreach ($data as $value) {
            Product::create($value);
        }
    }
}
