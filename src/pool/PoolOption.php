<?php
/**
 * FileName: PoolOption.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 11:49
 */

namespace swf\pool;


class PoolOption
{
    /**
     * Min connections of pool.
     * This means the pool will create $minConnections connections when
     * pool initialization.
     *
     * @var int
     */
    private $minConnections = 1;

    /**
     * Max connections of pool.
     *
     * @var int
     */
    private $maxConnections = 10;

    /**
     * The timeout of connect the connection.
     * Default value is 10 seconds.
     *
     * @var float
     */
    private $connectTimeout = 10.0;

    /**
     * The timeout of pop a connection.
     * Default value is 3 seconds.
     *
     * @var float
     */
    private $waitTimeout = 3.0;

    /**
     * Heartbeat of connection.
     * If the value is 10, then means 10 seconds.
     * If the value is -1, then means does not need the heartbeat.
     * Default value is -1.
     *
     * @var float
     */
    private $heartbeat = -1;

    /**
     * The max idle time for connection.
     * @var float
     */
    private $maxIdleTime = 60.0;

    public function __construct($options)
    {
        $this->minConnections = $options['min_connections'] ?? 1;
        $this->maxConnections = $options['max_connections'] ?? 10;
        $this->connectTimeout = $options['connect_timeout'] ?? 10.0;
        $this->waitTimeout = $options['wait_timeout'] ?? 3.0;
        $this->heartbeat = $options['heartbeat'] ?? -1;
        $this->maxIdleTime = $options['max_idle_time'] ?? 60.0;
    }

    public function getMaxConnections(): int
    {
        return $this->maxConnections;
    }

    public function setMaxConnections(int $maxConnections): self
    {
        $this->maxConnections = $maxConnections;
        return $this;
    }

    public function getMinConnections(): int
    {
        return $this->minConnections;
    }

    public function setMinConnections(int $minConnections): self
    {
        $this->minConnections = $minConnections;
        return $this;
    }

    public function getConnectTimeout(): float
    {
        return $this->connectTimeout;
    }

    public function setConnectTimeout(float $connectTimeout): self
    {
        $this->connectTimeout = $connectTimeout;
        return $this;
    }

    public function getHeartbeat(): float
    {
        return $this->heartbeat;
    }

    public function setHeartbeat(float $heartbeat): self
    {
        $this->heartbeat = $heartbeat;
        return $this;
    }

    public function getWaitTimeout(): float
    {
        return $this->waitTimeout;
    }

    public function setWaitTimeout(float $waitTimeout): self
    {
        $this->waitTimeout = $waitTimeout;
        return $this;
    }

    /**
     * @return float
     */
    public function getMaxIdleTime(): float
    {
        return $this->maxIdleTime;
    }

    /**
     * @param float $maxIdleTime
     */
    public function setMaxIdleTime(float $maxIdleTime): self
    {
        $this->maxIdleTime = $maxIdleTime;
        return $this;
    }
}