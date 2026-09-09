<?php

namespace App\Console\Commands;

use App\Http\Controllers\Settings\ApiController;
use App\Models\Beacon;
use App\Services\TelegramNotifier;
use App\Support\StatusThreshold;
use Illuminate\Console\Command;

class SendSystemStatusReport extends Command
{
    /**
     * php artisan telegram:status-report
     */
    protected $signature = 'telegram:status-report';

    protected $description = 'Send a beacon/siren status summary to Telegram (run on a schedule, e.g. every few hours)';

    public function handle(ApiController $apiController, TelegramNotifier $telegram): int
    {
        $beaconCounts  = Beacon::getCounts();
        $beaconOnline  = $beaconCounts['online'];
        $beaconOffline = $beaconCounts['offline'];
        $beaconTotal   = $beaconOnline + $beaconOffline;

        $sirenCounts = $apiController->getSirenStatusCounts();

        $now = now();

        $lines = [];
        $lines[] = '<b>' . $now->format('F j, Y') . '</b>';
        $lines[] = $now->format('g:i A');
        $lines[] = '';
        $lines[] = '<b>Beacon Status: ' . StatusThreshold::label($beaconOffline, $beaconTotal) . '</b>';
        $lines[] = "{$beaconOffline} / {$beaconTotal} beacon station(s) offline";
        $lines[] = '';

        if ($sirenCounts === null) {
            // Siren API was unreachable - say so plainly rather than guessing at numbers.
            $lines[] = '<b>Siren Status: Unknown</b>';
            $lines[] = 'Unable to reach siren API';
        } else {
            $sirenOffline = $sirenCounts['offline'];
            $sirenTotal   = $sirenCounts['total'];
            $lines[] = '<b>Siren Status: ' . StatusThreshold::label($sirenOffline, $sirenTotal) . '</b>';
            $lines[] = "{$sirenOffline} / {$sirenTotal} siren(s) offline";
        }

        $sent = $telegram->sendMessage(implode("\n", $lines));

        if (! $sent) {
            $this->error('Failed to send system status report to Telegram.');
            return self::FAILURE;
        }

        $this->info('System status report sent to Telegram.');
        return self::SUCCESS;
    }
}