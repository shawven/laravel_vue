<?php

namespace App\Notifications;

use App\Channels\JPushChannel;
use App\Exceptions\BizException;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use JPush\Client;

abstract class BaseJPushNotification extends Notification
{
    use Queueable;

    /**
     * @var string
     */
    public $message;

    /**
     * @var Client
     */
    public $client;

    /**
     * Create a new notification instance.
     *
     * @param string $message
     */
    public function __construct($message = '')
    {
        $this->message = $message;
        $this->client = $this->getClient();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return [JPushChannel::class];
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return new Client(config('jpush.appKey'), config('jpush.masterSecret'), config('jpush.logFile'));
    }

    /**
     * @param mixed $notifiable
     * @return array
     */
    public function toJPush($notifiable)
    {
        try {
            return $this->sendMessage($notifiable);
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            throw new BizException($e->getMessage(), $e);
        } catch (\JPush\Exceptions\APIRequestException $e) {
            throw new BizException($e->getMessage(), $e);
        }
    }

    /**
     * @param $notifiable
     * @return mixed
     */
    public abstract function sendMessage($notifiable);
}
