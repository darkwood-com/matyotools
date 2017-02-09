<?php
include dirname(__FILE__) . '/../config.php';
require dirname(__FILE__) . '/../vendor/autoload.php';

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

        $channels = array_filter($channels, function($channel) {
            /** @var Channel $channel */
            return strpos($channel->getName(), 'mpdm-') === false;
        });

        foreach ($channels as $channel)
        {
            $client->apiCall('channels.join', ['name' => $channel->getName()]);
        }
    });
}

$loop->run();