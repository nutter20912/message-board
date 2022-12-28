<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\PostSeeder;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    /**
     * 已認證使用者
     *
     * @var \App\Models\User $user
     */
    protected User $authenticatedUser;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PostSeeder::class);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->authenticatedUser = $user;
    }

    /**
     * 測試取得所有評論，成功
     *
     * @return void
     */
    public function test_index_comment_success()
    {
        $post = Post::find(1);
        $comments = Comment::factory()
            ->count(3)
            ->for($post)
            ->for($this->authenticatedUser)
            ->create();

        $response = $this->getJson("/api/posts/{$post->id}/comments");

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('paginator', fn ($json) => $json->whereAll([
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => 4,
                        'total' => 3,
                    ]))
                    ->has('data', 3, fn ($json) => $json->whereAll([
                        'id' => $comments->last()->id,
                        'content' => $comments->last()->content,
                        'updated_at' => $comments->last()->updated_at->toJSON(),
                        'created_at' => $comments->last()->created_at->toJSON(),
                        'user' => [
                            'id' => $this->authenticatedUser->id,
                            'name' => $this->authenticatedUser->name,
                        ],
                    ]))
                    ->etc()
            );
    }

    /**
     * 測試新增評論，成功
     *
     * @return void
     */
    public function test_store_comment_success()
    {
        $post = Post::find(1);
        $params = ['content' => 'content'];

        $response = $this->postJson("/api/posts/{$post->id}/comments", $params);

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->where('code', 200)
                    ->where('message', 'ok')
                    ->where('result.id', 1)
                    ->where('result.content', $params['content'])
                    ->has('result.created_at')
                    ->has('result.updated_at')
            );
    }

    /**
     * 測試新增評論，參數錯誤
     *
     * @return void
     */
    public function test_store_comment_on_bad_request()
    {
        $post = Post::find(1);

        $response = $this->postJson("/api/posts/{$post->id}/comments", []);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 10301,
                'message' => 'The content field is required.',
                'result' => null,
            ]);
    }

    /**
     * 測試取得評論，成功
     *
     * @return void
     */
    public function test_show_comment_success()
    {
        $comment = Comment::factory()->create();

        $response = $this->getJson("/api/comments/{$comment->id}");

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'code' => 200,
                'message' => 'ok',
                'result' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at->toJSON(),
                    'updated_at' => $comment->updated_at->toJSON(),
                ]
            ]);

        $this->assertModelExists($comment);
    }

    /**
     * 測試取得評論，資料不存在
     *
     * @return void
     */
    public function test_show_comment_error_with_wrong_id()
    {
        $response = $this->getJson('/api/comments/999');

        $response
            ->assertStatus(404)
            ->assertExactJson([
                'code' => 0,
                'message' => 'resource not found',
                'result' => null,
            ]);
    }

    /**
     * 測試更新評論，成功
     *
     * @return void
     */
    public function test_update_comment_success()
    {
        $comment = Comment::factory()
            ->for($this->authenticatedUser)
            ->create();

        $params = ['content' => 'content'];

        $response = $this->putJson("/api/comments/{$comment->id}", $params);

        $response
        ->assertStatus(200)
        ->assertJson(
            fn (AssertableJson $json) =>
            $json
                ->where('code', 200)
                ->where('message', 'ok')
                ->where('result.id', $comment->id)
                ->where('result.content', $params['content'])
                ->where('result.created_at', $comment->created_at->toJSON())
                ->has('result.updated_at')
        );

        $newComment = Comment::find($comment->id)->toArray();

        $this->assertEquals($newComment['content'], $params['content']);
    }

    /**
     * 測試更新評論，參數錯誤
     *
     * @return void
     */
    public function test_update_comment_on_bad_request()
    {
        $comment = Comment::factory()
            ->for($this->authenticatedUser)
            ->create();

        $response = $this->putJson("/api/comments/{$comment->id}", []);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 10302,
                'message' => 'The content field is required.',
                'result' => null,
            ]);
    }

    /**
     * 測試更新評論，授權失敗
     *
     * @return void
     */
    public function test_update_comment_unauthenticated()
    {
        $otherUser = User::factory()->create();

        $comment = Comment::factory()
            ->for($otherUser)
            ->create();

        $response = $this->putJson("/api/comments/{$comment->id}", ['content' => 'content']);

        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.',
            ]);
    }

    /**
     * 測試刪除評論，成功
     *
     * @return void
     */
    public function test_destroy_comment_success()
    {
        $comment = Comment::factory()
            ->for($this->authenticatedUser)
            ->create();

        $response = $this->deleteJson("/api/comments/{$comment->id}");

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'code' => 200,
                'message' => 'ok',
            ]);

        $this->assertModelMissing($comment);
    }

    /**
     * 測試刪除評論，授權失敗
     *
     * @return void
     */
    public function test_destroy_comment_unauthenticated()
    {
        $otherUser = User::factory()->create();

        $comment = Comment::factory()
            ->for($otherUser)
            ->create();

        $response = $this->deleteJson("/api/posts/{$comment->id}");

        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.',
            ]);

        $this->assertModelExists($comment);
    }
}
