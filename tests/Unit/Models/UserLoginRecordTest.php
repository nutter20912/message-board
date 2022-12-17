<?php

namespace Tests\Unit\Models;

use App\Models\UserLoginRecord;
use App\Models\UserNotification;
use Tests\TestCase;

class UserLoginRecordTest extends TestCase
{
    /**
     * 測試取得登入通知
     *
     * @return void
     */
    public function test_select_notification()
    {
        $userLoginRecord = UserLoginRecord::factory()
            ->hasNotification()
            ->create();

        $userNotification = $userLoginRecord->notification()->sole();

        $this->assertInstanceOf(UserNotification::class, $userNotification);
        $this->assertEquals(UserLoginRecord::class, $userNotification->notifiable_type);
    }
}
