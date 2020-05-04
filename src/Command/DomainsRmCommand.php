<?php

namespace App\Command;

use App\Entity\Domains;
use Doctrine\ORM\EntityManagerInterface;
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
     * DomainsRmCommand constructor.
     *
     * @param $em
     */
    public function __construct (EntityManagerInterface $em)
    {
        $this->em = $em;
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
            $dom2del = $this->em->getRepository (Domains::class)->findOneByIdOrDomain($domain);
            if (!$dom2del) {
                $io->writeln ("Domain not found.");
                return 0;
            }
            if ($io->confirm ("Are you sure you want to delete this domain [$domain]?")) {
                $this->em->remove($dom2del);
                $this->em->flush ();
                $io->success ("Domain removed!");
                return  0;
            }
            $io->writeln ('Cancelled.');
        } catch (\Exception $err) {
            $io->error ('Error deleting the domain: ' . $err->getMessage ());
        }

        return 0;
    }
}
