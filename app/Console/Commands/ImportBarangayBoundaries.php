<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barangay;
use Illuminate\Support\Facades\DB;

class ImportBarangayBoundaries extends Command
{
    protected $signature = 'import:barangays {path}';
    protected $description = 'Import barangay boundaries from a GeoJSON file';

    public function handle() {
        $path = $this->argument('path') ?? base_path('resources/geodata/Barangay_Boundary.json');
        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return 1;
        }

        $geojson = json_decode(file_get_contents($path), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Invalid GeoJSON file.");
            return 1;
        }

        if (!isset($geojson['features']) || !is_array($geojson['features'])) {
            $this->error("Invalid GeoJSON structure: 'features' array not found.");
            return 1;
        }

        $totalFeatures = count($geojson['features']);
        $importedCount = 0;
        $failedCount = 0;

        foreach ($geojson['features'] as $feature) {
            $geometry = $feature['geometry'];
            $properties = $feature['properties'];
            $name = $properties['Barangay'] ?? $properties['name'] ?? 'Unknown';

            try {
                DB::insert(
                    'INSERT INTO barangays (name, properties, boundary, created_at, updated_at) 
                    VALUES (?, ?, ST_SetSRID(ST_GeomFromGeoJSON(?), 4326), NOW(), NOW())',
                    [
                        $name,
                        json_encode($properties),
                        json_encode($geometry)
                    ]
                );
                $importedCount++;
            } catch (\Exception $e) {
                $this->error("Failed to import feature: {$name}. Error: " . $e->getMessage());
                $failedCount++;
            }
        }

        $this->info("Import completed. Imported: {$importedCount}, Failed: {$failedCount}, Total: {$totalFeatures}");
        return 0;
    }
}