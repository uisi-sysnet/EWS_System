<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beacon;
use App\Models\BeaconStatusLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BeaconController extends Controller
{
    /**
     * Get all beacons with their basic data
     * GET /api/Data/Beacons
     */
    public function getBeacons(): JsonResponse
    {
        $beacons = Beacon::with('location')->get();
        
        $formattedBeacons = $beacons->map(fn($beacon) => [
            'beacon_id' => $beacon->beacon_id,
            'oid' => $beacon->oid,
            'name' => $beacon->name,
            'group' => $beacon->group,
            'coordinates' => [
                'latitude' => $beacon->latitude,
                'longitude' => $beacon->longitude
            ]
        ]);

        return response()->json([
            'success' => true,
            'data' => $formattedBeacons,
            'meta' => $this->getBeaconMeta($beacons)
        ]);
    }

    /**
     * Get beacon status with history
     * GET /api/Data/BeaconStatus
     */
    public function getBeaconStatus(Request $request): JsonResponse
    {
        $beaconId = $request->input('beacon_id');
        $limit = $request->input('limit', 10);
        $days = $request->input('days', 7);

        if ($beaconId) {
            return $this->getSingleBeaconStatus($beaconId, $limit, $days);
        }

        return $this->getAllBeaconsStatus();
    }

    /**
     * Get beacon status summary
     * GET /api/Data/BeaconStatus/summary
     */
    public function getStatusSummary(): JsonResponse
    {
        $total = Beacon::count();
        $online = Beacon::where('status', true)->count();
        
        return response()->json([
            'success' => true,
            'data' => [
                'beacons' => [
                    'total' => $total,
                    'online' => $online,
                    'offline' => $total - $online,
                    'online_percentage' => $total > 0 ? round(($online / $total) * 100, 2) : 0
                ],
                'doors' => [
                    'open' => Beacon::where('is_door_open', true)->count(),
                    'closed' => Beacon::where('is_door_open', false)->count()
                ],
                'activity' => [
                    'status_changes_last_24h' => BeaconStatusLog::where('event_time', '>=', now()->subDay())->count(),
                    'last_update' => now()->toIso8601String()
                ]
            ]
        ]);
    }

    /**
     * Get single beacon status with history
     */
    private function getSingleBeaconStatus(string $beaconId, int $limit, int $days): JsonResponse
    {
        $beacon = Beacon::where('beacon_id', $beaconId)->first();
        
        if (!$beacon) {
            return response()->json([
                'success' => false,
                'message' => 'Beacon not found'
            ], 404);
        }

        $history = $this->getBeaconHistory($beaconId, $limit, $days);

        return response()->json([
            'success' => true,
            'data' => [
                'beacon' => [
                    'beacon_id' => $beacon->beacon_id,
                    'oid' => $beacon->oid,
                    'name' => $beacon->name,
                    'group' => $beacon->group,
                    'location' => $beacon->location?->location_name ?? '—',
                    'status' => $beacon->status ? 'online' : 'offline',
                    'last_seen_at' => $beacon->last_seen_at?->toIso8601String(),
                    'is_door_open' => $beacon->is_door_open,
                    'last_opened_at' => $beacon->last_opened_at?->toIso8601String()
                ],
                'history' => $history,
                'summary' => [
                    'total_events' => $history->count(),
                    'uptime_percentage' => $this->calculateUptime($history)
                ]
            ]
        ]);
    }

    /**
     * Get all beacons status
     */
    private function getAllBeaconsStatus(): JsonResponse
    {
        $beacons = Beacon::with('location')->get();
        
        $formattedBeacons = $beacons->map(fn($beacon) => [
            'beacon_id' => $beacon->beacon_id,
            'oid' => $beacon->oid,
            'name' => $beacon->name,
            'status' => $beacon->status ? 'online' : 'offline',
            'last_seen_at' => $beacon->last_seen_at?->toIso8601String(),
            'is_door_open' => $beacon->is_door_open,
            'last_opened_at' => $beacon->last_opened_at?->toIso8601String(),
            'status_age_seconds' => $beacon->last_seen_at 
                ? now()->diffInSeconds($beacon->last_seen_at)
                : null
        ]);

        return response()->json([
            'success' => true,
            'data' => $formattedBeacons,
            'meta' => $this->getBeaconMeta($beacons)
        ]);
    }

    /**
     * Get beacon history logs  
     */
    private function getBeaconHistory(string $beaconId, int $limit, int $days)
    {
        return BeaconStatusLog::where('beacon_identifier', $beaconId)
            ->where('event_time', '>=', now()->subDays($days))
            ->orderBy('event_time', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($log) => [
                'status' => $log->status,
                'event_time' => $log->event_time->toIso8601String(),
                'duration_seconds' => $log->duration_seconds,
                'duration_formatted' => $log->duration_seconds 
                    ? $this->formatDuration($log->duration_seconds)
                    : null
            ]);
    }

    /**
     * Get beacon meta statistics
     */
    private function getBeaconMeta($beacons): array
    {
        return [
            'total' => $beacons->count(),
            'online' => $beacons->where('status', true)->count(),
            'offline' => $beacons->where('status', false)->count(),
            'door_open' => $beacons->where('is_door_open', true)->count(),
            'door_closed' => $beacons->where('is_door_open', false)->count()
        ];
    }

    /**
     * Format duration in human-readable format
     */
    private function formatDuration(int $seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($days > 0) {
            return sprintf('%dd %02dh %02dm %02ds', $days, $hours, $minutes, $secs);
        } elseif ($hours > 0) {
            return sprintf('%dh %02dm %02ds', $hours, $minutes, $secs);
        } elseif ($minutes > 0) {
            return sprintf('%dm %02ds', $minutes, $secs);
        }
        
        return sprintf('%ds', $secs);
    }

    /**
     * Calculate uptime percentage from history
     */
    private function calculateUptime($history): float
    {
        if ($history->isEmpty()) {
            return 0;
        }

        $totalOnlineTime = $history->reduce(function ($carry, $log) {
            if ($log->status === 'online' && $log->duration_seconds) {
                return $carry + $log->duration_seconds;
            }
            return $carry;
        }, 0);

        $totalTime = $history->first()->event_time->diffInSeconds($history->last()->event_time);

        return $totalTime > 0 ? round(($totalOnlineTime / $totalTime) * 100, 2) : 0;
    }
}
