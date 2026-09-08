<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Beacon;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;


use App\Models\Siren; 

class LocationController extends Controller
{
public function index(): View
{
    $locations = Location::orderBy('location_name')->get();
    $beacons = Beacon::with('location')->orderBy('name')->get();
    $sirens = Siren::all(); 

    return view('settings.locations', compact('locations', 'beacons', 'sirens'));
}

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'location_name' => 'required|string|max:120|unique:locations,location_name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $location = Location::create([
            'location_name' => trim($request->location_name),
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Location created successfully.',
            'location' => [
                'id'            => $location->id,
                'location_name' => $location->location_name,
            ],
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $location = Location::findOrFail($id);

        Log::info('Location update – debug payload', [
            'id'                 => $id,
            'all_input'          => $request->all(),
            'location_name'      => $request->input('location_name'),
            'edit_location_name' => $request->input('edit_location_name'),
            '_method'            => $request->input('_method'),
            'csrf_present'       => $request->has('_token') ? 'yes' : 'no',
        ]);

        $validator = Validator::make($request->all(), [
            'location_name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('locations', 'location_name')->ignore($location),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $location->update([
            'location_name' => trim($request->input('location_name')),
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Location updated successfully.',
            'location' => [
                'id'            => $location->id,
                'location_name' => $location->location_name,
            ],
        ]);
    }
}