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
    }

    public function getId()
    {
        // TODO: Implement getId() method.
    }

    public function close()
    {
        // TODO: Implement close() method.
    }
}