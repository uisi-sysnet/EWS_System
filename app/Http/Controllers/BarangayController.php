<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangayController extends Controller
{

    public function getGeoJson()
    {
        $barangays = Barangay::select(
            'id',
            'name',
            DB::raw('ST_AsGeoJSON(boundary) AS geojson')
        )->get();

        $features = [];

        foreach ($barangays as $barangay) {
            $geometry = json_decode($barangay->geojson);

            $features[] = [
                'type' => 'Feature',
                'geometry' => $geometry,
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