<?php

namespace Tests\Unit\Listeners;

use App\Listeners\UserEventSubscriber;
use App\Models\User;
use App\Models\UserLoginRecord;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\ServerBag;
use Tests\TestCase;

class UserEventSubscriberTest extends TestCase
{
    /**
     * 測試登入監聽器成功
     *
     * @return void
     */
    public function test_subscribe_login_success()
    {
        $expected = [
            'ip' => '127.0.0.1',
            'host' => 'http://localhost',
            'user_agent' => 'PostmanRuntime/7.30.0',
            'request_time' => '2000-09-20 16:04:24',
        ];

        $requestMock = Mockery::mock(Request::class, function (MockInterface $mock) use ($expected) {
            $mock->shouldReceive('ip')->once()->andReturn($expected['ip']);
            $mock->shouldReceive('schemeAndHttpHost')->once()->andReturn($expected['host']);
            $mock->shouldReceive('userAgent')->once()->andReturn($expected['user_agent']);
            $mock->server = Mockery::mock(ServerBag::class, function (MockInterface $mock) use ($expected) {
                $mock->shouldReceive('get')
                    ->with('REQUEST_TIME')
                    ->andReturn(Carbon::createFromTimeString($expected['request_time'])->timestamp);
            });
        });

        $user = User::factory()->create();
        $loginEventMock = Mockery::mock(
            Login::class,
            fn (MockInterface $mock) => $mock->user = $user
        );


        (new UserEventSubscriber($requestMock))->handleUserLogin($loginEventMock);

        $userLoginRecord = UserLoginRecord::first();
        $this->assertEquals($userLoginRecord->toArray(), ['id' => 1, ...$expected]);

        $this->assertDatabaseHas(UserNotification::class, [
            'notifiable_id' => $userLoginRecord->id,
            'notifiable_type' => UserLoginRecord::class,
            'user_id' => $user->id,
            'content' => "新登入活動: {$user->email}, IP位址: {$userLoginRecord->ip}",
        ]);
    }
}
