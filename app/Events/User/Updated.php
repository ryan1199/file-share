<?php

namespace App\Events\User;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Updated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public User $user)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $this->user->load('archiveBoxes');
        $channels = [
            new Channel('user.index'),
            new Channel('user.show.'.$this->user->slug),
            new Channel('archive-box.user.create'),
        ];
        if ($this->user->archiveBoxes != null) {
            foreach ($this->user->archiveBoxes as $archiveBox) {
                $channels[] = new Channel('archive-box.show.'.$archiveBox->slug.'.user.index');
                $channels[] = new Channel('archive-box.show.'.$archiveBox->slug.'.user.edit');
            }
        }
        return $channels;
    }
}
