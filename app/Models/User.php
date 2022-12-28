<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Request;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    #[OA\Property(property: 'id', description: '編號', type: 'int', format: 'int64')]
    #[OA\Property(property: 'name', description: '姓名', type: 'string', maxLength: 255)]
    #[OA\Property(property: 'email', description: '信箱', type: 'string', maxLength: 100)]
    #[OA\Property(property: 'email_verified_at', description: '信箱驗證時間', type: 'string', format: 'date-time', nullable: true)]
    #[OA\Property(property: 'password', description: '密碼', type: 'string', format: 'password', maxLength: 255)]
    #[OA\Property(property: 'remember_token', description: '記住令牌', type: 'string', maxLength: 100)]
    #[OA\Property(property: 'created_at', description: '建立時間', type: 'string', format: 'date-time')]
    #[OA\Property(property: 'updated_at', description: '更新時間', type: 'string', format: 'date-time')]

    /**
     * 資料表名稱
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 路由模型綁定
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return match ($field) {
            'child_id' => Request::user()
                ->children()
                ->where($field, $value)
                ->firstOrFail(),
            default => parent::resolveRouteBinding($value),
        };
    }

    /**
     * 使用者文章
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * 使用者頻論
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * 使用者通知
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    /**
     * 擁有者
     */
    public function owners()
    {
        return $this->belongsToMany(
            related: User::class,
            table: 'user_relationship',
            foreignPivotKey: 'child_id',
            relatedPivotKey: 'owner_id',
        )
            ->using(UserRelationship::class)
            ->as('relationship')
            ->withPivot(['id', 'type'])
            ->withTimestamps();
    }

    /**
     * 下層
     */
    public function children()
    {
        return $this->belongsToMany(
            related: User::class,
            table: 'user_relationship',
            foreignPivotKey: 'owner_id',
            relatedPivotKey: 'child_id',
        )
            ->using(UserRelationship::class)
            ->as('relationship')
            ->withPivot(['id', 'type'])
            ->withTimestamps();
    }
}
