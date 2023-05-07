<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    /**
     * 使用者登入
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    #[OA\Post(
        path: '/api/auth/login',
        description: '使用者登入',
        tags: ['auth'],
        operationId: 'auth.login',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'email', type: 'string', description: '信箱'),
                    new OA\Property(property: 'password', type: 'string', description: '密碼'),
                ]
            ),
        ),
    )]
    #[OA\Response(
        response: 200,
        description: '登入成功',
        content: new OA\JsonContent(
            allOf: [
                new OA\Schema(ref: '#/components/schemas/apiResponse'),
                new OA\Schema(properties: [
                    new OA\Property(property: 'result', properties: [
                        new OA\Property(property: 'user', type: UserResource::class),
                        new OA\Property(property: 'token', type: 'string'),
                    ],),
                ]),
            ],
        )
    )]
    #[OA\Response(response: 400, description: 'Bad Request')]
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            throw new BadRequestException('Login failed', 10001);
        }

        $user = $request->user();
        $user->tokens()->delete();
        $token =  $user->createToken(
            name: 'pc',
            expiresAt: Carbon::now()
                ->addMinutes(config('sanctum.expiration'))
                ->toDateTime(),
        );

        return response()->json([
            'code' => 200,
            'message' => 'ok',
            'result' => [
                'user' => new UserResource($request->user()),
                'token' => $token->plainTextToken,
            ],
        ], 200);
    }

    /**
     * 使用者登出
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    #[OA\Put(
        path: '/api/auth/logout',
        description: '使用者登出',
        tags: ['auth'],
        operationId: 'auth.logout',
    )]
    #[OA\Response(response: 200, description: '登出成功', content: new OA\JsonContent())]
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'code' => 200,
            'message' => 'ok',
            'result' => null,
        ], 200);
    }
}
