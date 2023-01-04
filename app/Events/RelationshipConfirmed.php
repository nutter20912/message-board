<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RelationshipConfirmed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        /** @param \App\Models\User $user 使用者 */
        public User $user,

        /** @param int $requestId 請求使用者id */
        public int $requestId,

        /** @param bool $confirm 是否確認 */
        public bool $confirm,
    )
    {
        //
    }

    /**
     * 是否廣播事件
     *
     * @return bool
     */
    public function broadcastWhen()
    {
        return $this->confirm;
    }

    /**
     * 取得事件廣播頻道
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('users.' . $this->requestId);
    }
}
