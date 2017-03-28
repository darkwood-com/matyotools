<?php
namespace Slack;

use React\Promise;

/**
 * Represents a single Slack channel.
 */
class AutoChannel extends ClientObject implements ChannelInterface
{
    /**
     * @var Channel|Group|DirectMessageChannel|MultiDirectMessageChannel
     */
    private $instance;

    public function __construct(ClientObject $instance)
    {
        parent::__construct($instance->getClient(), $instance->data);

        $this->instance = $instance;
    }

    public function getInstance()
    {
        return $this->instance;
    }

    public function getId()
    {
        return $this->instance->getId();
    }

    public function close()
    {
        return $this->instance->close();
    }

    /**
     * @return Promise\Promise
     */
    public function getName()
    {
        if ($this->instance instanceof Channel || $this->instance instanceof Group) {
            return Promise\resolve($this->instance->getName());
        } elseif ($this->instance instanceof DirectMessageChannel) {
            return $this->instance->getUser()->then(function ($user) {
                /** @var User $user */
                return $user->getUsername();
            });
        } elseif ($this->instance instanceof MultiDirectMessageChannel) {
            return  Promise\resolve($this->data['name']);
        }

        return  Promise\resolve(null);
    }

    /**
     * @return Promise\Promise
     */
    public function getMembers()
    {
        if ($this->instance instanceof Channel || $this->instance instanceof Group) {
            return $this->instance->getMembers();
        } elseif ($this->instance instanceof DirectMessageChannel || $this->instance instanceof MultiDirectMessageChannel) {
            $memberPromises = [];
            foreach ($this->data['members'] as $memberId) {
                $memberPromises[] = $this->client->getUserById($memberId);
            }

            return Promise\all($memberPromises);
        }

        return Promise\resolve(array());
    }

    public function getHistory()
    {
        if ($this->instance instanceof Channel) {
            return $this->client->apiCall('channels.history', [
                'channel' => $this->getId(),
            ]);
        } elseif ($this->instance instanceof Group) {
            return $this->client->apiCall('groups.history', [
                'channel' => $this->getId(),
            ]);
        } elseif ($this->instance instanceof DirectMessageChannel) {
            return $this->client->apiCall('im.history', [
                'channel' => $this->getId(),
            ]);
        } elseif ($this->instance instanceof MultiDirectMessageChannel) {
            return $this->client->apiCall('mpim.history', [
                'channel' => $this->getId(),
            ]);
        }

        return Promise\resolve(array());
    }
}