<?php

namespace App\Libs\Telescope\Watchers;

use Laravel\Telescope\Watchers\EventWatcher as BaseEventWatcher;
use Illuminate\Support\Str;

class FrameworkEventWatcher extends BaseEventWatcher
{
    /**
     * Register the watcher.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function register($app)
    {
        optional(
            $this->options['events'],
            fn() => $app['events']->listen($this->options['events'], [$this, 'recordEvent'])
        );
    }

    /**
     * Determine if the event should be ignored.
     *
     * @param  string  $eventName
     * @return bool
     */
    protected function shouldIgnore($eventName)
    {
        return !Str::is($this->options['targets'] ?? [], $eventName);
    }
}
