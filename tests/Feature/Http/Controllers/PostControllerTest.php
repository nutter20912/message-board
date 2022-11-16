<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PostControllerTest extends TestCase
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

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->authenticatedUser = $user;
    }

    /**
     * 測試取得所有文章，成功
     *
     * @return void
     */
    public function test_index_post_success()
    {
        $posts = Post::factory()
            ->count(3)
            ->for($this->authenticatedUser)
            ->create();

        $response = $this->getJson('/api/posts');

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
                        'id' => $posts->last()->id,
                        'title' => $posts->last()->title,
                        'content' => $posts->last()->content,
                        'updated_at' => $posts->last()->updated_at->toJSON(),
                        'created_at' => $posts->last()->created_at->toJSON(),
                        'user' => [
                            'id' => $this->authenticatedUser->id,
                            'name' => $this->authenticatedUser->name,
                        ],
                    ]))
                    ->etc()
            );
    }


    /**
     * 測試新增文章，成功
     *
     * @return void
     */
    public function test_store_post_success()
    {
        $params = [
            'title' => 'title',
            'content' => 'content',
        ];

        $response = $this->postJson('/api/posts', $params);

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->where('code', 200)
                    ->where('message', 'ok')
                    ->where('result.id', 1)
                    ->where('result.title', $params['title'])
                    ->where('result.content', $params['content'])
                    ->has('result.created_at')
                    ->has('result.updated_at')
            );
    }

    /**
     * 測試新增文章，參數錯誤
     *
     * @return void
     */
    public function test_store_post_on_bad_request()
    {
        $response = $this->postJson('/api/posts', []);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 124,
                'message' => 'The title field is required.',
                'result' => null,
            ]);
    }

    /**
     * 測試取得文章，成功
     *
     * @return void
     */
    public function test_show_post_success()
    {
        $post = Post::factory()
            ->for($this->authenticatedUser)
            ->create();

        $response = $this->getJson("/api/posts/{$post->id}");

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'code' => 200,
                'message' => 'ok',
                'result' => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'created_at' => $post->created_at->toJSON(),
                    'updated_at' => $post->updated_at->toJSON(),
                    'user' => [
                        'id' => $post->user->id,
                        'name' => $post->user->name,
                    ],
                ]
            ]);

        $this->assertModelExists($post);
    }

    /**
     * 測試取得文章，資料不存在
     *
     * @return void
     */
    public function test_show_post_error_with_wrong_id()
    {
        $response = $this->getJson('/api/posts/999');

        $response
            ->assertStatus(404)
            ->assertExactJson([
                'code' => 0,
                'message' => 'resource not found',
                'result' => null,
            ]);
    }

    /**
     * 測試更新文章，成功
     *
     * @return void
     */
    public function test_update_post_success()
    {
        $post = Post::factory()
            ->for($this->authenticatedUser)
            ->create();

        $params = [
            'title' => 'title',
            'content' => 'content',
        ];

        $response = $this->putJson("/api/posts/{$post->id}", $params);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'code' => 200,
                'message' => 'ok',
            ]);

        $newPost = Post::find($post->id);

        $this->assertEquals(
            $newPost->toArray(),
            [
                ...$post->toArray(),
                'title' => 'title',
                'content' => 'content',
            ]
        );
    }

    /**
     * 測試更新文章，授權失敗
     *
     * @return void
     */
    public function test_update_post_unauthenticated()
    {
        $otherUser = User::factory()->create();

        $post = Post::factory()
            ->for($otherUser)
            ->create();

        $response = $this->putJson("/api/posts/{$post->id}", []);

        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.',
            ]);
    }

    /**
     * 測試刪除文章，成功
     *
     * @return void
     */
    public function test_destroy_post_success()
    {
        $post = Post::factory()
            ->for($this->authenticatedUser)
            ->create();

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'code' => 200,
                'message' => 'ok',
            ]);

        $this->assertModelMissing($post);
    }

    /**
     * 測試刪除文章，授權失敗
     *
     * @return void
     */
    public function test_destroy_post_unauthenticated()
    {
        $otherUser = User::factory()->create();

        $post = Post::factory()
            ->for($otherUser)
            ->create();

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.',
            ]);

        $this->assertModelExists($post);
    }
}
