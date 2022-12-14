<?php

namespace App\Models;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastableModelEventOccurred;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class Comment extends Model
{
    use BroadcastsEvents, HasFactory;

    #[OA\Property(property: 'id', description: '編號', type: 'int', format: 'int64')]
    #[OA\Property(property: 'content', description: '內容', type: 'string', maxLength: 255)]
    #[OA\Property(property: 'created_at', description: '建立時間', type: 'string', format: 'date-time')]
    #[OA\Property(property: 'updated_at', description: '更新時間', type: 'string', format: 'date-time')]
    #[OA\Property(property: 'post_id', description: '文章編號', type: 'int', format: 'int64')]
    #[OA\Property(property: 'user_id', description: '使用者編號', type: 'int', format: 'int64')]

    /**
     * 資料表名稱
     *
     * @var string
     */
    protected $table = 'comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'user_id',
        'post_id',
    ];

    /**
     * 評論文章
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * 使用者
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 事件推播
     *
     * @param string $event
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn($event)
    {
        return match ($event) {
            'created' => [new PrivateChannel("users.{$this->post->user_id}")],
            default => [],
        };
    }

    /**
     * 自定義事件廣播創建
     *
     * @param  string  $event
     * @return \Illuminate\Database\Eloquent\BroadcastableModelEventOccurred
     */
    protected function newBroadcastableEvent($event)
    {
        return (new BroadcastableModelEventOccurred($this, $event))
            ->dontBroadcastToCurrentUser();
    }
}
