<?php
/**
 * FileName: AbstractPool.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 11:56
 */

namespace swf\pool;


use swf\facade\Log;
use think\Container;

abstract class AbstractPool
{
    /**
     * @var Channel
     */
    protected $channel;

    /**
     * @var ContainerInterface
     */
    protected $name;

    /**
     * @var PoolOptionInterface
     */
    protected $option;

    /**
     * @var int
     */
    protected $currentConnections = 0;

    public function __construct($config = [],$name = '')
    {
        $this->name = $name;
        $this->initOption($config);
        $this->channel = new Channel($this->option->getMaxConnections());
    }

    public function get()
    {
        return $this->getConnection();
    }

    public function release($connection): void
    {
        $this->channel->push($connection);
    }

    public function flush(): void
    {
        $num = $this->getConnectionsInChannel();

        if ($num > 0) {
            while ($conn = $this->channel->pop($this->option->getWaitTimeout())) {
                $conn->close();
            }
        }
    }

    public function getCurrentConnections(): int
    {
        return $this->currentConnections;
    }

    public function getOption()
    {
        return $this->option;
    }

    protected function getConnectionsInChannel(): int
    {
        return $this->channel->length();
    }

    protected function initOption($options = []): void
    {
        $this->option = new PoolOption($options);
    }

    abstract protected function createConnection();

    private function getConnection()
    {
        $num = $this->getConnectionsInChannel();
        try {
            if ($num === 0 && $this->currentConnections < $this->option->getMaxConnections()) {
                ++$this->currentConnections;
                return $this->createConnection();
            }
        } catch (\Throwable $throwable) {
            --$this->currentConnections;
            throw $throwable;
        }
        $connection = $this->channel->pop($this->option->getWaitTimeout());

        return $connection;
    }

    
}