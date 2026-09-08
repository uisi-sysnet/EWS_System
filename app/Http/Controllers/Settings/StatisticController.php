<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\BeaconStatusLog;
use App\Models\Beacon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticController extends Controller
{
    public function index(Request $request): View
    {
        $logs = BeaconStatusLog::with('beacon')
            ->orderBy('event_time', 'desc')
            ->paginate(50);

        $chartData = $this->getChartData($request->get('range', 'last_3_hours'));
        
        // Get offline beacons with their offline duration
        $offlineBeacons = $this->getOfflineBeaconsWithDuration();

        return view('settings.statistics', compact('logs', 'chartData', 'offlineBeacons'));
    }

    public function chartData(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->getChartData($request->get('range', 'last_3_hours')));
    }

    /**
     * Get all beacons that are currently offline with their offline duration
     */
    private function getOfflineBeaconsWithDuration(): array
    {
        $now = Carbon::now();
        
        // Get all beacons that have an offline status as their latest log
        // Using a subquery to get the latest status for each beacon
        $latestStatuses = BeaconStatusLog::select('beacon_id', 'beacon_name', 'beacon_identifier', 'status', 'event_time')
            ->whereIn('id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('beacon_status_logs')
                    ->groupBy('beacon_id');
            })
            ->get();
        
        $offlineBeacons = [];
        
        foreach ($latestStatuses as $latestLog) {
            // ONLY include if status is 'offline'
            if ($latestLog->status !== 'offline') {
                continue;
            }
            
            // Find the last online record before the current offline period
            $lastOnline = BeaconStatusLog::where('beacon_id', $latestLog->beacon_id)
                ->where('status', 'online')
                ->where('event_time', '<', $latestLog->event_time)
                ->orderBy('event_time', 'desc')
                ->first();
            
            $offlineSince = $latestLog->event_time;
            
            // Calculate duration
            $duration = $now->diff($offlineSince);
            
            $offlineBeacons[] = [
                'beacon_id' => $latestLog->beacon_identifier, // Using beacon_identifier as the display ID
                'name' => $latestLog->beacon_name,
                'location' => $this->getBeaconLocation($latestLog->beacon_id), // You'll need this helper method
                'offline_since' => $offlineSince,
                'last_online' => $lastOnline ? $lastOnline->event_time : null,
                'duration' => $this->formatDuration($duration),
                'duration_raw' => $now->timestamp - $offlineSince->timestamp,
                'status' => 'offline'
            ];
        }
        
        // Sort by duration (longest offline first)
        usort($offlineBeacons, function($a, $b) {
            return $b['duration_raw'] - $a['duration_raw'];
        });
        
        return $offlineBeacons;
    }

    /**
     * Get location name for a beacon
     */
    private function getBeaconLocation($beaconId): string
    {
        $beacon = Beacon::find($beaconId);
        return $beacon && $beacon->location ? $beacon->location->location_name : 'No Location';
    }
    
    /**
     * Format duration for human readability
     */
    private function formatDuration($duration): string
    {
        if ($duration->y > 0) {
            return $duration->y . ' year' . ($duration->y > 1 ? 's' : '') . 
                   ($duration->m > 0 ? ', ' . $duration->m . ' month' . ($duration->m > 1 ? 's' : '') : '');
        }
        if ($duration->m > 0) {
            return $duration->m . ' month' . ($duration->m > 1 ? 's' : '') . 
                   ($duration->d > 0 ? ', ' . $duration->d . ' day' . ($duration->d > 1 ? 's' : '') : '');
        }
        if ($duration->d > 0) {
            return $duration->d . ' day' . ($duration->d > 1 ? 's' : '') . 
                   ($duration->h > 0 ? ', ' . $duration->h . ' hour' . ($duration->h > 1 ? 's' : '') : '');
        }
        if ($duration->h > 0) {
            return $duration->h . ' hour' . ($duration->h > 1 ? 's' : '') . 
                   ($duration->i > 0 ? ', ' . $duration->i . ' minute' . ($duration->i > 1 ? 's' : '') : '');
        }
        if ($duration->i > 0) {
            return $duration->i . ' minute' . ($duration->i > 1 ? 's' : '') . 
                   ($duration->s > 0 ? ', ' . $duration->s . ' second' . ($duration->s > 1 ? 's' : '') : '');
        }
        return $duration->s . ' second' . ($duration->s > 1 ? 's' : '');
    }

    private function getChartData(string $range = 'last_3_hours'): array
    {
        $end = Carbon::now();
        
        // Set start time and interval based on range
        switch ($range) {
            case 'last_6_hours':
                $start = Carbon::now()->subHours(6);
                $intervalMinutes = 30;
                break;
            case 'last_12_hours':
                $start = Carbon::now()->subHours(12);
                $intervalMinutes = 60;
                break;
            case 'last_24_hours':
                $start = Carbon::now()->subHours(24);
                $intervalMinutes = 120;
                break;
            case 'last_week':
                $start = Carbon::now()->subWeek();
                $intervalMinutes = 720;
                break;
            case 'last_month':
                $start = Carbon::now()->subMonth();
                $intervalMinutes = 1440;
                break;
            case 'last_6_months':
                $start = Carbon::now()->subMonths(6);
                $intervalMinutes = 10080;
                break;
            case 'last_year':
                $start = Carbon::now()->subYear();
                $intervalMinutes = 43200;
                break;
            case 'last_10_years':
                $start = Carbon::now()->subYears(10);
                $intervalMinutes = 525600;
                break;
            default:
                $start = Carbon::now()->subHours(3);
                $intervalMinutes = 10;
                $range = 'last_3_hours';
                break;
        }

        $beacons = Beacon::all(['beacon_id']);
        $totalBeacons = $beacons->count();

        if ($totalBeacons === 0) {
            return [
                'categories' => [],
                'online' => [],
                'offline' => [],
                'range' => $range,
                'interval_minutes' => $intervalMinutes,
                'total_beacons' => 0,
            ];
        }

        // Generate time buckets
        $buckets = [];
        $currentBucket = clone $start;
        $currentBucket->second(0)->microsecond(0);
        
        $maxBuckets = 100;
        $bucketCount = 0;
        
        while ($currentBucket <= $end && $bucketCount < $maxBuckets) {
            $buckets[] = clone $currentBucket;
            $currentBucket->addMinutes($intervalMinutes);
            $bucketCount++;
        }

        $onlinePeaks = [];
        $offlinePeaks = [];
        $labels = [];

        foreach ($buckets as $bucketStart) {
            $bucketEnd = clone $bucketStart;
            $bucketEnd->addMinutes($intervalMinutes);
            
            // Format labels
            if ($range == 'last_10_years') {
                $labels[] = $bucketStart->format('Y');
            } elseif ($range == 'last_year') {
                $labels[] = $bucketStart->format('M Y');
            } elseif ($range == 'last_6_months') {
                $labels[] = $bucketStart->format('M d');
            } elseif ($range == 'last_month') {
                $labels[] = $bucketStart->format('M d');
            } elseif ($range == 'last_week') {
                $labels[] = $bucketStart->format('D H:i');
            } else {
                $labels[] = $bucketStart->format('H:i');
            }
            
            // Get all status logs in this interval with their exact times
            $logsInInterval = BeaconStatusLog::whereBetween('event_time', [$bucketStart, $bucketEnd])
                ->orderBy('event_time', 'asc')
                ->get();
            
            if ($logsInInterval->isEmpty()) {
                // No logs in interval - get the latest status before this interval for each beacon
                $latestStatuses = DB::table('beacon_status_logs as bsl1')
                    ->join(DB::raw('(SELECT beacon_id, MAX(event_time) as max_time FROM beacon_status_logs WHERE event_time <= ? GROUP BY beacon_id) as bsl2'), function($join) {
                        $join->on('bsl1.beacon_id', '=', 'bsl2.beacon_id')
                            ->on('bsl1.event_time', '=', 'bsl2.max_time');
                    })
                    ->setBindings([$bucketStart])
                    ->select('bsl1.beacon_id', 'bsl1.status')
                    ->get()
                    ->keyBy('beacon_id');
                
                $online = 0;
                $offline = 0;
                
                foreach ($beacons as $beacon) {
                    if (isset($latestStatuses[$beacon->beacon_id])) {
                        if ($latestStatuses[$beacon->beacon_id]->status === 'online') {
                            $online++;
                        } else {
                            $offline++;
                        }
                    } else {
                        $offline++;
                    }
                }
                
                $onlinePeaks[] = $online;
                $offlinePeaks[] = $offline;
                continue;
            }
            
            // Group logs by timestamp to see the status at each exact moment
            $logsByTimestamp = $logsInInterval->groupBy(function($log) {
                return $log->event_time->format('Y-m-d H:i:s');
            });
            
            $maxOnline = 0;
            $maxOffline = 0;
            
            // For each timestamp in the interval, calculate online/offline counts
            foreach ($logsByTimestamp as $timestamp => $logsAtMoment) {
                $onlineAtMoment = $logsAtMoment->where('status', 'online')->count();
                $offlineAtMoment = $logsAtMoment->where('status', 'offline')->count();
                
                $maxOnline = max($maxOnline, $onlineAtMoment);
                $maxOffline = max($maxOffline, $offlineAtMoment);
            }
            
            // Also need to consider the status between logs - get the status just before the interval starts
            $statusBeforeInterval = DB::table('beacon_status_logs as bsl1')
                ->join(DB::raw('(SELECT beacon_id, MAX(event_time) as max_time FROM beacon_status_logs WHERE event_time < ? GROUP BY beacon_id) as bsl2'), function($join) {
                    $join->on('bsl1.beacon_id', '=', 'bsl2.beacon_id')
                        ->on('bsl1.event_time', '=', 'bsl2.max_time');
                })
                ->setBindings([$bucketStart])
                ->select('bsl1.beacon_id', 'bsl1.status')
                ->get()
                ->keyBy('beacon_id');
            
            $initialOnline = 0;
            $initialOffline = 0;
            
            foreach ($beacons as $beacon) {
                if (isset($statusBeforeInterval[$beacon->beacon_id])) {
                    if ($statusBeforeInterval[$beacon->beacon_id]->status === 'online') {
                        $initialOnline++;
                    } else {
                        $initialOffline++;
                    }
                } else {
                    $initialOffline++;
                }
            }
            
            $maxOnline = max($maxOnline, $initialOnline);
            $maxOffline = max($maxOffline, $initialOffline);
            
            $onlinePeaks[] = $maxOnline;
            $offlinePeaks[] = $maxOffline;
        }

        return [
            'categories' => $labels,
            'online' => $onlinePeaks,
            'offline' => $offlinePeaks,
            'range' => $range,
            'interval_minutes' => $intervalMinutes,
            'total_beacons' => $totalBeacons,
        ];
    }
}