<?php

namespace App\Events\User;

use App\Models\ArchiveBox;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Deleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public $userName, public $archiveBox = null)
    {
        if ($archiveBox != null) {
            $this->archiveBox = ArchiveBox::find($archiveBox);
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new Channel('user.index'),
            new Channel('archive-box.user.create'),
        ];
        if ($this->archiveBox != null) {
            $channels[] = new Channel('archive-box.index');
            $channels[] = new Channel('archive-box.show.'.$this->archiveBox->slug.'.user.index');
            $channels[] = new Channel('archive-box.show.'.$this->archiveBox->slug.'.user.edit');
        }
        return $channels;
    }
}
