<?php

namespace App\Listeners;

use App\Models\UserLoginRecord;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;

class UserEventSubscriber implements ShouldQueue
{
    /**
     * @var Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create the event handler.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 處理使用者登入監聽器
     *
     * @param Login $event
     */
    public function handleUserLogin(Login $event)
    {
        $userLoginRecord = UserLoginRecord::create([
            'ip' => $this->request->ip(),
            'host' => $this->request->schemeAndHttpHost(),
            'user_agent' => $this->request->userAgent(),
            'request_time' => (new Carbon($this->request->server->get('REQUEST_TIME')))
                ->toDateTimeString(),
        ]);

        $userNotification = new UserNotification();
        $userNotification->content = "新登入活動: {$event->user->email}, IP位址: {$userLoginRecord->ip}";
        $userNotification
            ->user()->associate($event->user)
            ->notifiable()->associate($userLoginRecord)
            ->save();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array
     */
    public function subscribe()
    {
        return [
            Login::class => 'handleUserLogin',
        ];
    }
}
