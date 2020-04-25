<?php

namespace App\MessageHandler;

use App\Message\NotifyDomainStatus;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;

final class NotifyDomainStatusHandler implements MessageHandlerInterface
{
    /**
     * @var NotifierInterface $notifier
     */
    private $notifier;
    /**
     * NotifyDomainStatusHandler constructor.
     *
     * @param NotifierInterface $notifier
     */
    public function __construct (NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }
    
    public function __invoke(NotifyDomainStatus $message)
    {
        $notification = (new Notification($message->getMessage ()))
            ->importance ('high');
        $this->notifier->send ($notification);
    }
}
