<?php

require_once "vendor/autoload.php";

use Lib\SimpleRedis;

$sr = new SimpleRedis;
$sr->open();

var_dump($sr->set("name", "Matheus", 60));
echo PHP_EOL;

var_dump($sr->get("name"));
echo PHP_EOL;

sleep(30);
var_dump($sr->del("name"));
echo PHP_EOL;

$sr->close();
