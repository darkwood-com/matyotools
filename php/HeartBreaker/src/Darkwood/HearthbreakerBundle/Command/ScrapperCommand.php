<?php

namespace Darkwood\HearthbreakerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScrapperCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('scrapper:run')
            ->setDescription('run scrapper')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Darkwood\HearthbreakerBundle\Services\ScrapperService $scrapperService */
        $scrapperService = $this->getContainer()->get('hb.scrapper');
        $scrapperService->getCardList();
    }
}