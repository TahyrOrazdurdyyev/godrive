<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load(['user', 'driver', 'service', 'zone']);
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('user.' . $this->order->user_id),
            new PrivateChannel('driver.' . $this->order->driver_id),
            new Channel('order.' . $this->order->id),
        ];
    }

    public function broadcastAs()
    {
        return 'order.updated';
    }

    public function broadcastWith()
    {
        return [
            'order' => $this->order,
            'message' => 'Order status updated to: ' . $this->order->status
        ];
    }
}

