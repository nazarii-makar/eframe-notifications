<?php

namespace EFrame\Notifications\Channels;

use EFrame\Amqp\Amqp;
use Illuminate\Notifications\Notification;
use EFrame\Notifications\Messages\AmqpMessage;
use EFrame\Notifications\Clients\SmsFlyClient;
use EFrame\Notifications\Messages\SmsFlyMessage;

/**
 * Class AmqpChannel
 * @package EFrame\Notifications\Channels
 */
class AmqpChannel
{
    /**
     * @var Amqp
     */
    protected $amqp;

    /**
     * AmqpChannel constructor.
     *
     * @param Amqp $amqp
     */
    public function __construct(Amqp $amqp)
    {
        $this->amqp = $amqp;
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
        if ( ! $to = $notifiable->routeNotificationFor('amqp', $notification)) {
            return;
        }

        $message = $notification->toAmqp($notifiable);

        if (is_string($message)) {
            $message = new AmqpMessage($message);
        }

        return $this->amqp->publish(
            $message->getOption('routing'),
            $message,
            $message->getOptions()
        );
    }
}
