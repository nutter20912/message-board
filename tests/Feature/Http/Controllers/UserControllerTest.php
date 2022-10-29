<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

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
                'code' => 123,
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
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->where('code', 200)
                    ->where('message', 'ok')
                    ->where('result.id', $user->id)
                    ->where('result.name', $user->name)
                    ->where('result.email', $user->email)
                    ->where('result.created_at', $user->created_at->toJSON())
                    ->where('result.updated_at', $user->updated_at->toJSON())
            );

        $this->assertModelExists($user);
    }
}
