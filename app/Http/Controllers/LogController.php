<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\View\View;

class LogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 250);
        
        $limit = min($limit, 800); 
        
        $logs = Log::orderBy('datetime', 'desc')  
            ->limit($limit)
            ->get()
            ->reverse() 
            ->values(); 
        
        return response()->json($logs);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string',
            'type'    => 'nullable|string|in:info,error,success,data',
        ]);

        Log::create([
            'user_id'  => Auth::id(), 
            'datetime' => now(),
            'message'  => $request->message,
            'type'     => $request->type ?? 'info',
        ]);

        return response()->json(['success' => true]);
    }

    public function showLogsPage(Request $request): View
    {
        $baseQuery = Log::query();

        $minDateStr = (clone $baseQuery)->min('datetime');
        $maxDateStr = (clone $baseQuery)->max('datetime');

        $minDate = $minDateStr ? Carbon::parse($minDateStr) : null;
        $maxDate = $maxDateStr ? Carbon::parse($maxDateStr) : null;

        $query = Log::with('user');

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('datetime', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('datetime', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $query->where('message', 'like', '%' . $request->search . '%');
        }

        $logs = $query->orderBy('id', 'desc')->paginate(100);

        return view('settings.logs', compact('logs', 'minDate', 'maxDate'));
    }
}