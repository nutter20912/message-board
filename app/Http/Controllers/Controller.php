<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

!defined('APP_URL') && define('APP_URL', env('APP_URL'));

#[OA\OpenApi(
    info: new OA\Info(
        version: '1.0.0',
        title: '簡易留言板',
        description: '簡易留言板 api doc',
        contact: new OA\contact(
            name: 'paul chou',
            url: 'https://github.com/nutter20912',
        )
    ),
    servers: [
        new OA\Server(
            url: '{appUrl}:{port}/{basePath}',
            description: 'local API server',
            variables: [
                new OA\ServerVariable(
                    serverVariable: 'appUrl',
                    enum: [APP_URL],
                    default: APP_URL,
                ),
                new OA\ServerVariable(
                    serverVariable: 'port',
                    enum: ['80'],
                    default: '80',
                ),
                new OA\ServerVariable(
                    serverVariable: 'basePath',
                    enum: ['api'],
                    default: 'api',
                )
            ]
        )
    ],
    security: [
        ['XSRF-TOKEN' => []],
    ],
)]
#[OA\Schema(
    schema: 'apiResponse',
    description: '基本回應格式',
    type: 'object',
    properties: [
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string', example: 'ok'),
        new OA\Property(property: 'result', type: 'object', nullable: true),
    ],
)]
#[OA\SecurityScheme(
    securityScheme: 'XSRF-TOKEN',
    type: 'apiKey',
    name: 'X-XSRF-TOKEN',
    in: 'header'
)]
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
