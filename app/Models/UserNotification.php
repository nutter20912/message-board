<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class UserNotification extends Model
{
    use HasFactory;

    #[OA\Property(property: 'id', description: '編號', type: 'int', format: 'int64')]
    #[OA\Property(property: 'content', description: '內容', type: 'string', maxLength: 255)]
    #[OA\Property(property: 'created_at', description: '建立時間', type: 'string', format: 'date-time')]
    #[OA\Property(property: 'user_id', description: '使用者編號', type: 'int', format: 'int64')]
    #[OA\Property(property: 'notifiable_type', description: '可通知類型', type: 'string', maxLength: 255)]
    #[OA\Property(property: 'notifiable_id', description: '可通知類型編號', type: 'int', format: 'int64')]

    /**
     * 資料表名稱
     *
     * @var string
     */
    protected $table = 'users_notifications';

    /**
     * 是否維護時間戳
     */
    public $timestamps = false;

    /**
     * 可填充屬性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
    ];

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
     * 可通知模型
     */
    public function notifiable()
    {
        return $this->morphTo();
    }
}
