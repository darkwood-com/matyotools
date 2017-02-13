<?php

namespace Command;

use Services\SlackService;
use Slack\Group;
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

class EmojiCommand extends Command
{
    /**
     * @var SlackService
     */
    protected $slackService;

    public function __construct($slackService)
    {
        parent::__construct();

        $this->slackService = $slackService;
    }

    protected function configure()
    {
        $this
            ->setName('bot:emoji');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = Factory::create();

        $this->slackService
            ->getChannels($loop)
            ->then(function ($channels) {
                $messagesPromise = array_map(function ($channel) {
                    /** @var Channel|Group $channel */
                    $client = $channel->getClient();

                    $method = 'channels.history';
                    if($channel instanceof Group) {
                        $method = 'groups.history';
                    }

                    return $client->apiCall($method, [
                        'channel' => $channel->getName(),
                    ]);
                }, $channels);

                return Promise\all($messagesPromise);
            })->then(function ($messages) {
                /** @var Channel[] $channels */

                dump($messages);
            }, function ($a) {
                dump($a);
            });

        $loop->run();
    }
}