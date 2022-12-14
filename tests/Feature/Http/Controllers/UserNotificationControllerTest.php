<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\UserNotifiable;
use App\Models\User;
use App\Models\UserLoginRecord;
use App\Models\UserNotification;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserNotificationControllerTest extends TestCase
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
     * 測試取得所有通知，成功
     *
     * @return void
     */
    public function test_index_user_notitication_success()
    {
        $expected = UserNotification::factory()
            ->count(3)
            ->for(UserLoginRecord::factory(), 'notifiable')
            ->for($this->authenticatedUser)
            ->create()
            ->last();

        $response = $this->getJson('/api/notifications');

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('paginator', fn ($json) => $json->whereAll([
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => 15,
                        'total' => 3,
                    ]))
                    ->has('data', 3, fn ($json) => $json->whereAll([
                        'id' => $expected->id,
                        'content' => $expected->content,
                        'created_at' => $expected->created_at,
                        'notifiable_type' => UserNotifiable::tryFrom($expected->notifiable_type)?->name,
                        'notifiable_id' => $expected->notifiable_id,
                    ]))
                    ->etc()
            );
    }

    /**
     * 測試取得通知，成功
     *
     * @return void
     */
    public function test_show_user_notitication_success()
    {
        $expected = UserNotification::factory()
            ->for(UserLoginRecord::factory(), 'notifiable')
            ->for($this->authenticatedUser)
            ->create();

        $response = $this->getJson("/api/notifications/{$expected->id}");

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'code' => 200,
                'message' => 'ok',
                'result' => [
                    'id' => $expected->id,
                    'content' => $expected->content,
                    'created_at' => $expected->created_at,
                    'notifiable_id' => $expected->notifiable_id,
                    'notifiable_type' => UserNotifiable::tryFrom($expected->notifiable_type)?->name,
                    'notifiable' => $expected->notifiable->toArray(),
                ]
            ]);

        $this->assertModelExists($expected);
    }

    /**
     * 測試取得通知，授權失敗
     *
     * @return void
     */
    public function test_show_user_notitication_unauthenticated()
    {
        $otherUser = User::factory()->create();

        $userNotification = UserNotification::factory()
            ->for(UserLoginRecord::factory(), 'notifiable')
            ->for($otherUser)
            ->create();

        $response = $this->getJson("/api/notifications/{$userNotification->id}");

        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.',
            ]);
    }
}
