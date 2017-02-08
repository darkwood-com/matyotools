<?php
include 'config.php';

require 'vendor/autoload.php';

use Mpociot\BotMan\BotManFactory;
use Mpociot\BotMan\BotMan;
use React\EventLoop\Factory;
use Slack\ApiClient;
use Slack\Channel;
use Slack\User;

$loop = Factory::create();
//$botman = BotManFactory::createForRTM($config, $loop);

$client = new ApiClient($loop);
$client->setToken($config['slack_token']);
$client->getChannelByName('ricard_inte')->then(function($channel) use ($client) {
    /** @var Channel $channel */
    $channel->getMembers()->then(function($members) use ($client, $channel) {
        /** @var User[] $members */

        //get 5 members
        shuffle($members);
        $members = array_slice($members, 0, 5);
        foreach ($members as $member)
        {
            $client->send("@{$member->getUsername()} :taco:", $channel);
        }
    });
});

$loop->run();