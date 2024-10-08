<?php

namespace App\Events\ArchiveBox\User;

use App\Models\ArchiveBox;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PermissionChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public ArchiveBox $archiveBox, public User $user, public $permissionLevel3 = false)
    {
        if ($permissionLevel3 != false) {
            $this->permissionLevel3 = true;
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
            new Channel('archive-box.show.'.$this->archiveBox->slug.'.user.index'),
        ];
        if ($this->permissionLevel3) {
            $channels[] = new Channel('archive-box.index');
        }
        return $channels;
    }
}
