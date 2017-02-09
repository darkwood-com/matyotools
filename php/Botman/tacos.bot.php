<?php
include 'config.php';

require 'vendor/autoload.php';

use Mpociot\BotMan\BotManFactory;
use Mpociot\BotMan\BotMan;
use React\EventLoop\Factory;
use React\Promise;
use Slack\ApiClient;
use Slack\Channel;
use Slack\User;

$loop = Factory::create();
//$botman = BotManFactory::createForRTM($config, $loop);

$client = new ApiClient($loop);
$client->setToken($config['slack_token']);

Promise\all([
    $client->getAuthedUser(),
    $client->getGroupByName('big-youth'),
])->then(function ($data) use ($client) {
    /** @var User $user */
    $authedUser = $data[0];
    /** @var Channel $channel */
    $channel = $data[1];

    $channel->getMembers()
        ->then(function ($members) use($authedUser) {
            /** @var User[] $members */

            //shuffle members without me
            shuffle($members);
            $members = array_filter($members, function (User $user) use($authedUser) {
                return $user->getId() != $authedUser->getId();
            });

            return $members;
        })->then(function ($members) {
            /** @var User[] $members */
            return Promise\map($members, function ($member) {
                /** @var User $member */
                return $member->getPresence()->then(function($presence) use ($member) {
                    return [$member, $presence];
                });
            })->then(function ($members) {
                $members = array_filter($members, function($data) {
                    return $data[1] == 'active';
                });
                $members = array_map(function ($data) {
                    return $data[0];
                }, $members);

                return $members;
            });
        })->then(function ($members) use($client, $channel) {
            /** @var User[] $members */

            //get 5 members
            $members = array_slice($members, 0, 5);
            foreach ($members as $member)
            {
                $client->send("<@{$member->getId()}|{$member->getUsername()}> :taco:", $channel);
            }
        });
});

$loop->run();