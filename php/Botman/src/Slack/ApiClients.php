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
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
     * @return ApiClient[]
     */
    public function getClients()
    {
        return $this->clients;
    }

    public function getHistories($expr = null)
    {
        /*return $this->getChannels($expr)
            ->then(function ($channels) {

            });
        $promises = array_reduce($this->getChannels($expr), function ($carry, $);
        return Promise\reduce($promises, function ($carry, $messages) {
            return array_merge($carry, $messages);
        }, []);*/
    }
}