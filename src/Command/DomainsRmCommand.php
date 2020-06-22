<?php

namespace App\Command;

use App\Entity\Domains;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DomainsRmCommand extends Command
{
    protected static $defaultName = 'domains:rm';
    private $em;
    
    /**
     * @var LoggerInterface $logger
     */
    private $logger;
    
    /**
     * DomainsRmCommand constructor.
     *
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     */
    public function __construct (EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
        parent::__construct ();
    }
    
    protected function configure()
    {
        $this
            ->setDescription('Remove a domain')
            ->addArgument('domain', InputArgument::REQUIRED, 'The id or the domain to remove')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $domain = $input->getArgument('domain');
        
        try {
            /**
             * @var Domains $dom2del
             */
            $dom2del = $this->em->getRepository (Domains::class)->findOneByIdOrDomain($domain);
            if (!$dom2del) {
                $io->writeln ("Domain not found.");
                $this->logger->warning ('Tried to remove non existent domain.', ['domain' => $domain]);
                return 0;
            }
            if ($io->confirm ("Are you sure you want to delete this domain [{$dom2del->getDomain ()}}]?")) {
                $this->em->remove($dom2del);
                $this->em->flush ();
                $io->success ("Domain removed!");
                $this->logger->info ('Domain removed from database.', ['domain' => $dom2del->getDomain ()]);
                return  0;
            }
            $io->writeln ('Cancelled.');
        } catch (\Exception $err) {
            $io->error ('Error deleting the domain: ' . $err->getMessage ());
            $this->logger->error ('Error deleting domain from the database.', ['domain' => $dom2del->getDomain ()]);
        }

        return 0;
    }
}
