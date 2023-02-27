<?php

namespace App\Services\Logging\Watchers;

use App\Services\Logging\LoggingType;
use App\Services\LoggingService;
use Illuminate\Database\Events\QueryExecuted;

/**
 * sql監聽器
 */
class QueryWatcher extends Watcher
{
    /**
     * {@inheritDoc}
     */
    public function register($app)
    {
        $app['events']->listen(QueryExecuted::class, [$this, 'handle']);
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {

        if ($this->isIgnoredConnection($event->connectionName)) {
            return;
        }

        LoggingService::record(LoggingType::QUERY, [
            'sql' => $event->sql,
            'bindings' => $event->bindings,
            'time' => $event->time,
            'connectionName' => $event->connectionName,
        ]);
    }

    /**
     * 是否忽略連線
     *
     * @param string $connectionName
     * @return boolean
     */
    public function isIgnoredConnection($connectionName)
    {
        return in_array($connectionName, $this->options['ignored_connection']);
    }
}
