<?php
/**
 * FileName: RedisPool.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-04 22:29
 */

namespace swf\redis\pool;


use swf\pool\AbstractPool;
use swf\redis\RedisConnection;
use think\Container;

class RedisPool extends AbstractPool
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $config;

    public function __construct($name,Container $container)
    {
        $this->name = $name;

        $config = \Yaconf::get('redis');
        
        $this->config = $config[$name];

        parent::__construct($this->config['pool'] ?? [],$container);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    protected function createConnection()
    {
        return new RedisConnection($this, $this->config);
    }
}