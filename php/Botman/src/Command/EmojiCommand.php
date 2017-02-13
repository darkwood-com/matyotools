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

        $this->slackService
            ->getHistories($loop)
            ->then(function ($histories) {
                $messages = [];
                foreach ($histories as $kHistory => $history) {
                    foreach ($history['history']['messages'] as $kMessage => $message) {
                        if($message['type'] == 'message') {
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

                $messages = array_map(function($message) use ($histories) {
                    $newMessage = $histories[$message['kHistory']]['history']['messages'][$message['kMessage']];
                    $newMessage['channel'] = $histories[$message['kHistory']]['channel'];

                    return $newMessage;
                }, $messages);

                return $messages;
            })->then(function ($messages) use ($output) {
                foreach ($messages as $message) {
                    $output->writeln($message['ts'] . ' - ' . $message['text']);
                }
            })
        ;

        $loop->run();
    }
}