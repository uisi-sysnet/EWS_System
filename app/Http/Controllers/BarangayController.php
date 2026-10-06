<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use Illuminate\Http\Request;

class BarangayController extends Controller
{

    public function getGeoJson()
    {
        $barangays = Barangay::query()->get(['id', 'name', 'boundary']);

        $features = [];

        foreach ($barangays as $barangay) {
            $features[] = [
                'type' => 'Feature',
                'geometry' => $barangay->boundary,
                'properties' => [
                    'id' => $barangay->id,
                    'name' => $barangay->name,
                ],
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
