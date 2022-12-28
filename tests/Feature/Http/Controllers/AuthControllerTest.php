<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    /**
     * 測試 csrf cookie 成功
     *
     * @return void
     */
    public function test_get_csrf_cookie_success()
    {
        $response = $this->get('/api/auth/csrf-cookie');

        $response->assertStatus(302);
    }

    /**
     * 測試登入成功
     *
     * @return void
     */
    public function test_login_success()
    {
        $user = User::factory()->create([
            'password' => Hash::make('aa11')
        ]);

        $params = [
            'email' => $user->email,
            'password' => 'aa11',
        ];

        $response = $this->postJson('/api/auth/login', $params);

        $response->assertStatus(200)
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

        Event::assertDispatched(\Illuminate\Auth\Events\Login::class, 1);
    }

    /**
     * 測試登入失敗，參數格式錯誤
     *
     * @return void
     */
    public function test_login_error_with_validate_failed()
    {
        $response = $this->postJson('/api/auth/login');

        $response->assertStatus(422)
            ->assertExactJson([
                'code' => 0,
                'message' => 'The email field is required. (and 1 more error)',
                'result' => null,
            ]);

        Event::assertNotDispatched(\Illuminate\Auth\Events\Login::class);
    }

    /**
     * 測試登入失敗，驗證錯誤
     *
     * @return void
     */
    public function test_login_error_with_wrong_credentials()
    {
        $params = [
            'email' => 'aa@aa.com',
            'password' => 'aa11',
        ];

        $response = $this->postJson('/api/auth/login', $params);

        $response->assertStatus(400)
            ->assertExactJson([
                'code' => 10001,
                'message' => 'Login failed',
                'result' => null,
            ]);

        Event::assertNotDispatched(\Illuminate\Auth\Events\Login::class);
    }

    /**
     * 測試登出成功
     *
     * @return void
     */
    public function test_logout_success()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertExactJson([
                'code' => 200,
                'message' => 'ok',
                'result' => null,
            ]);
    }
}
