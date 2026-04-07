<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PermanenceUpdated implements ShouldBroadcast
{
    public $permanence;
    public $action; // create | update | delete

    public function __construct($permanence, $action)
    {
        $this->permanence = $permanence;
        $this->action = $action;
    }

    public function broadcastOn()
    {
        return ['permanences'];
    }

    public function broadcastAs()
    {
        return 'PermanenceUpdated';
    }

    public function broadcastWith()
    {
        return [
            'permanence' => $this->permanence,
            'action' => $this->action
        ];
    }
}