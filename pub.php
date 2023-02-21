<?php

require_once "vendor/autoload.php";

use Lib\SimpleRedis;

$sr = new SimpleRedis;
$sr->open();

for ($i = 1; $i <= 10; $i++) { 
    $sr->pub("channel", "Matheus " . $i);
}

sleep(3);

$sr->pub("channel_break", "channel_break");

$sr->close();
