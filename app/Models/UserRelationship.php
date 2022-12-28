<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class UserRelationship extends Pivot
{
    use HasFactory;

    #[OA\Property(property: 'id', description: '編號', type: 'int', format: 'int64')]
    #[OA\Property(property: 'type', description: '類型', type: 'int')]
    #[OA\Property(property: 'created_at', description: '建立時間', type: 'string', format: 'date-time')]
    #[OA\Property(property: 'updated_at', description: '更新時間', type: 'string', format: 'date-time')]
    #[OA\Property(property: 'owner_id', description: '所有者編號', type: 'int', format: 'int64')]
    #[OA\Property(property: 'child_id', description: '下層編號', type: 'int', format: 'int64')]

    /**
     * 資料表名稱
     *
     * @var string
     */
    protected $table = 'user_relationship';
}
