<?php

namespace Services;

use Mpociot\BotMan\BotManFactory;
use Mpociot\BotMan\BotMan;
use React\EventLoop\Factory;
use React\Promise;
use Slack\ApiClients;
use Slack\ApiClient;
use Slack\Channel;
use Slack\DirectMessageChannel;
use Slack\Group;
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
     * @param $loop
     * @return Promise\Promise
     */
    public function getHistories($loop)
    {
        $clients = array_map(function ($config) use ($loop) {
            $client = new ApiClient($loop);
            $client->setToken($this->configs[$config]['slack_token']);

            return $client;
        }, ['bigyouth', 'makheia']);

        $channelHistories = array_reduce($clients, function ($carry, $client) {
            /** @var ApiClient $client */
            $carry[] = $client
                ->getChannels()
                ->then(function ($channels) {
                    /** @var Channel[] $channels */
                    $histories = [];

                    foreach ($channels as $channel) {
                        $histories[] = Promise\resolve($channel)
                            ->then(function ($channel) {
                                /** @var Channel $channel */
                                $client = $channel->getClient();

                                return $client->apiCall('channels.history', [
                                    'channel' => $channel->getId(),
                                ])->then(function ($history) use ($channel) {
                                    return [
                                        'channel' => $channel,
                                        'history' => $history,
                                    ];
                                });
                            });
                    }

                    return Promise\all($histories);
                });

            return $carry;
        }, []);

        $groupHistories = array_reduce($clients, function ($carry, $client) {
            /** @var ApiClient $client */
            $carry[] = $client
                ->getGroups()
                ->then(function ($groups) {
                    /** @var Group[] $groups */
                    $histories = [];

                    foreach ($groups as $group) {
                        $histories[] = Promise\resolve($group)
                            ->then(function ($group) {
                                /** @var Group $group */
                                $client = $group->getClient();

                                return $client->apiCall('groups.history', [
                                    'channel' => $group->getId(),
                                ])->then(function ($history) use ($group) {
                                    return [
                                        'channel' => $group,
                                        'history' => $history,
                                    ];
                                });
                            });
                    }

                    return Promise\all($histories);
                });

            return $carry;
        }, []);

        $dmHistories = array_reduce($clients, function ($carry, $client) {
            /** @var ApiClient $client */
            $carry[] = $client
                ->getDMs()
                ->then(function ($dms) {
                    /** @var DirectMessageChannel[] $dms */
                    $histories = [];

                    foreach ($dms as $dm) {
                        $histories[] = Promise\resolve($dm)
                            ->then(function ($dm) {
                                /** @var DirectMessageChannel $dm */
                                $client = $dm->getClient();

                                return $client->apiCall('im.history', [
                                    'channel' => $dm->getId(),
                                ])->then(function ($history) use ($dm) {
                                    return [
                                        'channel' => $dm,
                                        'history' => $history,
                                    ];
                                });
                            });
                    }

                    return Promise\all($histories);
                });

            return $carry;
        }, []);

        return Promise\reduce(array_merge(
            $channelHistories,
            $groupHistories,
            $dmHistories
        ), function ($carry, $histories) {
            $carry = array_merge($carry, $histories);
            return $carry;
        }, []);
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