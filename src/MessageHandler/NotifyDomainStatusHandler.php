<?php

namespace App\MessageHandler;

use App\Message\NotifyDomainStatus;
use Psr\Log\LoggerInterface;
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
     * @var LoggerInterface $logger
     */
    private $logger;
    /**
     * NotifyDomainStatusHandler constructor.
     *
     * @param NotifierInterface $notifier
     * @param LoggerInterface $logger
     */
    public function __construct (NotifierInterface $notifier, LoggerInterface $logger)
    {
        $this->notifier = $notifier;
        $this->logger = $logger;
    }
    
    public function __invoke(NotifyDomainStatus $message)
    {
        $notification = (new Notification($message->getMessage ()))
            ->importance ('high');
        try {
            $this->notifier->send ($notification);
            $this->logger->info ('Notification sent!', ['message' => $message->getMessage ()]);
        } catch (\Exception $err) {
            $this->logger->error ('Couldn`t send notification.', ['code' => $err->getCode (), 'message' => $err->getMessage ()]);
        }
    }
}
