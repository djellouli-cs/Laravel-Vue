<?php
// app/Events/TestEvent.php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class TestEvent implements ShouldBroadcast
{
    use Dispatchable;

    public $message;

    public function __construct()
    {
        $this->message = "Hello from Reverb!";
    }

    // Public channel
    public function broadcastOn()
    {
        return new Channel('test');
    }

    // Optional: custom name (makes JS easier)
    public function broadcastAs()
    {
        return 'TestEvent';
    }
}