<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets; // ✅ important
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use App\Models\Numero;

class NumeroUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $numero;
    public $deleted = false;

    public function __construct(Numero $numero, bool $deleted = false)
    {
        $this->numero = $numero;
        $this->deleted = $deleted;
    }

    public function broadcastOn()
    {
        return new Channel('numeros'); // matches Echo channel
    }

    public function broadcastAs()
    {
        return 'NumeroUpdated';
    }
}