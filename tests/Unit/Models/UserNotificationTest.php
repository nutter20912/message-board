<?php

namespace Tests\Unit\Models;

use App\Models\UserLoginRecord;
use App\Models\UserNotification;
use Tests\TestCase;

class UserNotificationTest extends TestCase
{
    /**
     * 測試取得可通知模型
     *
     * @return void
     */
    public function test_select_notifiable()
    {
        $userNotification = UserNotification::factory()
            ->for(UserLoginRecord::factory(), 'notifiable')
            ->create();

        $notifiable = $userNotification->notifiable;

        $this->assertInstanceOf(UserLoginRecord::class, $notifiable);
        $this->assertEquals(UserLoginRecord::class, $userNotification->notifiable_type);
    }
}
