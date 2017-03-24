<?php

namespace Command;

use Services\SlackService;
use Slack\AutoChannel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mpociot\BotMan\BotManFactory;
use Mpociot\BotMan\BotMan;
use React\EventLoop\Factory;
use React\Promise;
use Slack\ApiClient;
use Slack\Channel;
use Slack\User;

class AutoJoinCommand extends Command
{
    /**
     * @var SlackService
     */
    protected $slackService;

    public function __construct(SlackService $slackService)
    {
        parent::__construct();

        $this->slackService = $slackService;
    }

    protected function configure()
    {
        $this
            ->setName('bot:auto-join')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = Factory::create();

        $clients = $this->slackService->getClients($loop);
        $this->slackService
            ->getChannels($clients, null, '/^mpdm-/')
            ->then(function ($channels) {
                /** @var AutoChannel[] $channels */
                foreach ($channels as $channel)
                {
                    $name = $this->slackService->getChannelName($channel);
                    dump($name);

                    $client = $channel->getClient();
                    //$client->apiCall('channels.join', ['name' => $channel->getName()]);
                }

                return $channels;
            })
            ->then(function ($channels) use ($output) {
                /** @var AutoChannel[] $channels */

                $output->writeln("joined " . count($channels) . " channels");
            });
        ;

        $loop->run();
    }
}