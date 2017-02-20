<?php

namespace Command;

use Services\SlackService;
use Slack\ChannelInterface;
use Slack\Group;
use Slack\Message\Message;
use Slack\Message\MessageBuilder;
use Slack\Payload;
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

class ReactCommand extends Command
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
            ->setName('bot:react');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = Factory::create();

        $clients = $this->slackService->getClients($loop, ['makheia', 'symfony-devs']);
        $clients->getChannels()->then(function ($channels) use ($output) {
            /** @var ChannelInterface[] $channels */
            foreach ($channels as $channel) {
                $output->writeln($channel->getId());
            }
        });

        $loop->run();
    }
}