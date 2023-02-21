<?php

require_once "vendor/autoload.php";

use Lib\SimpleRedis;

$sr = new SimpleRedis;
$sr->open();

// Set (key, value, ttl)
echo "Set: ", $sr->set("name", "Matheus", 60), PHP_EOL, PHP_EOL;

// Get (key)
echo "Get: ", $sr->get("name"), PHP_EOL, PHP_EOL;

sleep(10);
// Del (key)
echo "Del: ", $sr->del("name"), PHP_EOL, PHP_EOL;

// List (value, key)
echo "List push: ",
$sr->listPush("Matheus", "list_names"), " | ", 
$sr->listPush("Johann"), " | ",
$sr->listPush("Araújo"), PHP_EOL, PHP_EOL;

// List size (key)
echo "List size: ", $sr->listSize(), PHP_EOL, PHP_EOL;

// List index (index, key)
echo "List index: ", $sr->listIndex(0), PHP_EOL, PHP_EOL;

// List all (key)
echo "List: ";
var_dump($sr->listAll());
echo PHP_EOL;

// List pop (key)
while (($value = $sr->listPop()) !== null) {
    echo "Name: ", $value, PHP_EOL;
    sleep(5);
}

$sr->close();
