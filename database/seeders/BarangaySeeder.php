<?php

namespace Database\Seeders;

use App\Models\Barangay;
use App\Models\Municipality;
use Illuminate\Database\Seeder;

class BarangaySeeder extends Seeder
{
    public function run(): void
    {
        $municipality = Municipality::where('name', 'Luna')->firstOrFail();

        $barangays = [
            // Exact centroid coordinates confirmed April 2026.
            // household_count column was dropped in migration 2026_04_19_000002.
            ['name' => 'Luna',    'latitude' => 18.333209, 'longitude' => 121.354445],
            ['name' => 'Bacsay',  'latitude' => 18.290748, 'longitude' => 121.359264],
            ['name' => 'Turod',   'latitude' => 18.328254, 'longitude' => 121.352291],
            ['name' => 'Zumigui', 'latitude' => 18.338661, 'longitude' => 121.351669],
        ];

        foreach ($barangays as $data) {
            Barangay::updateOrCreate(
                ['name' => $data['name'], 'municipality_id' => $municipality->id],
                array_merge($data, ['municipality_id' => $municipality->id])
            );
        }
    }
}
