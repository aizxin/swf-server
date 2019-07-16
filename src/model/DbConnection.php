<?php
/**
 * FileName: DbConnection.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-16 19:31
 */

namespace swf\model;


use swf\pool\Connection;

class DbConnection extends Connection
{
    /**
     * @var \Redis
     */
    protected $connection;

    /**
     * @var array
     */
    protected $config;

    public function __construct(DbPool $pool, $config)
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
        $type = !empty($this->config['type']) ? $this->config['type'] : 'mysql';

        if (strpos($type, '\\')) {
            $class = $type;
        } else {
            $class = '\\think\\db\\connector\\' . ucfirst($type);
        }
        $this->connection = new $class($this->config);
        $this->lastUseTime = microtime(true);
        return true;
    }

    public function close(): bool
    {
        return true;
    }
}