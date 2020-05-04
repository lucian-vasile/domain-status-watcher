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

class DomainsListCommand extends Command
{
    protected static $defaultName = 'domains:list';
    private $em;
    
    /**
     * DomainsListCommand constructor.
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
            ->setDescription('List domains that are watched')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $domains = $this->em->getRepository (Domains::class)->findAll ();

        $headers = [
            'id',
            'domain',
            'current_status',
            'checked_at',
            'exipres_at',
            'is_owned',
        ];
        
        $domArray = [];
        foreach ($domains as $domain) {
            /**
             * @var Domains $domain
             */
            $domArray[] = [
                $domain->getId (),
                $domain->getDomain (),
                substr ($domain->getCurrentStatus (), 0, 40),
                $domain->getCheckedAt () ? $domain->getCheckedAt ()->format ('Y-m-d') : 'null',
                $domain->getExpiresAt () ? $domain->getExpiresAt ()->format ('Y-m-d') : 'null',
                $domain->getIsOwned (),
            ];
        }
        
        $io->table ($headers, $domArray);
        
        //$io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
