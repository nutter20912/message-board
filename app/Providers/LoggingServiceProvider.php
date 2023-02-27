<?php

namespace App\Providers;

use App\Services\LoggingService;
use Illuminate\Support\ServiceProvider;

class LoggingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerWatchers();

        $this->app->terminating(
            fn() => LoggingService::batchStore()
        );
    }

    /**
     * 註冊監聽器
     *
     * @return void
     */
    public function registerWatchers()
    {
        $watchers = config('logging.watchers');

        foreach ($watchers as $watcherClass => $config) {
            if ($config['enabled'] === false) {
                continue;
            }

            $watcher = $this->app->make($watcherClass, ['options' => $config]);
            $watcher->register($this->app);
        }
    }
}
