<?php

namespace Services;

use Mpociot\BotMan\BotManFactory;
use Mpociot\BotMan\BotMan;
use React\EventLoop\Factory;
use React\Promise;
use Slack\ApiClient;
use Slack\Channel;
use Slack\User;

class SlackService
{
    /**
     * @var array
     */
    protected $configs;

    public function __construct($configs)
    {
        $this->configs = $configs;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * @param $loop
     * @return Promise\Promise
     */
    public function getChannels($loop)
    {
        $clients = array_map(function($config) use ($loop) {
            $client = new ApiClient($loop);
            $client->setToken($this->configs[$config]['slack_token']);

            return $client;
        }, ['bigyouth', 'makheia']);
        $channelsPromises = array_reduce($clients, function ($carry, $client) {
            /** @var ApiClient $client */
            $carry[] = $client->getChannels();
            $carry[] = $client->getGroups();

            return $carry;
        }, []);

        return Promise\reduce($channelsPromises, function($carry, $channels) {
            $carry = array_merge($carry, $channels);
            return $carry;
        }, []);
    }
}