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

/**
 * Class VerifyDomainHandler
 *
 * @package App\MessageHandler
 */
final class VerifyDomainHandler implements MessageHandlerInterface
{
    
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var MessageBusInterface
     */
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
    
    /**
     * @param VerifyDomain $message
     *
     * @throws \Novutec\WhoisParser\Exception\NoQueryException
     */
    public function __invoke(VerifyDomain $message)
    {
        /**
         * @var Domains $domain;
         */
        $domain = $this->entityManager->getRepository (Domains::class)->find ($message->getDomainId ());
        if (!$domain) {
            $this->logger->info ("Non existent domain. Probably deleted. Not watching anymore.", ['id' => $message->getDomainId ()]);
            $this->bus->dispatch (new NotifyDomainStatus("Domain #{$message->getDomainId ()} has been deleted. Not watching"));
            return;
        }
    
        $whois = new Parser();
        
        try {
            /**
             * @var Result $result
             */
            $result = $whois->lookup ($domain->getDomain ());
        } catch (\Exception $er) {
            // if this fails recheck in about an hour
            $this->logger->error ('Error looking up domain.', ['domain' => $domain->getDomain (), 'error' => $er->getMessage ()]);
            $this->bus->dispatch (new VerifyDomain($message->getDomainId ()), [
                new DelayStamp(rand (1800000, 3600000))
            ]);
            return;
        }
        $this->logger->info ("Domain fetched.", ['domain' => $domain->getDomain ()]);
        
        $nowDate = new \DateTime();
        $expiresDate = new \DateTime($result->expires);
        
        $status = $result->status;
        if (is_array ($result->status)) {
            $status = implode (',', $result->status);
        }
        
        $domain->setCheckedAt ($nowDate)
            ->setRawWhoisResponse ($result->toArray ())
            ->setCurrentStatus ($status);
        
        if ($result->expires) {
            $domain->setExpiresAt ($expiresDate);
        }
        
        $this->entityManager->persist ($domain);
        $this->entityManager->flush ();
        
        if ($result->expires) {
            // expires in the past, soon to be unregistered, check every 5 minutes
            if ($expiresDate <= $nowDate) {
                $this->bus->dispatch (new VerifyDomain($message->getDomainId ()), [
                    new DelayStamp(rand (100000, 300000))
                ]);
                $this->logger->info ("Expires in the past but still registered. Checking every few minutes.", ['domain' => $domain->getDomain ()]);
                return;
            }
    
            // expires in the future, check then
            if ($expiresDate > $nowDate) {
                $diff = ($expiresDate->getTimestamp () - $nowDate->getTimestamp ()) * 1000;
                $this->bus->dispatch (new VerifyDomain($message->getDomainId ()), [
                    new DelayStamp($diff)
                ]);
                $this->logger->info ("Expires in the future. Setting new check date.", ['domain' => $domain->getDomain (), 'check-date' => $expiresDate->format ('Y-m-d')]);
                return;
            }
        }
        
        // not registered: send a notification everyday and re-check in 24 hrs
        // @todo: Register the domain
        if (!$result->registered) {
            $this->bus->dispatch (new NotifyDomainStatus("{$domain->getDomain ()} is unregistered!"));
            $this->bus->dispatch (new VerifyDomain($message->getDomainId ()), [
                new DelayStamp(86400000)
            ]);
            $this->logger->info ('This domain is not registered. Re-check every 24 hours.', ['domain' => $domain->getDomain ()]);
            return;
        }
        
        // if all other checks fail recheck in one hour
        $this->bus->dispatch (new VerifyDomain($message->getDomainId ()), [
            new DelayStamp(3600000)
        ]);
        $this->logger->warning ('Something went wrong and non of the conditions were met. Re-checking in an hour.', ['domain' => $domain->getDomain ()]);
        
    }
}
