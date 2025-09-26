<?php

namespace App\Events;

use App\Models\Driver;
use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $driver;
    public $order;

    public function __construct(Driver $driver, Order $order = null)
    {
        $this->driver = $driver;
        $this->order = $order;
    }

    public function broadcastOn()
    {
        $channels = [];
        
        if ($this->order) {
            $channels[] = new PrivateChannel('user.' . $this->order->user_id);
            $channels[] = new Channel('order.' . $this->order->id);
        }
        
        return $channels;
    }

    public function broadcastAs()
    {
        return 'driver.location.updated';
    }

    public function broadcastWith()
    {
        return [
            'driver_id' => $this->driver->id,
            'location' => [
                'lat' => $this->driver->location_lat,
                'lng' => $this->driver->location_lng,
                'rotation' => $this->driver->rotation,
            ],
            'order_id' => $this->order?->id,
            'timestamp' => now()->toISOString(),
        ];
    }
}

