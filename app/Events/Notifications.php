<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\User;

class Notifications implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public $time;

    private $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $message)
    {
        $this->user = $user;
        $this->message = $message;
        $this->time = $user->created_at->format('d/m/Y H:i');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('notifications.'.$this->user->id);
        //return new PrivateChannel('notifications.'.$this->user->id);
    }
}
