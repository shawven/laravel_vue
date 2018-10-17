<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-26 14:46
 */
namespace App\Channels;

use Illuminate\Notifications\Notification;

class JPushChannel
{
    /**
     * 发送给定通知到极光推送.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $notification->toJPush($notifiable);
    }
}
