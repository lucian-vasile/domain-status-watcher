<?php

namespace App\Command;

use App\Entity\Domains;
use App\Message\VerifyDomain;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class DomainsAddCommand extends Command
{
    protected static $defaultName = 'domains:add';

    private $entityManager;
    private $bus;
    
    public function __construct (EntityManagerInterface $entityManager, MessageBusInterface $bus)
    {
        $this->entityManager = $entityManager;
        $this->bus = $bus;
        parent::__construct ();
    }
    
    protected function configure()
    {
        $this
            ->setDescription('Add a new domain to watch')
            ->addArgument('domain', InputArgument::REQUIRED, 'The domain to add')
            ->addArgument('owned', InputArgument::REQUIRED, 'Mention if the domain is owned')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $domain = $input->getArgument('domain');
        $owned = $input->getArgument('owned');
        
        $domainObject = new Domains();
        $domainObject->setDomain ($domain);
        $domainObject->setIsOwned ($owned);
        $this->entityManager->persist ($domainObject);
        $this->entityManager->flush ();
    
        $this->bus->dispatch (new VerifyDomain($domainObject->getId()));

        $io->success("The domain <$domain> has been successfully added!");

        return 0;
    }
}
