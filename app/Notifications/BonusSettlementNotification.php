<?php

namespace App\Notifications;

class BonusSettlementNotification extends BaseJPushNotification
{
    public function sendMessage($notifiable)
    {
        return $this->client->push()
            ->setPlatform(['ios', 'android'])
            ->addRegistrationId($notifiable->devicdid)
            ->iosNotification($this->message, [
                'sound' => 'sound.caf',
                'badge' => '+1',
                'content-available' => true,
                'mutable-content' => true,
                'category' => 'jiguang',
                'extras' => [
                    'orderId' => $notifiable->id
                ],
            ])
            ->androidNotification($this->message, [
                'title' => '提现通知',
                'builder_id' => 2,
                'extras' => [
                    'orderId' => $notifiable->id
                ],
            ])
            ->send();
    }
}
