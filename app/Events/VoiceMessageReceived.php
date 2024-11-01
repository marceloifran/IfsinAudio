<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class VoiceMessageReceived implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $audioUrl;

    public function __construct($audioUrl)
    {
        $this->audioUrl = $audioUrl;
    }

    public function broadcastOn()
    {
        return new Channel('wakie-talkie');
    }

    public function broadcastAs()
    {
        return 'voice-message';
    }
}
