<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(RolesTableSeeder::class);
        // $this->call(PermissionsTableSeeder::class);
        // $this->call(UserTableSeeder::class);
        // $this->call(GroupDivisionSeeder::class);
        // $this->call(ProductSeeder::class);
        // $this->call(ProgramSeeder::class);
        // $this->call(SubDivisionSeeder::class);
        // $this->call(EmployeeSeeder::class);
        $this->call(JobEmployeeSeeder::class);
    }
}
