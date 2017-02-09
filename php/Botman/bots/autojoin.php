<?php
include '../config.php';

require '../vendor/autoload.php';

use Mpociot\BotMan\BotManFactory;
use Mpociot\BotMan\BotMan;
use React\EventLoop\Factory;
use React\Promise;
use Slack\ApiClient;
use Slack\Channel;
use Slack\User;

$loop = Factory::create();

foreach ($configs as $config)
{
    $client = new ApiClient($loop);
    $client->setToken($config['slack_token']);

    Promise\all([
        $client->getChannels(),
        $client->getGroups(),
    ])->then(function ($data) use ($client) {
        /** @var Channel[] $channels */
        $channels = array_merge($data[0], $data[1]);

        foreach ($channels as $channel)
        {
            $client->apiCall('channels.join', ['name' => $channel->getName()]);
        }
    });
}

$loop->run();