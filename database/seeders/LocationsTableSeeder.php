<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationsTableSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            'Alabang',
            'Ayala Alabang',
            'Bayanan',
            'Buli',
            'Cupang',
            'Poblacion',
            'Putatan',
            'Sucat',
            'Tunasan',
        ];

        foreach ($locations as $location) {
            DB::table('locations')->insert([
                'location_name' => $location,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}