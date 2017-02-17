<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mpociot\BotMan\BotManFactory;
use Mpociot\BotMan\BotMan;
use React\EventLoop\Factory;
use React\Promise;
use Slack\ApiClient;
use Slack\Channel;
use Slack\User;

class AutoJoinCommand extends Command
{
    /**
     * @var array
     */
    protected $configs;

    public function __construct($configs)
    {
        parent::__construct();

        $this->configs = $configs;
    }

    protected function configure()
    {
        $this
            ->setName('auto-join')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = Factory::create();

        $promises = [];
        foreach ($this->configs as $config)
        {
            $client = new ApiClient($loop);
            $client->setToken($config['slack_token']);

            $promises[] = Promise\all([
                $client->getChannels(),
                $client->getGroups(),
            ])->then(function ($data) use ($client) {
                /** @var Channel[] $channels */
                $channels = array_merge($data[0], $data[1]);

                // do no join mpim channels
                $channels = array_filter($channels, function($channel) {
                    /** @var Channel $channel */
                    return strpos($channel->getName(), 'mpdm-') === false;
                });

                foreach ($channels as $channel)
                {
                    $client->apiCall('channels.join', ['name' => $channel->getName()]);
                }

                return $channels;
            });
        }
        Promise\reduce($promises, function ($carry, $channels) {
            /** @var Channel[] $channels */
            $carry = array_merge($carry, $channels);

            return $carry;
        }, [])->then(function ($channels) use ($output) {
            /** @var Channel[] $channels */

            $output->writeln("joined " . count($channels) . " channels");
        });

        $loop->run();
    }
}