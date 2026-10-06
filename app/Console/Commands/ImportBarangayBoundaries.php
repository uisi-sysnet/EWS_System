<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barangay;

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
            $geometry = $feature['geometry'] ?? null;
            $properties = $feature['properties'] ?? [];
            if (! is_array($geometry) || ! isset($geometry['type'], $geometry['coordinates'])) {
                $this->error('Skipped a feature with invalid geometry.');
                $failedCount++;
                continue;
            }
            $name = $properties['Barangay'] ?? $properties['name'] ?? 'Unknown';

            try {
                Barangay::create([
                    'name' => $name,
                    'properties' => $properties,
                    'boundary' => $geometry,
                ]);
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
