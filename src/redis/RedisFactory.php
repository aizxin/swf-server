<?php
/**
 * FileName: RedisFactory.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-04 22:27
 */

namespace swf\redis;


use think\Container;

class RedisFactory
{
    /**
     * @var RedisProxy[]
     */
    protected $proxies;

    public function __construct()
    {
        $redisConfig = \Yaconf::get('redis');

        foreach ($redisConfig as $poolName => $item) {
            $this->proxies[$poolName] = Container::getInstance()->make(RedisProxy::class, ['pool' => $poolName]);
        }
    }

    /**
     * @return \Redis
     */
    public function get(string $poolName)
    {
        $proxy = $this->proxies[$poolName] ?? null;
        if (! $proxy instanceof RedisProxy) {
            throw new \Exception('Redis proxy is invalid.');
        }

        return $proxy;
    }
}