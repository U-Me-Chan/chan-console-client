<?php

use Symfony\Component\Console\Application;
use PF\Console\Commands\Read\Board;
use PF\Console\Commands\Read\Thread;
use PF\Console\Commands\Create\Post;
use PF\Console\Commands\Create\Reply;

require_once "vendor/autoload.php";

$app = new Application('PissyFront', '0.1.0');

$app->addCommands([
    new Board(),
    new Thread(),
    new Post(),
    new Reply()
]);

$app->run();
