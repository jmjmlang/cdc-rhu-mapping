<?php

namespace Database\Seeders;

use App\Models\Municipality;
use Illuminate\Database\Seeder;

class MunicipalitySeeder extends Seeder
{
    public function run(): void
    {
        Municipality::updateOrCreate(
            ['name' => 'Luna'],
            ['name' => 'Luna', 'province' => 'Apayao', 'region' => 'CAR']
        );
    }
}
