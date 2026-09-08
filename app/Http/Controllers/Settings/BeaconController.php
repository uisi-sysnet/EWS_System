<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Beacon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use App\Events\BeaconStatusUpdated;
use App\Events\BeaconDoorStatusUpdated;


use Illuminate\Support\Facades\Log;

class BeaconController extends Controller
{
    public function index(): View
    {
        $beacons = Beacon::with('location')->orderBy('name')->get();

        return view('settings.beacons', compact('beacons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'beacon_name' => ['required', 'string', 'max:120'],
            'location_id' => ['required', 'exists:locations,id'],
            'oid'         => ['nullable', 'string', 'max:100', 'unique:beacons,oid'],
            'beacon_id'   => ['nullable', 'string', 'max:60',  'unique:beacons,beacon_id'],
            'group'       => ['nullable', 'string', 'max:120'],
            'latitude'    => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'longitude'   => ['nullable', 'numeric', 'min:-180', 'max:180'],
        ]);

        $beacon = Beacon::create([
            'name'        => $data['beacon_name'],
            'location_id' => $data['location_id'],
            'oid'         => $data['oid'],
            'beacon_id'   => $data['beacon_id'],
            'group'       => $data['group'],
            'latitude'    => $data['latitude'],
            'longitude'   => $data['longitude'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Beacon created successfully.',
                'beacon'  => $beacon->load('location')
            ]);
        }

        return redirect()
            ->route('settings.locations')
            ->with('success', 'Beacon created successfully.');
    }

    public function update(Request $request, $id)
    {
        $beacon = Beacon::findOrFail($id);

        $data = $request->validate([
            'beacon_name' => ['required', 'string', 'max:120'],
            'location_id' => ['required', 'exists:locations,id'],
            'oid'         => ['nullable', 'string', 'max:100', Rule::unique('beacons')->ignore($beacon->id)],
            'beacon_id'   => ['nullable', 'string', 'max:60',  Rule::unique('beacons')->ignore($beacon->id)],
            'group'       => ['nullable', 'string', 'max:120'],
            'latitude'    => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'longitude'   => ['nullable', 'numeric', 'min:-180', 'max:180'],
        ]);

        $beacon->update([
            'name'        => $data['beacon_name'],
            'location_id' => $data['location_id'],
            'oid'         => $data['oid'],
            'beacon_id'   => $data['beacon_id'],
            'group'       => $data['group'],
            'latitude'    => $data['latitude'],
            'longitude'   => $data['longitude'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Beacon updated successfully.',
                'beacon'  => $beacon->load('location')
            ]);
        }

        return redirect()->route('settings.locations')->with('success', 'Beacon updated successfully.');
    }



    public function list()
    {
        $beacons = Beacon::with('location')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('name')
            ->get()
            ->map(function ($beacon) {
                return [
                    'id'          => $beacon->id,
                    'name'        => $beacon->name,
                    'location_name' => $beacon->location->location_name ?? '',
                    'location_id' => $beacon->location_id,
                    'oid'         => $beacon->oid,
                    'beacon_id'   => $beacon->beacon_id,
                    'group'       => $beacon->group,
                    'latitude'    => $beacon->latitude,
                    'longitude'   => $beacon->longitude,
                ];
            });

        return response()->json($beacons);
    }


    public function updateStatus(Request $request)
    {
        try {
            $beaconId = trim($request->beacon_id);

            $beacon = Beacon::where('beacon_id', $beaconId)->first();

            if (!$beacon) {
                Log::warning('Beacon not found', ['beacon_id' => $beaconId]);

                $allBeacons = Beacon::select('id', 'beacon_id', 'name')->get();
                Log::info('Available beacons:', $allBeacons->toArray());

                return response()->json([
                    'error' => 'Beacon not found',
                    'available_beacons' => $allBeacons->pluck('beacon_id')
                ], 404);
            }

            $request->validate([
                'status' => 'required|boolean',
            ]);

            $oldStatus = $beacon->status;
            $beacon->status = $request->status;
            $beacon->last_seen_at = $request->status ? now() : $beacon->last_seen_at;
            $beacon->save();

            if ($oldStatus != $beacon->status) {
                broadcast(new BeaconStatusUpdated($beacon));

                Log::info('Beacon status updated and broadcasted via Pusher', [
                    'beacon_id' => $beacon->beacon_id,
                    'old_status' => $oldStatus,
                    'new_status' => $beacon->status
                ]);
            }

            return response()->json([
                'success' => true,
                'beacon_id' => $beacon->beacon_id,
                'status' => $beacon->status
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating beacon status:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateDoorStatus(Request $request)
    {
        try {
            $beaconId = trim($request->beacon_id);

            $beacon = Beacon::where('beacon_id', $beaconId)->first();

            if (!$beacon) {
                Log::warning('Beacon not found for door status', ['beacon_id' => $beaconId]);
                return response()->json(['error' => 'Beacon not found'], 404);
            }

            $request->validate([
                'is_door_open' => 'required|boolean',
            ]);

            $oldDoorStatus = $beacon->is_door_open;
            $beacon->is_door_open = $request->is_door_open;

            if ($request->is_door_open) {
                $beacon->last_opened_at = now();
            }

            $beacon->save();

            Log::info('Beacon door status updated', [
                'beacon_id' => $beacon->beacon_id,
                'is_door_open' => $beacon->is_door_open,
                'last_opened_at' => $beacon->last_opened_at
            ]);

            broadcast(new BeaconDoorStatusUpdated($beacon));

            return response()->json([
                'success' => true,
                'beacon_id' => $beacon->beacon_id,
                'is_door_open' => $beacon->is_door_open,
                'last_opened_at' => $beacon->last_opened_at
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating beacon door status:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
