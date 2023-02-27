<?php

namespace App\Services\Logging\Watchers;

/**
 * 監聽器
 */
abstract class Watcher
{
    /**
     * The configured watcher options.
     *
     * @var array
     */
    public $options = [];

    /**
     * Create a new watcher instance.
     *
     * @param  array  $options
     * @return void
     */
    public function __construct($options = [])
    {
        $this->options = $options;
    }

    /**
     * 註冊監聽器
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public abstract function register($app);
}