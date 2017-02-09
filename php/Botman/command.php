<?php

require __DIR__.'/config.php';
require __DIR__.'/vendor/autoload.php';
require __DIR__.'/src/bots/autojoin.php';
require __DIR__.'/src/bots/tacos.php';

use Symfony\Component\Console\Application;
use bots\AutoJoinCommand;
use bots\TacosCommand;

$application = new Application();
$application->add(new AutoJoinCommand($configs));
$application->add(new TacosCommand($configs));
$application->run();