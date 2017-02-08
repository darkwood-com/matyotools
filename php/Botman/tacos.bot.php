<?php
include 'config.php';

require 'vendor/autoload.php';

use Mpociot\BotMan\BotManFactory;
use Mpociot\BotMan\BotMan;
use React\EventLoop\Factory;

$loop = Factory::create();
$botman = BotManFactory::createForRTM($config, $loop);

$botman->hears('keyword', function(BotMan $bot) {
    $bot->reply('I heard you! :)');
});

$botman->hears('convo', function(BotMan $bot) {
    $bot->startConversation(new ExampleConversation());
});

$loop->run();