<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class UserResource extends JsonResource
{
    #[OA\Property(property: 'id', description: '編號', type: 'int', example: '1')]
    #[OA\Property(property: 'name', description: '姓名', type: 'string', example: 'paul')]
    #[OA\Property(property: 'email', description: '信箱', type: 'string', example: 'asd@asca.com')]
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
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'relationship' => $this->whenLoaded('owners', fn () => $this->owners->first()?->relationship),
        ];
    }
}
