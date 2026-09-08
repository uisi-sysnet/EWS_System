<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beacon;
use Illuminate\Http\Request;

class BeaconStatusController extends Controller
{
    public function update(Request $request, $beaconId)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $beacon = Beacon::where('beacon_id', $beaconId)->firstOrFail();
        $beacon->status = $request->status;
        $beacon->last_seen_at = now();
        $beacon->save();

        return response()->json(['message' => 'Beacon status updated']);
    }
}