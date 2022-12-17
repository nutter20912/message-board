<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserNotificationCollection;
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
        path: '/notifications',
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
            ->with('notifiable')
            ->orderby('id', 'desc')
            ->paginate(perPage: 4, page: $page);

        return (new UserNotificationCollection($notifications))->resolve();
    }
}
