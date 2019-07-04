<?php
/**
 * FileName: RedisProxy.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-04 22:28
 */

namespace swf\redis;


use swf\redis\pool\PoolFactory;

class RedisProxy extends Redis
{
    protected $poolName;

    public function __construct(string $pool,PoolFactory $factory)
    {
        parent::__construct($factory);

        $this->poolName = $pool;
    }
}