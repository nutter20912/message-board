<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class Post extends Model
{
    use HasFactory;

    #[OA\Property(property: 'id', description: '編號', type: 'int', format: 'int64')]
    #[OA\Property(property: 'title', description: '標題', type: 'string', maxLength: 255)]
    #[OA\Property(property: 'content', description: '內容', type: 'string')]
    #[OA\Property(property: 'created_at', description: '建立時間', type: 'string', format: 'date-time')]
    #[OA\Property(property: 'updated_at', description: '更新時間', type: 'string', format: 'date-time')]
    #[OA\Property(property: 'user_id', description: '使用者編號', type: 'int', format: 'int64')]

    /**
     * 資料表名稱
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'user_id',
    ];

    /**
     * 作者
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
