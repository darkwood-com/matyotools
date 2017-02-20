<?php
namespace Slack;

use GuzzleHttp;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;
use React\Promise;
use Slack\ApiClient;
use Slack\Channel;
use Slack\ClientObject;
use Slack\Message\Message;
use Slack\Message\MessageBuilder;

class ApiClients
{
    /**
     * @var ApiClient[]
     */
    protected $clients = [];

    public function addClient($client)
    {
        $this->clients[] = $client;
    }

    /**
     * Gets all channels in the team.
     *
     * @return \React\Promise\PromiseInterface
     */
    public function getChannels($expr = null)
    {
        $channelPromises = array_reduce($this->clients, function ($carry, $client) {
            /** @var ApiClient $client */
            $carry[] = $client->getChannels();

            return $carry;
        }, []);

        $groupPromises = array_reduce($this->clients, function ($carry, $client) {
            /** @var ApiClient $client */
            $carry[] = $client->getGroups();

            return $carry;
        }, []);

        $dmPromises = array_reduce($this->clients, function ($carry, $client) {
            /** @var ApiClient $client */
            $carry[] = $client->getDMs();

            return $carry;
        }, []);

        $mdmPromises = array_reduce($this->clients, function ($carry, $client) {
            /** @var ApiClient $client */
            $carry[] = $client->apiCall('mpim.list')->then(function ($response) use ($client) {
                $mdms = [];
                foreach ($response['groups'] as $group) {
                    dump($group);
                    $mdms[] = new MultiDirectMessageChannel($client, $group);
                }
                return $mdms;
            });

            return $carry;
        }, []);

        return Promise\reduce(array_merge($channelPromises, $groupPromises, $dmPromises, $mdmPromises), function ($carry, $channels) {
            return array_merge($carry, $channels);
        }, []);
    }
}