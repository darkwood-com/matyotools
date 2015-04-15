<?php

namespace Darkwood\HearthbreakerBundle\Command;

use Darkwood\HearthbreakerBundle\Events;
use Doctrine\ORM\EntityManager;
use Goutte\Client;
use GuzzleHttp\Event\BeforeEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ScrapperCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('scrapper:run')
            ->setDescription('run scrapper')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, '', 50)
			->addOption('sync', null, InputOption::VALUE_OPTIONAL, '', true)
			->addOption('identify', 'i', InputOption::VALUE_OPTIONAL, '', true)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = intval($input->getOption('limit'));

        $container = $this->getContainer();

        /** @var EntityManager $em */
        $em = $container->get('doctrine.orm.entity_manager');
        $flushHandler = function() use ($em) {
            static $count = 0;

            $count = ($count + 1) % 10;
            if($count == 0) {
                $em->clear();
            }
        };

        /** @var EventDispatcher $dispatcher */
        $dispatcher = $container->get('event_dispatcher');
        $dispatcher->addListener(Events::SYNC_DECK, $flushHandler);

        /** @var Client $client */
        $client = $container->get('hb.client');
        $client->getClient()->getEmitter()->on('before', function (BeforeEvent $event) use ($output) {
            $output->writeln($event->getRequest()->getUrl());
        }, 'last');

		$tasks = array('sync' => true, 'identify' => true);
		if($input->getOption('sync')) {
			$tasks = array('sync' => true);
		} else if($input->getOption('identify')) {
			$tasks = array('sync' => true);
		}

		if(isset($tasks['sync']) && $tasks['sync']) {
			$container->get('hb.hearthstonedecks.scrapper')->sync($limit);
			$container->get('hb.hearthstats.scrapper')->sync($limit);
			$container->get('hb.hearthpwn.scrapper')->sync($limit);
		}

		if(isset($tasks['identify']) && $tasks['identify']) {
			$output->writeln('Identify cards ...');
			$container->get('hb.card')->identify();
		}
    }
}
