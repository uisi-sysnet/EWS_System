<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Siren;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class SirenController extends Controller
{

    public function update(Request $request, string $oid): RedirectResponse|JsonResponse
    {
        try {
            $validated = $request->validate([
                'name'        => 'nullable|string|max:255',
                'location_id' => 'nullable|exists:locations,id',
                'siren_id'    => 'nullable|string|max:255',
                'group'       => 'nullable|string|max:255',
                'latitude'    => 'nullable|numeric|between:-90,90',
                'longitude'   => 'nullable|numeric|between:-180,180',
                'enabled'     => 'sometimes|boolean',
            ]);
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                ], 422);
            }
            throw $e;
        }

        $siren = Siren::firstOrCreate(
            ['oid' => $oid],
            [
                'name'      => $request->input('name'),
                'siren_id'  => $request->input('siren_id'),
                'group'     => $request->input('group'),
                'latitude'  => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'enabled'   => $request->input('enabled', true),
            ]
        );

        $siren->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Siren updated successfully.',
                'siren'   => $siren->load('location')
            ]);
        }

        return redirect()->back()->with('success', 'Siren updated.');
    }
}