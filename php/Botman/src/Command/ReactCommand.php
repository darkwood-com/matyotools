<?php

namespace Command;

use Services\SlackService;
use Slack\AutoChannel;
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

        $clients = $this->slackService->getClients($loop, 'bigyouth');

        $this->slackService
            ->getChannels($clients, 'big-youth')
            ->then(function ($channels) {
                return Promise\map($channels, function ($channel) {
                    /** @var AutoChannel $channel */
                    return Promise\all(array(
                        $channel,
                        $channel->getClient()->getAuthedUser(),
                        Promise\map($channel->getMembers(), function ($member) {
                            /** @var User $member */
                            return Promise\all(array(
                                $member,
                                $member->getPresence(),
                            ));
                        }),
                    ));
                });
            })
            ->then(function ($datas) {
                $channelUsers = array();

                foreach ($datas as $data) {
                    /** @var User $authedUser */
                    /** @var Channel $channel */
                    /** @var User[] $members */
                    list($channel, $authedUser, $members) = $data;

                    /*$message = array_map(function (User $user) {
                        return "<@{$user->getId()}|{$user->getUsername()}>";
                    }, $users);
                    $message[] = ':taco:';
                    $message = implode(" ", $message);

                    $client = $channel->getClient();
                    $client->send($message, $channel);

                    foreach ($users as $user) {
                        $output->writeln('tacos sent to ' . $user->getUsername());
                    }*/
                }
            });

        $loop->run();
    }
}