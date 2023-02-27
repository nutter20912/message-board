<?php

namespace App\Services\Logging\Watchers;

use App\Services\Logging\LoggingType;
use App\Services\LoggingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

/**
 * 請求監聽器
 */
class RequestWatcher extends Watcher
{
    /**
     * {@inheritDoc}
     */
    public function register($app)
    {
        $app['events']->listen(RequestHandled::class, [$this, 'handle']);
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(RequestHandled $event)
    {
        LoggingService::record(LoggingType::REQUEST, [
            'ip_address' => $event->request->ip(),
            'userAgent' => $event->request->userAgent(),
            'url' => $event->request->fullUrl(),
            'method' => $event->request->method(),
            'request' => [
                'header' => $event->request->header(),
                'payload' => $this->mask($event->request->all()),
            ],
            'response' => [
                'status' => $event->response->getStatusCode(),
                'body' => $event->response->getContent(),
            ],
        ]);
    }

    /**
     * 隱藏字元
     *
     * @param array $payload
     * @return void
     */
    private function mask($payload)
    {
        return collect($payload)
            ->map(
                fn ($value, $key) => in_array($key, $this->options['hidden'])
                    ? Str::mask($value, '*', 0)
                    : $value
            )
            ->toArray();
    }
}
