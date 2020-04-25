<?php

namespace App\MessageHandler;

use App\Entity\Domains;
use App\Message\NotifyDomainStatus;
use App\Message\VerifyDomain;
use Doctrine\ORM\EntityManagerInterface;
use Novutec\WhoisParser\Parser;
use Novutec\WhoisParser\Result\Result;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Validator\Constraints\Date;

final class VerifyDomainHandler implements MessageHandlerInterface
{
    
    private $logger;
    private $entityManager;
    private $bus;
    
    /**
     * VerifyDomainHandler constructor.
     *
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $entityManager
     * @param MessageBusInterface $bus
     */
    public function __construct (LoggerInterface $logger, EntityManagerInterface $entityManager, MessageBusInterface $bus)
    {
        $this->bus = $bus;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->logger->info ("Got to the VerifyDomainHandler constructor");
    }
    public function __invoke(VerifyDomain $message)
    {
        /**
         * @var Domains $domain;
         */
        $domain = $this->entityManager->getRepository (Domains::class)->find ($message->getDomainId ());
        $this->logger->info ("Domain #{$message->getDomainId ()} fetched: {$domain->getDomain ()}");
    
        $whois = new Parser();
        
        /**
         * @var Result $result
         */
        $result = $whois->lookup ($domain->getDomain ());
        
        $nowDate = new \DateTime();
        $expiresDate = new \DateTime($result->expires);
        
        $domain->setCheckedAt ($nowDate)
            ->setRawWhoisResponse ($result->toArray ())
            ->setCurrentStatus ($result->status);
        
        if ($result->expires) {
            $domain->setExpiresAt ($expiresDate);
        }
        
        $this->entityManager->persist ($domain);
        $this->entityManager->flush ();
        
        if ($result->expires) {
            // expires in the past, soon to be unregistered, check every 5 minutes
            if ($expiresDate <= $nowDate) {
                $this->bus->dispatch (new VerifyDomain($message->getDomainId ()), [
                    new DelayStamp(300000)
                ]);
            }
    
            // expires in the future, check then
            if ($expiresDate > $nowDate) {
                $diff = ($expiresDate->getTimestamp () - $nowDate->getTimestamp ()) * 1000;
                $this->bus->dispatch (new VerifyDomain($message->getDomainId ()), [
                    new DelayStamp($diff)
                ]);
            }
        }
        
        // not registered: send a notification everyday and re-check in 24 hrs
        // @todo: Register the domain
        if (!$result->registered) {
            $this->bus->dispatch (new NotifyDomainStatus("{$domain->getDomain ()} is unregistered!"));
            $this->bus->dispatch (new VerifyDomain($message->getDomainId ()), [
                new DelayStamp(86400000)
            ]);
        }
        
    }
}
