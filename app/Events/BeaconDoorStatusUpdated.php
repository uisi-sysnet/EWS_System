<?php

namespace App\Events;

use App\Models\Beacon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

use Illuminate\Support\Facades\Log;

class BeaconDoorStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $beacon;

    public function __construct(Beacon $beacon)
    {
        $this->beacon = $beacon;
    }

    public function broadcastOn()
    {
        return new Channel('beacons');
    }

    public function broadcastAs()
    {
        return 'beacon.door.updated';
    }

    public function broadcastWith()
    {
        Log::info('🚪 Broadcasting door event via Pusher', [
            'beacon_id' => $this->beacon->beacon_id,
            'is_door_open' => $this->beacon->is_door_open
        ]);

        return [
            'beacon_id' => $this->beacon->beacon_id,
            'name' => $this->beacon->name,
            'is_door_open' => $this->beacon->is_door_open,
            'last_opened_at' => $this->beacon->last_opened_at?->toISOString(),
        ];
    }
}
