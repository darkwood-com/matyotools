<?php

namespace Command;

use Services\SlackService;
use Slack\Group;
use Slack\Message\Message;
use Slack\Message\MessageBuilder;
use Slack\Payload;
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

class EmojiCommand extends Command
{
    /**
     * @var SlackService
     */
    protected $slackService;

    public function __construct($slackService)
    {
        parent::__construct();

        $this->slackService = $slackService;
    }

    protected function configure()
    {
        $this
            ->setName('bot:emoji');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = Factory::create();

        $this->slackService->getLastMessages($loop)
            ->then(function ($messages) use ($output) {
                foreach ($messages as $message) {
                    $time = new \DateTime($message['ts']);

                    $output->writeln($time->format('JJ/MM/YYYY') . ' - ' . $message['text']);
                }
            })
        ;

        $loop->run();
    }
}