<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * 測試新增使用者成功
     *
     * @return void
     */
    public function test_store_user_success()
    {
        $params = [
            'name' => 'paul',
            'email' => 'paul@gmail.com',
            'password' => 'aa11',
        ];

        $response = $this->postJson('/api/users', $params);

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->where('code', 200)
                    ->where('message', 'ok')
                    ->where('result.id', 1)
                    ->where('result.name', $params['name'])
                    ->where('result.email', $params['email'])
                    ->has('result.created_at')
                    ->has('result.updated_at')
            );
    }

    /**
     * 測試新增使用者參數錯誤
     *
     * @return void
     */
    public function test_store_user_on_bad_request()
    {
        $params = [
            'name' => 'paul',
            'email' => 'paul',
            'password' => 'aa11',
        ];

        $response = $this->postJson('/api/users', $params);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 10101,
                'message' => 'The email must be a valid email address.',
                'result' => null,
            ]);
    }

    /**
     * 測試查詢使用者成功
     *
     * @return void
     */
    public function test_show_user_success()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/users/{$user->id}");

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'code' => 200,
                'message' => 'ok',
                'result' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->toJSON(),
                    'updated_at' => $user->updated_at->toJSON(),
                ]
            ]);

        $this->assertModelExists($user);
    }

    /**
     * 測試查詢使用者成功，查詢關係
     *
     * @return void
     */
    public function test_show_success_with_relationship()
    {
        /** @var \App\Models\User $owner */
        $owner = User::factory()->create();

        $user = User::factory()
            ->hasAttached($owner, ['type' => 0], 'owners')
            ->create();

        $expectedRelationship = $user
            ->owners()
            ->get()
            ->first()
            ->toArray()['relationship'];

        $response = $this->actingAs($owner)->json('GET', "/api/users/{$user->id}", ['relationship' => true]);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'code' => 200,
                'message' => 'ok',
                'result' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->toJSON(),
                    'updated_at' => $user->updated_at->toJSON(),
                    'relationship' => $expectedRelationship,
                ]
            ]);

        $this->assertModelExists($user);
    }

    /**
     * 測試查詢使用者成功，查詢關係且關係不存在
     *
     * @return void
     */
    public function test_show_success_with_unexist_relationship()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->json('GET', "/api/users/{$user->id}", ['relationship' => true]);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'code' => 200,
                'message' => 'ok',
                'result' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->toJSON(),
                    'updated_at' => $user->updated_at->toJSON(),
                    'relationship' => null,
                ]
            ]);

        $this->assertModelExists($user);
    }

    /**
     * 測試查詢使用者不存在
     *
     * @return void
     */
    public function test_show_user_error_with_wrong_id()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/users/999");

        $response
            ->assertStatus(404)
            ->assertExactJson([
                'code' => 0,
                'message' => 'resource not found',
                'result' => null,
            ]);
    }
}
