<?php

namespace Darkwood\HearthbreakerBundle\Command;

use Goutte\Client;
use GuzzleHttp\Event\BeforeEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ScrapperCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('scrapper:run')
            ->setDescription('run scrapper')
			->addOption('limit', null, InputOption::VALUE_OPTIONAL, '', 50)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = intval($input->getOption('limit'));

        /** @var Client $client */
        $client = $this->getContainer()->get('hb.client');
        $client->getClient()->getEmitter()->on('before', function(BeforeEvent $event) use ($output) {
            $output->writeln($event->getRequest()->getUrl());
        }, 'last');

        $this->getContainer()->get('hb.hearthstonedecks.scrapper')->sync($limit);
        $this->getContainer()->get('hb.hearthpwn.scrapper')->sync($limit);
        $this->getContainer()->get('hb.card')->identify();
    }
}
