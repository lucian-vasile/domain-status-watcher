<?php

namespace App\Command;

use App\Entity\Domains;
use App\Message\OrderCountMonitor;
use App\Message\VerifyDomain;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class DomainsWatchCommand extends Command
{
    protected static $defaultName = 'domains:watch';
    
    private $entityManager;
    private $bus;
    private $logger;
    
    /**
     * DomainsWatchCommand constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param MessageBusInterface $bus
     * @param LoggerInterface $logger
     */
    public function __construct (EntityManagerInterface $entityManager, MessageBusInterface $bus, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->bus = $bus;
        $this->logger = $logger;
        
        parent::__construct ();
    }
    
    
    protected function configure()
    {
        $this
            ->setDescription('Start watching domain statuses')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $domains = $this->entityManager->getRepository (Domains::class)->getDomainsToCheck ();
        
        $this->logger->info ('Starting a new domains watch.');
        
        $delay = 0;
        foreach ($domains as $domain) {
            /** @var Domains $domain */
            $this->bus->dispatch (new VerifyDomain($domain->getId()), [
                new DelayStamp($delay)
            ]);
            $this->logger->info ('Domain added to watch.', ['domain' => $domain->getDomain ()]);
            $delay += 15000;
        }

        return 0;
    }
}