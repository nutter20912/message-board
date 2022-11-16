<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CommentController extends Controller
{
    /**
     * 取得文章全部評論
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    #[OA\Get(
        path: '/posts/{postId}/comments',
        description: '取得文章全部評論',
        tags: ['comments'],
        operationId: 'comments.index',
        parameters: [
            new OA\Parameter(
                name: 'postId',
                in: 'path',
                description: '文章編號',
                required: 'true',
                schema: new OA\Schema(type: 'integer')
            ),
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
                    new OA\Property(property: 'result', type: CommentCollection::class),
                ]),
            ],
        )
    )]
    public function index(Request $request, Post $post)
    {
        $page = $request->input('page', 1);

        $comments = $post->comments()
            ->with('user')
            ->orderby('id', 'desc')
            ->paginate(perPage: 4, page: $page);

        return (new CommentCollection($comments))->resolve();
    }

    /**
     * 新增評論
     *
     * @param  \App\Http\Requests\StoreCommentRequest  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    #[OA\Post(
        path: '/posts/{postId}/comments',
        description: '新增評論',
        tags: ['comments'],
        operationId: 'comments.store',
        parameters: [
            new OA\Parameter(
                name: 'postId',
                in: 'path',
                description: '文章編號',
                required: 'true',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'content', type: 'string', description: '內容', maxLength: 255),
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
                    new OA\Property(property: 'result', type: CommentResource::class),
                ]),
            ],
        )
    )]
    #[OA\Response(response: 400, description: 'Bad request')]
    public function store(StoreCommentRequest $request, Post $post)
    {
        $content = $request->input('content');
        $user = $request->user();

        $comment = new Comment();
        $comment->content = $content;
        $comment
            ->post()->associate($post)
            ->user()->associate($user)
            ->save();

        return response()->json([
            'code' => 200,
            'message' => 'ok',
            'result' => new CommentResource($comment),
        ], 200);
    }

    /**
     * 取得評論
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    #[OA\Get(
        path: '/comments/{id}',
        description: '取得評論',
        tags: ['comments'],
        operationId: 'comments.show',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: '評論編號',
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
                    new OA\Property(property: 'result', type: CommentResource::class),
                ]),
            ],
        )
    )]
    #[OA\Response(response: 400, description: 'Bad request')]
    public function show(Comment $comment)
    {
        return response()->json([
            'code' => 200,
            'message' => 'ok',
            'result' => new CommentResource($comment),
        ], 200);
    }

    /**
     * 更新評論
     *
     * @param  \App\Http\Requests\UpdateCommentRequest  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    #[OA\Put(
        path: '/comments/{id}',
        description: '更新評論',
        tags: ['comments'],
        operationId: 'comments.update',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: '評論編號',
                required: 'true',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['content'],
                properties: [
                    new OA\Property(property: 'content', type: 'string', description: '內容', maxLength: 255),
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
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $content = $request->input('content');

        $this->authorize('update', $comment);

        $comment->update(['content' => $content]);

        return response()->json([
            'code' => 200,
            'message' => 'ok',
            'result' => new CommentResource($comment),
        ], 200);
    }

    /**
     * 刪除評論
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    #[OA\Delete(
        path: '/comments/{id}',
        description: '刪除評論',
        tags: ['comments'],
        operationId: 'comments.destroy',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: '評論編號',
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
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json([
            'code' => 200,
            'message' => 'ok',
        ], 200);
    }
}
