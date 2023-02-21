<?php

namespace Lib;

use Predis\Client;

class SimpleRedis {

    private $host = null;
    private $port = null;    
    private $password = null;
    private $username = null;
    private $scheme = null;
    private $callbacks = [];
    private $pubsub = null;
    private $list = null;
    public $debug = false;
    public static $connection = null;

    public function __construct(string $host = "localhost", string $port = "6379", string $password = "password", string $username = "", string $scheme = "tcp")
    {
        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
        $this->username = $username;
        $this->scheme = $scheme;
    }

    public function open()
    {
        if (self::$connection === null) {
            self::$connection = new Client([
                'scheme' => $this->scheme,
                'host' => $this->host,
                'port' => $this->port,
                'username' => $this->username,
                'password' => $this->password,
                'read_write_timeout' => 0
            ]);
        }
        return self::$connection;
    }

    public function close()
    {
        if (self::$connection !== null) {
            self::$connection = null;
        }
        return self::$connection;
    }

    public function get(string $key)
    {
        if (self::$connection !== null) {
            return self::$connection->get($key);
        }
        return null;
    }

    public function set(string $key, $value, int $time = 0)
    {
        if (self::$connection !== null) {
            if ($time > 0) {
                return self::$connection->setex($key, $time, $value);//seg
                //return self::$connection->psetex($key, $time, $value);//ms
            } else {
                return self::$connection->set($key, $value);
            }
            return self::$connection->get($key);
        }
        return false;
    }

    public function del(string $key)
    {
        if (self::$connection !== null) {
            return self::$connection->del($key);
        }
        return null;
    }

    public function pub(string $channel, string $message)
    {
        if (self::$connection !== null) {
            return self::$connection->publish($channel, $message);
        }
        return null;
    }

    public function sub(string $channel, callable $callback)
    {
        if (self::$connection !== null) {
            return [$channel => $this->callbacks[$channel] = $callback];
        }
        return null;
    }

    public function waitCallbacks(int $sleep = 0)
    {
        if (self::$connection !== null) {
            $this->pubsub = self::$connection->pubSubLoop();
            $this->callbacks["channel_break"] = function(){};
            $this->pubsub->subscribe(array_keys($this->callbacks));
            foreach ($this->pubsub as $message) {
                if ($this->debug) {
                    echo  "Kind: ", $message->kind, " | Channel: ", $message->channel, " | Payload: ", $message->payload, PHP_EOL;
                }
                if ($message->kind === "message" && in_array($message->channel, array_keys($this->callbacks))) { 
                    $this->callbacks[$message->channel]($message->payload, $message->channel);
                }
                if ($message->kind === "message" && $message->channel === "channel_break" && $message->payload === "channel_break") {
                    $this->pubsub->unsubscribe();
                    $this->callbacks = [];
                    break;
                }
                if ($sleep > 0) {
                    usleep($sleep);
                }
            }
            unset($this->pubsub);
        }
        return null;
    }

    public function listPush($message, string $list = null)
    {
        if ($list === null) {
            $list = $this->list ?? md5(uniqid());
        }
        if (self::$connection !== null) {
            $this->list = $list;
            return self::$connection->lpush($list, $message);
        }
        return null;
    }

    public function listPop(string $list = null)
    {
        if ($list === null) {
            $list = $this->list;
        }
        if (self::$connection !== null && $list !== null) {
            $this->list = $list;
            return self::$connection->lpop($list);
        }
    }

    public function listSize(string $list = null)
    {
        if ($list === null) {
            $list = $this->list;
        }
        if (self::$connection !== null && $list !== null) {
            $this->list = $list;
            return self::$connection->llen($list);
        }
        return -1;
    }

    public function listIndex(int $index, string $list = null)
    {
        if ($list === null) {
            $list = $this->list;
        }
        if (self::$connection !== null && $list !== null) {
            $this->list = $list;
            return self::$connection->lindex($list, $index);
        }
        return null;
    }

    public function listAll(string $list = null, bool $reverse = true)
    {
        if ($list === null) {
            $list = $this->list;
        }
        if (self::$connection !== null && $list !== null) {
            $this->list = $list;
            $array = self::$connection->lrange($list, 0, -1) ?? [];
            if ($reverse) {
                $array = array_reverse($array);
            }
            return $array;
        }
    }

}
