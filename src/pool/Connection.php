<?php
/**
 * FileName: Connection.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-04 22:21
 */

namespace swf\pool;


abstract class Connection
{

    /**
     * @var Pool
     */
    protected $pool;

    /**
     * @var float
     */
    protected $lastUseTime = 0.0;

    public function __construct(AbstractPool $pool)
    {
        $this->pool = $pool;
    }

    public function release(): void
    {
        $this->pool->release($this);
    }

    public function getConnection()
    {
        return $this->getActiveConnection();
    }

    public function check(): bool
    {
        $maxIdleTime = $this->pool->getOption()->getMaxIdleTime();
        $now = microtime(true);
        if ($now > $maxIdleTime + $this->lastUseTime) {
            return false;
        }

        $this->lastUseTime = $now;
        return true;
    }

    abstract public function getActiveConnection();
}