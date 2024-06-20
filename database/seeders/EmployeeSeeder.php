<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use Illuminate\Support\Str;
class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['id' => Str::random(30), 'user_id' => 15,'name' => 'Hinata','created_by' => '', 'updated_by' => '']
        ];

        foreach ($data as  $value) {
            Employee::create($value);
        }
    }
}
