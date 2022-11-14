<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class CommentResource extends JsonResource
{
    #[OA\Property(property: 'id', description: '編號', type: 'int', format: 'int64')]
    #[OA\Property(property: 'content', description: '內容', type: 'string', maxLength: 255)]
    #[OA\Property(property: 'created_at', description: '建立時間', type: 'string', format: 'date-time')]
    #[OA\Property(property: 'updated_at', description: '更新時間', type: 'string', format: 'date-time')]

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->when($request->routeIs('posts.comments.index'), $this->user->only('id', 'name')),
        ];
    }
}
