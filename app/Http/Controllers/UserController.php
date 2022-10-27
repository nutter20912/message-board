<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Post(
        path: '/users',
        description: '新增使用者',
        tags: ['users'],
        operationId: 'users.store',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', description: '姓名'),
                    new OA\Property(property: 'email', type: 'string', description: '信箱'),
                    new OA\Property(property: 'password', type: 'string', description: '密碼'),
                ]
            ),
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'success',
        content: new OA\JsonContent(
            allOf: [
                new OA\Schema(ref: '#/components/schemas/apiResponse'),
                new OA\Schema(properties: [
                    new OA\Property(property: 'result', type: UserResource::class),
                ]),
            ],
        )
    )]
    #[OA\Response(response: 400, description: 'Bad request')]
    /**
     * 新增使用者
     *
     * @param App\Http\Requests\UserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->save();

        return response()->json([
            'code' => 200,
            'message' => 'ok',
            'result' => new UserResource($user),
        ], 200);
    }

    #[OA\Get(
        path: '/users/{id}',
        description: '查詢使用者',
        tags: ['users'],
        operationId: 'users.show',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: '使用者編號',
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
                    new OA\Property(property: 'result', type: UserResource::class),
                ]),
            ],
        )
    )]
    #[OA\Response(response: 400, description: 'Bad request')]
    /**
     * 取得使用者
     *
     * @param App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json([
            'code' => 200,
            'message' => 'ok',
            'result' => new UserResource($user),
        ], 200);
    }

    /**
     * 更新使用者
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 刪除使用者
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
