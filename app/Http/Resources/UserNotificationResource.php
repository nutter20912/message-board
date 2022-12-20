<?php

namespace App\Http\Resources;

use App\Enums\UserNotifiable;
use App\Models\UserLoginRecord;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class UserNotificationResource extends JsonResource
{
    #[OA\Property(property: 'id', description: '編號', type: 'int', format: 'int64')]
    #[OA\Property(property: 'content', description: '內容', type: 'string')]
    #[OA\Property(property: 'created_at', description: '建立時間', type: 'string', format: 'date-time')]
    #[OA\Property(property: 'notifiable_type', description: '可通知模型類別', type: 'string', enum: ['UserLoginRecord'])]
    #[OA\Property(property: 'notifiable_id', description: '可通知模型編號', type: 'int', format: 'int64')]
    #[OA\Property(
        property: 'notifiable',
        oneOf: [ new OA\Property(type: UserLoginRecord::class)],
    )]

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
            'notifiable_type' => UserNotifiable::tryFrom($this->notifiable_type)?->name,
            'notifiable_id' => $this->notifiable_id,
            'notifiable' => $this->whenLoaded('notifiable'),
        ];
    }
}
