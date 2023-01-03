<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Database\Seeders\UserRelationshipSeeder;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserRelationshipControllerTest extends TestCase
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

        $this->seed(UserRelationshipSeeder::class);

        $user = User::find(1);
        $this->actingAs($user);
        $this->authenticatedUser = $user;
    }

    /**
     * 測試取得所有關係成功
     *
     * @return void
     */
    public function test_index_success()
    {
        $children = $this->authenticatedUser
            ->children()
            ->get();

        $response = $this->getJson('/api/relationship');

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('paginator', fn ($json) => $json->whereAll([
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => 15,
                        'total' => 2,
                    ]))
                    ->has('data', $children->count(), fn ($json) => $json->whereAll([
                        'id' => $children->last()->id,
                        'name' => $children->last()->name,
                        'email' => $children->last()->email,
                        'relationship' => $children->last()->relationship->toArray(),
                    ]))
                    ->etc()
            );
    }

    /**
     * 測試取得所有關係成功，取得所有好友關係
     *
     * @return void
     */
    public function test_index_success_with_type()
    {
        $children = $this->authenticatedUser
            ->children()
            ->wherePivot('type', 1)
            ->get();

        $params = ['type' => 1];

        $response = $this->json('GET', '/api/relationship', $params);

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('paginator', fn ($json) => $json->whereAll([
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => 15,
                        'total' => 1,
                    ]))
                    ->has('data', $children->count(), fn ($json) => $json->whereAll([
                        'id' => $children->last()->id,
                        'name' => $children->last()->name,
                        'email' => $children->last()->email,
                        'relationship' => $children->last()->relationship->toArray(),
                    ]))
                    ->etc()
            );
    }

    /**
     * 測試取得所有關係失敗，類型錯誤
     *
     * @return void
     */
    public function test_index_error_with_wrong_type()
    {
        $params = ['type' => 999];

        $response = $this->json('GET', '/api/relationship', $params);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 10407,
                'message' => 'User relationship type is wrong.',
                'result' => null,
            ]);
    }

    /**
     * 測試新增關係成功
     *
     * @return void
     */
    public function test_store_success()
    {
        $child = User::factory()->create();

        $params = ['child_id' => $child->id];

        $response = $this->postJson('/api/relationship', $params);

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->where('code', 200)
                    ->where('message', 'ok')
            );

        $this->authenticatedUser
            ->children()
            ->wherePivot('child_id', $child->id)
            ->firstOrFail();
    }

    /**
     * 測試新增關係失敗，不能加自己
     *
     * @return void
     */
    public function test_store_error_with_self()
    {
        $params = ['child_id' => $this->authenticatedUser->id];

        $response = $this->postJson('/api/relationship', $params);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 10408,
                'message' => 'Can not request self.',
                'result' => null,
            ]);
    }

    /**
     * 測試新增關係失敗，關係已存在
     *
     * @return void
     */
    public function test_store_error_with_user_relationship_existed()
    {
        $child = $this->authenticatedUser
            ->children()
            ->first();

        $params = ['child_id' => $child->id];

        $response = $this->postJson('/api/relationship', $params);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 10401,
                'message' => 'Already requested.',
                'result' => null,
            ]);
    }

    /**
     * 測試新增關係失敗，使用者資料不存在
     *
     * @return void
     */
    public function test_store_error_with_user_not_found()
    {
        $params = ['child_id' => 9999];

        $response = $this->postJson('/api/relationship', $params);

        $response
            ->assertStatus(404)
            ->assertExactJson([
                'code' => 10402,
                'message' => 'Child not found.',
                'result' => null,
            ]);
    }

    /**
     * 測試取得好友成功
     *
     * @return void
     */
    public function test_show_friend_success()
    {
        $child = $this->authenticatedUser
            ->children()
            ->first();

        $response = $this->getJson("/api/relationship/{$child->id}");

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'code' => 200,
                'message' => 'ok',
                'result' => [
                    'id' => $child->id,
                    'name' => $child->name,
                    'email' => $child->email,
                    'relationship' => $child->relationship->toArray(),
                ]
            ]);
    }

    /**
     * 測試更新關係成功
     *
     * @return void
     */
    public function test_update_success()
    {
        $child = $this->authenticatedUser
            ->children()
            ->wherePivot('type', 0)
            ->first();

        $params = ['type' => 1];

        $response = $this->putJson("/api/relationship/{$child->id}", $params);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'code' => 200,
                'message' => 'ok',
            ]);

        $this->authenticatedUser
            ->children()
            ->wherePivot('child_id', $child->id)
            ->wherePivot('type', 1)
            ->firstOrFail();
    }

    /**
     * 測試更新關係失敗，類型錯誤
     *
     * @return void
     */
    public function test_update_error_with_wrong_type()
    {
        $child = $this->authenticatedUser
            ->children()
            ->first();

        $params = ['type' => 9999];

        $response = $this->putJson("/api/relationship/{$child->id}", $params);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 10404,
                'message' => 'User relationship type is wrong.',
                'result' => null,
            ]);
    }

    /**
     * 測試刪除關係成功
     *
     * @return void
     */
    public function test_destroy_success()
    {
        $child = $this->authenticatedUser
            ->children()
            ->first();

        $response = $this->deleteJson("/api/relationship/{$child->id}");

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'code' => 200,
                'message' => 'ok',
            ]);

        $this->assertModelMissing($child->relationship);
    }
}
