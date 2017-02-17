<?php
namespace Model;

use GuzzleHttp;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use Slack\ApiClient;
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
    public function getChannels()
    {
        return $this->apiCall('channels.list')->then(function ($response) {
            $channels = [];
            foreach ($response['channels'] as $channel) {
                $channels[] = new Channel($this, $channel);
            }
            return $channels;
        });
    }

    /**
     * Gets all groups the authenticated user is a member of.
     *
     * @return \React\Promise\PromiseInterface
     */
    public function getGroups()
    {
        return $this->apiCall('groups.list')->then(function ($response) {
            $groups = [];
            foreach ($response['groups'] as $group) {
                $groups[] = new Group($this, $group);
            }
            return $groups;
        });
    }
}