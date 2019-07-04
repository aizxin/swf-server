<?php
/**
 * FileName: Redis.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-04 22:32
 */

namespace swf\redis;


use swf\redis\pool\PoolFactory;
use Swoole\Coroutine;

class Redis
{
    /**
     * @var PoolFactory
     */
    protected $factory;

    /**
     * @var string
     */
    protected $poolName = 'default';

    public function __construct(PoolFactory $factory)
    {
        $this->factory = $factory;
    }

    public function __call($name, $arguments)
    {
        $connection = $this->getConnection();

        try {
            // Execute the command with the arguments.
            $result = $connection->{$name}(...$arguments);
        } finally {
            // Release connection.

            if ($this->shouldUseSameConnection($name)) {
                // Should storage the connection to coroutine context, then use defer() to release the connection.
                if (Coroutine::getCid()) {
                    \Yaf\Registry::get('swoole')->defer(function () use ($connection) {
                        $connection->release();
                    });
                }
            } else {
                $connection->release();
            }
        }

        return $result;
    }

    /**
     * Define the commands that needs same connection to execute.
     * When these commands executed, the connection will storage to coroutine context.
     */
    private function shouldUseSameConnection(string $methodName): bool
    {
        return in_array($methodName, [
            'multi',
            'pipeline',
        ]);
    }

    /**
     * Get a connection from coroutine context, or from redis connectio pool.
     * @param mixed $hasContextConnection
     */
    private function getConnection(): RedisConnection
    {
        $pool = $this->factory->getPool($this->poolName);
        return $pool->get()->getConnection();
    }
}