<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class UserNotificationCollection extends ResourceCollection
{
    #[OA\Property(
        property: 'data',
        description: '資料',
        type: 'array',
        items: new OA\Items(type: UserNotificationResource::class)
    )]
    #[OA\Property(
        property: 'paginator',
        description: '分頁訊息',
        type: 'object',
        properties: [
            new OA\Property(property: 'current_page', description: '當前頁', type: 'int'),
            new OA\Property(property: 'last_page', description: '最後一頁', type: 'int'),
            new OA\Property(property: 'per_page', description: '每頁', type: 'int'),
            new OA\Property(property: 'total', description: '合計', type: 'int'),
        ],
    )]

    /**
     * @var string
     */
    public $collects = UserNotificationResource::class;

    protected $preserveAllQueryParameters = false;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'paginator' => [
                'current_page' => $this->resource->currentPage(),
                'last_page' => $this->resource->lastPage(),
                'per_page' => $this->resource->perPage(),
                'total' => $this->resource->total(),
            ],
        ];
    }
}
