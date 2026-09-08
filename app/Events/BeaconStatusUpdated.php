<?php

namespace App\Events;

use App\Models\Beacon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BeaconStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $beacon;

    /**
     * Create a new event instance.
     */
    public function __construct(Beacon $beacon)
    {
        $this->beacon = $beacon;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('beacons'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'beacon.status.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'beacon_id' => $this->beacon->beacon_id,
            'id' => $this->beacon->id,
            'name' => $this->beacon->name,
            'status' => (bool) $this->beacon->status,
            'location' => $this->beacon->location?->location_name,
            'group' => $this->beacon->group,
            'last_seen_at' => $this->beacon->last_seen_at?->toIso8601String(),
            'updated_at' => $this->beacon->updated_at->toIso8601String(),
        ];
    }

    /**
     * Handle a broadcast failure.
     */
    public function failed(\Exception $e): void
    {
        Log::error('BeaconStatusUpdated broadcast failed', [
            'error' => $e->getMessage(),
            'beacon_id' => $this->beacon->id,
            'trace' => $e->getTraceAsString()
        ]);
    }
}