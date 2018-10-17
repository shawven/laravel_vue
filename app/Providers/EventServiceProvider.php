<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ]
    ];

    /**
     * 注册的订阅者类.
     *
     * @var array
     */
    protected $subscribe = [
        'App\Listeners\ScheduleEventSubscriber',
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // 监听QueryExecuted
        if (!App::environment('production')) {
            Event::listen('Illuminate\Database\Events\QueryExecuted', 'App\Listeners\SqlListener');
        }

        //
    }
}
