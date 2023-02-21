<?php

namespace Lib;

use Predis\Client;

class SimpleRedis {


    private $host = null;
    private $port = null;    
    private $password = null;
    private $username = null;
    private $scheme = null;
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
                'password' => $this->password
            ]);
        }
        return self::$connection;
    }

    public function close()
    {
        if (self::$connection !== null) {
            self::$connection->close();
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

}
