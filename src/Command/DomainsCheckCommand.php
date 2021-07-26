<?php

namespace App\Command;

use Iodev\Whois\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DomainsCheckCommand extends Command
{
    protected static $defaultName = 'domains:check';
    protected static $defaultDescription = 'Prints useful info about a domain';

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('domain', InputArgument::REQUIRED, 'The domain to check')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $domain = $input->getArgument('domain');

        $whois = Factory::get()->createWhois();
        $info = $whois->loadDomainInfo($domain);

        $io->write($info->toArray());
        return true;
    }
}
