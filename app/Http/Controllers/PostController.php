<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PostController extends Controller
{
    /**
     * 取得所有文章
     *
     * @return \Illuminate\Http\Response
     */
    #[OA\Get(
        path: '/api/posts',
        description: '取得所有文章',
        tags: ['posts'],
        operationId: 'posts.index',
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
                    new OA\Property(property: 'result', type: PostCollection::class),
                ]),
            ],
        )
    )]
    #[OA\Response(response: 400, description: 'Bad request')]
    public function index(Request $request)
    {
        $page = $request->input('page', 1);

        /** @var Illuminate\Pagination\Paginator */
        $posts = Post::with('user')
            ->orderby('id', 'desc')
            ->paginate(perPage: 4, page: $page);


        return (new PostCollection($posts))->resolve();
    }

    /**
     * 新增文章
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    #[OA\Post(
        path: '/api/posts',
        description: '新增文章',
        tags: ['posts'],
        operationId: 'posts.store',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', description: '標題'),
                    new OA\Property(property: 'content', type: 'string', description: '內容'),
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
                    new OA\Property(property: 'result', type: PostResource::class),
                ]),
            ],
        )
    )]
    #[OA\Response(response: 400, description: 'Bad request')]
    public function store(PostRequest $request)
    {
        $title = $request->input('title');
        $content = $request->input('content');
        $user = $request->user();

        $post = new Post();
        $post->title = $title;
        $post->content = $content;

        $user->posts()->save($post);

        return response()->json([
            'code' => 200,
            'message' => 'ok',
            'result' => new PostResource($post),
        ], 200);
    }

    /**
     * 取得文章
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    #[OA\Get(
        path: '/api/posts/{id}',
        description: '取得文章',
        tags: ['posts'],
        operationId: 'posts.show',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: '文章編號',
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
                    new OA\Property(property: 'result', type: PostResource::class),
                ]),
            ],
        )
    )]
    #[OA\Response(response: 400, description: 'Bad request')]
    public function show(Post $post)
    {
        return response()->json([
            'code' => 200,
            'message' => 'ok',
            'result' => new PostResource($post),
        ], 200);
    }

    /**
     * 更新文章
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    #[OA\Put(
        path: '/api/posts/{id}',
        description: '更新文章',
        tags: ['posts'],
        operationId: 'posts.update',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: '文章編號',
                required: 'true',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', description: '標題'),
                    new OA\Property(property: 'content', type: 'string', description: '內容'),
                ]
            ),
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'success',
        content: new OA\JsonContent(ref: '#/components/schemas/apiResponse')
    )]
    #[OA\Response(response: 400, description: 'Bad request')]
    public function update(Request $request, Post $post)
    {
        $title = $request->input('title');
        $content = $request->input('content');

        $this->authorize('update', $post);

        $params = [
            'title' => $title,
            'content' => $content,
        ];

        $post->update(array_filter($params));

        return response()->json([
            'code' => 200,
            'message' => 'ok',
        ], 200);
    }

    /**
     * 刪除文章
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    #[OA\Delete(
        path: '/api/posts/{id}',
        description: '刪除文章',
        tags: ['posts'],
        operationId: 'posts.destroy',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: '文章編號',
                required: 'true',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'success',
        content: new OA\JsonContent(ref: '#/components/schemas/apiResponse')
    )]
    #[OA\Response(response: 400, description: 'Bad request')]
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([
            'code' => 200,
            'message' => 'ok',
        ], 200);
    }
}
