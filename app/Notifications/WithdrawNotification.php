<?php

namespace App\Notifications;

class WithdrawNotification extends BaseJPushNotification
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
                    'withdrawId' => $notifiable->withdraw_id,
                    'withdrawResult' => $notifiable->withdraw_result,
                    'moneyRecordId' => $notifiable->money_record_id
                ],
            ])
            ->androidNotification($this->message, [
                'title' => '提现通知',
                'builder_id' => 2,
                'extras' => [
                    'withdrawId' => $notifiable->withdraw_id,
                    'withdrawResult' => $notifiable->withdraw_result,
                    'moneyRecordId' => $notifiable->money_record_id
                ],
            ])
            ->send();
    }
}
