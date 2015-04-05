<?php

namespace Darkwood\HearthbreakerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
        /** @var \Darkwood\HearthstonedecksBundle\Services\ScrapperHearthstonedecksService $scrapperService */
        //$scrapperService = $this->getContainer()->get('hb.hearthstonedecks.scrapper');
        //$scrapperService->syncCardList();
        //$scrapperService->syncDeckList();

        /** @var \Darkwood\HearthpwnBundle\Services\ScrapperHearthpwnService $scrapperService */
        $scrapperService = $this->getContainer()->get('hb.hearthpwn.scrapper');
        //$scrapperService->syncCardList();
        $scrapperService->syncDeckList();
    }
}
