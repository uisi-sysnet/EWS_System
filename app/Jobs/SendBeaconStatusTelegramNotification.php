<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\TelegramNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBeaconStatusTelegramNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Plain scalars only (no Beacon model) so this survives queue
     * serialization cleanly and doesn't re-fetch a stale model.
     */
    public function __construct(
        public string $beaconName,
        public string $locationName,
        public ?string $group,
        public bool $status,
    ) {}

    public function handle(TelegramNotifier $telegram): void
    {
        $emoji = $this->status ? '🟢' : '🔴';
        $state = $this->status ? 'ONLINE' : 'OFFLINE';

        $lines = [
            sprintf('%s <b>Beacon %s</b>', $emoji, $state),
            sprintf('Station: %s', $this->beaconName),
            sprintf('Location: %s', $this->locationName),
        ];

        if ($this->group) {
            $lines[] = sprintf('Group: %s', $this->group);
        }

        $lines[] = sprintf('Time: %s', now()->format('M d, Y g:i A'));

        $telegram->sendMessage(implode("\n", $lines));
    }
}