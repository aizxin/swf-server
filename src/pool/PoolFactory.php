<?php
/**
 * FileName: PoolFactory.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 12:04
 */

namespace swf\pool;


use think\Container;

class PoolFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Pool[]
     */
    protected $pools = [];

    /**
     * @var array
     */
    protected $configs;

    public function __construct($container = '')
    {
        $this->container = \think\Container::getInstance();
    }

    public function addConfig(Config $config)
    {
        $this->configs[$config->getName()] = $config;
        return $this;
    }

    public function getPool(string $name,$pool,$config = [])
    {
        $name = $config['name'] ?? $name;

        if (isset($this->pools[$name])) {
            return $this->pools[$name];
        }

        if ($this->container instanceof Container) {
            $pool = $this->container->make($pool, ['name' => $name,'config'=>$config]);
        }
        return $this->pools[$name] = $pool;
    }


    public function get(string $name, callable $callback, array $option = []): Pool
    {
        if (! $this->hasConfig($name)) {
            $config = new Config($name, $callback, $option);
            $this->addConfig($config);
        }

        $config = $this->getConfig($name);

        if (! isset($this->pools[$name])) {
            $this->pools[$name] = $this->container->make(Pool::class, [
                'callback' => $config->getCallback(),
                'option' => $config->getOption(),
            ]);
        }

        return $this->pools[$name];
    }

    protected function hasConfig(string $name): bool
    {
        return isset($this->configs[$name]);
    }

    protected function getConfig(string $name): Config
    {
        return $this->configs[$name];
    }
}