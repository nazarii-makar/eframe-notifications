<?php

namespace EFrame\Notifications\Channels;

use Illuminate\Notifications\Notification;
use EFrame\Notifications\Clients\SmsFlyClient;
use EFrame\Notifications\Messages\SmsFlyMessage;

class SmsFlyChannel
{
    /**
     * The SmsFly client instance.
     *
     * @var \EFrame\Notifications\Clients\SmsFlyClient
     */
    protected $sms_fly;

    /**
     * SmsFlyChannel constructor.
     *
     * @param SmsFlyClient $sms_fly
     */
    public function __construct(SmsFlyClient $sms_fly)
    {
        $this->sms_fly = $sms_fly;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed                                  $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     *
     * @return \EFrame\Notifications\Messages\SmsFlyMessage|void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $to = $notifiable->routeNotificationFor('smsFly', $notification)) {
            return;
        }

        $message = $notification->toSmsFly($notifiable);

        if (is_string($message)) {
            $message = new SmsFlyMessage($message);
        }

        $message->from = $message->from ? $message->from : $this->sms_fly->alfaname();

        return $this->sms_fly->send($message, $to);
    }
}
