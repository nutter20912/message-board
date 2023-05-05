<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserNotificationCollection;
use App\Http\Resources\UserNotificationResource;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserNotificationController extends Controller
{
    /**
     * 取得使用者全部通知
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    #[OA\Get(
        path: '/api/notifications',
        description: '取得使用者全部通知',
        tags: ['users'],
        operationId: 'notifications.index',
        parameters: [
            new OA\Parameter(
                name: 'page',
                in: 'query',
                description: '分頁',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'success',
        content: new OA\JsonContent(
            allOf: [
                new OA\Schema(ref: '#/components/schemas/apiResponse'),
                new OA\Schema(properties: [
                    new OA\Property(property: 'result', type: UserNotificationCollection::class),
                ]),
            ],
        )
    )]
    public function index(Request $request)
    {
        $page = $request->input('page', 1);

        $notifications = $request->user()
            ->notifications()
            ->orderby('id', 'desc')
            ->paginate(perPage: 15, page: $page);

        return (new UserNotificationCollection($notifications))->resolve();
    }

    /**
     * 取得通知
     *
     * @param  \App\Models\UserNotification  $userNotification
     * @return \Illuminate\Http\Response
     */
    #[OA\Get(
        path: '/api/notifications/{id}',
        description: '取得通知',
        tags: ['users'],
        operationId: 'notifications.show',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: '通知編號',
                required: 'true',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'success',
        content: new OA\JsonContent(
            allOf: [
                new OA\Schema(ref: '#/components/schemas/apiResponse'),
                new OA\Schema(properties: [
                    new OA\Property(property: 'result', type: UserNotificationResource::class),
                ]),
            ],
        )
    )]
    public function show(UserNotification $userNotification)
    {
        $this->authorize('view', $userNotification);

        return response()->json([
            'code' => 200,
            'message' => 'ok',
            'result' => new UserNotificationResource($userNotification->load('notifiable')),
        ], 200);
    }
}
