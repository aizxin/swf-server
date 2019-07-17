<?php
/**
 * FileName: CacheConnection.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-16 20:57
 */

namespace swf\cache;


use swf\pool\Connection;
use think\Container;

class CacheConnection extends Connection
{
    /**
     * @var \Redis
     */
    protected $connection;

    /**
     * @var array
     */
    protected $config;

    public function __construct(CachePool $pool, $config)
    {
        parent::__construct($pool);
        $this->config = $config;

        $this->reconnect();
    }

    public function getActiveConnection()
    {
        if ($this->check()) {
            return $this->connection;
        }

        if (! $this->reconnect()) {
            throw new \Exception('Connection reconnect failed.');
        }
        return $this->connection;
    }

    public function reconnect(): bool
    {
        $type = !empty($this->config['type']) ? $this->config['type'] : 'File';

        $this->connection = Container::factory($type, '\\think\\cache\\driver\\', $this->config);;
        $this->lastUseTime = microtime(true);
        return true;
    }

    public function close(): bool
    {
        unset($this->connection);
        return true;
    }
}