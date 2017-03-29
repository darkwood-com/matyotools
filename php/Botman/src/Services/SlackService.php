<?php

namespace Services;

use Doctrine\Common\Cache\Cache;
use Mpociot\BotMan\BotManFactory;
use Mpociot\BotMan\BotMan;
use React\EventLoop\Factory;
use React\Promise;
use Slack\ApiClients;
use Slack\ApiClient;
use Slack\AutoChannel;
use Slack\Channel;
use Slack\DirectMessageChannel;
use Slack\Group;
use Slack\MultiDirectMessageChannel;
use Slack\User;

class SlackService
{
    /**
     * @var array
     */
    protected $configs;

    /**
     * @var Cache
     */
    protected $cache;

    public function __construct($configs, $cache)
    {
        $this->configs = $configs;
        $this->cache = $cache;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    public function getClients($loop, $configs = array())
    {
        $clients = new ApiClients();

        if (!is_array($configs)) {
            $configs = array($configs);
        } elseif (count($configs) == 0) {
            $configs = array_keys($this->configs);
        }

        foreach ($configs as $config) {
            $client = new ApiClient($loop);
            $client->setToken($this->configs[$config]['slack_token']);

            $clients->addClient($client);
        }

        return $clients;
    }

    /**
     * Gets all channels in the team.
     *
     * @param ApiClients $apiClient
     * @param null $inExpr
     * @param null $notInExpr
     * @return Promise\PromiseInterface
     */
    public function getChannels(ApiClients $apiClient, $inExpr = null, $notInExpr = null)
    {
        $promises = array_reduce($apiClient->getClients(), function ($carry, $client) {
            /** @var ApiClient $client */
            $carry[] = $client->getChannels();
            $carry[] = $client->getGroups();
            $carry[] = $client->getDMs();
            $carry[] = $client->apiCall('mpim.list')->then(function ($response) use ($client) {
                $mdms = [];
                foreach ($response['groups'] as $group) {
                    $mdms[] = new MultiDirectMessageChannel($client, $group);
                }
                return $mdms;
            });

            return $carry;
        }, []);

        return Promise\reduce($promises, function ($carry, $channels) {
            $channels = array_map(function ($channel) {
                return new AutoChannel($channel);
            }, $channels);

            return array_merge($carry, $channels);
        }, [])->then(function ($channels) use ($inExpr, $notInExpr) {
            $promise = Promise\resolve($channels);

            $match = function ($expr, $value) {
                if (!is_array($value)) {
                    $value = array($value);
                }

                foreach ($value as $v) {
                    $isRegex = preg_match("/^\/[\s\S]+\/$/", $expr);
                    if (($isRegex && preg_match($expr, $v) === 1)
                        || (!$isRegex && strpos($expr, $v) !== false)
                    ) {
                        return true;
                    }
                }

                return false;
            };

            if ($inExpr) {
                $promise = $promise->then(function ($channels) use ($inExpr, $match) {
                    return Promise\reduce($channels, function ($carry, AutoChannel $channel) use ($inExpr, $match) {
                        return $this->getChannelName($channel)
                            ->then(function ($name) use ($carry, $channel, $inExpr, $match) {

                                if ($match($inExpr, $name)) {
                                    $carry[] = $channel;
                                }

                                return $carry;
                            });
                    }, array());
                });
            }

            if ($notInExpr) {
                $promise = $promise->then(function ($channels) use ($notInExpr, $match) {
                    return Promise\reduce($channels, function ($carry, AutoChannel $channel) use ($notInExpr, $match) {
                        return $this->getChannelName($channel)
                            ->then(function ($name) use ($carry, $channel, $notInExpr, $match) {

                                if ($match($notInExpr, $name)) {
                                    $carry = array_filter($carry, function ($item) use ($channel) {
                                        return $item !== $channel;
                                    });
                                }

                                return $carry;
                            });
                    }, $channels);
                });
            }

            return $promise;
        });
    }

    /**
     * @param AutoChannel $channel
     * @return Promise\Promise
     */
    public function getChannelName($channel)
    {
        $key = "slack.channel.{$channel->getId()}.name";
        $deferred = new Promise\Deferred();

        $name = $this->cache->fetch($key);
        if ($name !== false) {
            $deferred->resolve($name);
        } else {
            $channel->getName()
                ->then(function ($name) use ($deferred, $key) {
                    $this->cache->save($key, $name, 3600 * 24 * 7);
                    $deferred->resolve($name);
                });
        }

        return $deferred->promise();
    }

    /**
     * @param AutoChannel $channel
     * @return Promise\Promise
     */
    public function getHistory($channel)
    {
        $key = "slack.channel.{$channel->getId()}.history";
        $deferred = new Promise\Deferred();

        $history = $this->cache->fetch($key);
        if ($history !== false) {
            $deferred->resolve($history);
        } else {
            $channel->getHistory()
                ->then(function ($history) use ($deferred, $key) {
                    $this->cache->save($key, $history, 3600);
                    $deferred->resolve($history);
                });
        }

        return $deferred->promise();
    }

    /**
     * @param ApiClients $apiClient
     * @param null $inExpr
     * @param null $notInExpr
     * @return Promise\PromiseInterface
     */
    public function getHistories(ApiClients $apiClient, $inExpr = null, $notInExpr = null)
    {
        return $this
            ->getChannels($apiClient, $inExpr, $notInExpr)
            ->then(function ($channels) {
                return Promise\all(Promise\reduce($channels, function ($carry, $channel) {
                    /** @var AutoChannel $channel */
                    $carry[] = $channel->getHistory()
                        ->then(function ($history) use ($channel) {
                            return [
                                'channel' => $channel,
                                'history' => $history,
                            ];
                        });
                    return $carry;
                }, array()));
            });
    }

    /**
     * @param $loop
     * @return Promise\Promise
     */
    public function getLastMessages($loop)
    {
        return $this->getHistories($loop)
            ->then(function ($histories) {
                $messages = [];
                foreach ($histories as $kHistory => $history) {
                    foreach ($history['history']['messages'] as $kMessage => $message) {
                        if ($message['type'] == 'message') {
                            $messages[] = [
                                'kHistory' => $kHistory,
                                'kMessage' => $kMessage,
                                'ts' => $message['ts'],
                            ];
                        }
                    }
                }

                usort($messages, function ($messageA, $messageB) {
                    return $messageA['ts'] < $messageB['ts'];
                });

                $messages = array_reverse(array_slice($messages, 0, 20));

                $messages = array_map(function ($message) use ($histories) {
                    $newMessage = $histories[$message['kHistory']]['history']['messages'][$message['kMessage']];
                    $newMessage['channel'] = $histories[$message['kHistory']]['channel'];

                    return $newMessage;
                }, $messages);

                return $messages;
            });
    }
}