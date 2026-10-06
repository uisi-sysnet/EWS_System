<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SystemStatusReportController extends Controller
{
    public function download(): BinaryFileResponse|RedirectResponse
    {
        $exitCode = Artisan::call('telegram:image-status-report', ['--save-only' => true]);
        $path = trim(Artisan::output());

        if ($exitCode !== 0 || $path === '' || ! is_file($path)) {
            Log::error('Unable to generate downloadable system status report', [
                'exit_code' => $exitCode,
                'output' => $path,
            ]);

            return back()->with('report_feedback', 'The system status report could not be generated.');
        }

        return response()->download($path, 'muntinlupa-system-status-report.png')->deleteFileAfterSend(true);
    }

    public function sendToTelegram(): RedirectResponse
    {
        abort_unless(in_array(auth()->user()->user_level ?? '', ['superadmin', 'admin'], true), 403);

        $exitCode = Artisan::call('telegram:image-status-report');

        if ($exitCode !== 0) {
            Log::error('Manual Telegram system status report failed', [
                'output' => Artisan::output(),
            ]);

            return back()->with('report_feedback', 'The report could not be sent to Telegram. Check Telegram configuration and the application log.');
        }

        return back()->with('report_feedback', 'System status report sent to Telegram.');
    }
}
