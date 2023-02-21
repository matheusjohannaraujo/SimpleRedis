<?php

require_once "vendor/autoload.php";

use Lib\SimpleRedis;

$sr = new SimpleRedis;
$sr->open();

$sr->sub("channel", function($message, $channel) {
    echo "Message: ", $message, " | Channel: ", $channel, PHP_EOL;
});

$sr->waitCallbacks(1000000);

$sr->close();
