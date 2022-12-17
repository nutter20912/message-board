<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class UserLoginRecord extends Model
{
    use HasFactory;

    #[OA\Property(property: 'id', description: '編號', type: 'int', format: 'int64')]
    #[OA\Property(property: 'ip', description: 'IP位址', type: 'string', maxLength: 45)]
    #[OA\Property(property: 'host', description: '服務器域名', type: 'string', format: 'hostname')]
    #[OA\Property(property: 'user_agent', description: '用戶代理', type: 'string', maxLength: 255)]
    #[OA\Property(property: 'request_time', description: '請求開始時間', type: 'string', format: 'date-time')]

    /**
     * 資料表名稱
     *
     * @var string
     */
    protected $table = 'users_login_records';

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
        'ip',
        'host',
        'user_agent',
        'request_time',
    ];

    /**
     * 取得登入通知
     */
    public function notification()
    {
        return $this->morphOne(UserNotification::class, 'notifiable');
    }
}
