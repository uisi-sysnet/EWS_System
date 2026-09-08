<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Signal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SignalController extends Controller
{
    public function index()
    {
        $signals = Signal::orderBy('name')->get();
        return view('settings.signals', compact('signals'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'oid'             => ['required', 'string', 'max:100'],
            'signal_id'       => ['required', 'integer', 'min:1'],
            'duration'        => ['nullable', 'integer', 'min:0'],
            'beacon_color_1'  => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/i'],
            'delay_1'         => ['nullable', 'integer', 'min:0'],
            'beacon_color_2'  => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/i'],
            'delay_2'         => ['nullable', 'integer', 'min:0'],
            'button_color'    => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/i'],
        ]);

        try {
            $signal = Signal::updateOrCreate(
                ['oid' => $data['oid']],
                [
                    'name'            => $data['name'],
                    'signal_id'       => $data['signal_id'],
                    'duration'        => $data['duration'] ?? 0,
                    'beacon_color_1'  => $data['beacon_color_1'] ?? null,
                    'delay_1'         => $data['delay_1'] ?? 0,
                    'beacon_color_2'  => $data['beacon_color_2'] ?? null,
                    'delay_2'         => $data['delay_2'] ?? 0,
                    'button_color'    => $data['button_color'] ?? null,
                ]
            );

            $action = $signal->wasRecentlyCreated ? 'created' : 'updated';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Signal {$action} successfully.",
                    'signal'  => $signal
                ]);
            }

            return redirect()
                ->route('settings.signals')
                ->with('success', "Signal {$action} successfully.");
                
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save signal: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->route('settings.signals')
                ->with('error', 'Failed to save signal: ' . $e->getMessage())
                ->withInput();
        }
    }
}