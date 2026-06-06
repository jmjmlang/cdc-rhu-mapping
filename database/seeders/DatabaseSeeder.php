<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MunicipalitySeeder::class,    // 1: no dependencies
            BarangaySeeder::class,        // 2: needs municipalities
            HealthCategorySeeder::class,  // 3: no dependencies
            AdminUserSeeder::class,       // 4: no dependencies
            CaseReportSeeder::class,      // 5: needs barangays, categories, users
        ]);
    }
}
