<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;
use Illuminate\View\View;

class SystemLogController extends Controller
{
    public function index(Request $request): View
    {
        $baseQuery = Activity::query();

        $minDateStr = (clone $baseQuery)->min('created_at');
        $maxDateStr = (clone $baseQuery)->max('created_at');

        $minDate = $minDateStr ? Carbon::parse($minDateStr) : null;
        $maxDate = $maxDateStr ? Carbon::parse($maxDateStr) : null;

        $query = Activity::with('causer', 'subject');

        // Filter by event
        if ($request->filled('event') && $request->event !== 'all') {
            $query->where('event', $request->event);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in description, log_name, event, or properties
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', $search)
                    ->orWhere('log_name', 'like', $search)
                    ->orWhere('event', 'like', $search)
                    ->orWhere('properties', 'like', $search);
            });
        }

        // Use paginate instead of get
        $logs = $query->orderBy('id', 'desc')->paginate(100);

        // Get unique log names for filter dropdown
        $logNames = Activity::distinct()->pluck('log_name')->filter()->values();

        return view('settings.systemLogs', compact('logs', 'minDate', 'maxDate', 'logNames'));
    }
}
