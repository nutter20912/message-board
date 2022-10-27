<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Get(
        path: '/auth/csrf-cookie',
        description: '取得 XSRF-TOKEN',
        tags: ['auth'],
        operationId: 'auth.sanctum.csrf-cookie',
    )]
    #[OA\Response(
        response: 204,
        description: 'set an XSRF-TOKEN cookie',
    )]
    #[OA\Response(response: 400, description: 'Bad Request')]
    /**
     * @see \Laravel\Sanctum\Http\Controllers\CsrfCookieController
     */
    public function getCsrfCookie()
    {
        return Redirect::route('sanctum.csrf-cookie');
    }
}
