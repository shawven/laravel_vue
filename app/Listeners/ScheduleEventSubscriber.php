<?php

/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-06-17 14:08
 */

namespace App\Listeners;

use Illuminate\Events\Dispatcher;

class ScheduleEventSubscriber
{

    /**
     * 为订阅者注册监听器.
     *
     * @param  Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Schedules\CountUserMoneyRecords',
            'App\Listeners\Schedules\CountUserMoneyRecordsListener@count'
        );
    }
}
