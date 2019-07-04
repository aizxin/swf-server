<?php
/**
 * FileName: Redis.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-04 22:23
 */

namespace swf\redis;


use swf\pool\Connection;
use swf\redis\pool\RedisPool;

class RedisConnection extends Connection
{
    /**
     * @var \Redis
     */
    protected $connection;

    /**
     * @var array
     */
    protected $config;

    public function __construct(RedisPool $pool, $config)
    {
        parent::__construct($pool);
        $this->config = $config;

        $this->reconnect();
    }

    public function __call($name, $arguments)
    {
        return $this->connection->{$name}(...$arguments);
    }

    public function getActiveConnection()
    {
        if ($this->check()) {
            return $this;
        }

        if (! $this->reconnect()) {
            throw new \Exception('Connection reconnect failed.');
        }
        return $this;
    }

    public function reconnect(): bool
    {
        $host = $this->config['host'] ?? 'localhost';
        $port = $this->config['port'] ?? 6379;
        $auth = $this->config['auth'] ?? null;
        $db = $this->config['db'] ?? 0;
        $timeout = $this->config['timeout'] ?? 0.0;

        $redis = new \Redis();
        if (! $redis->connect($host, $port, $timeout)) {
            throw new \Exception('Connection reconnect failed.');
        }

        if (isset($auth)) {
            $redis->auth($auth);
        }

        if ($db > 0) {
            $redis->select($db);
        }

        $this->connection = $redis;
        $this->lastUseTime = microtime(true);
        return true;
    }

    public function close(): bool
    {
        return true;
    }
}