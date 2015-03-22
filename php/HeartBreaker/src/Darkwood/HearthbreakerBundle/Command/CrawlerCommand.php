<?php

namespace Darkwood\HearthbreakerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CrawlerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('crawler:run')
            ->setDescription('run crawler')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
