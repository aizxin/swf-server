<?php
/**
 * FileName: Cache.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 14:54
 */

namespace swf\cache;


use swf\pool\PoolFactory;
use Swoole\Coroutine;
use think\cache\Driver;
use think\CacheManager;
use think\Container;

class Cache extends CacheManager
{
    /**
     * 连接或者切换缓存
     * @access public
     *
     * @param  string $name  连接配置名
     * @param  bool   $force 强制重新连接
     *
     * @return Driver
     */
    public function store(string $name = '', bool $force = false): Driver
    {
        if ('' == $name) {
            $name = $this->config['default'] ?? 'file';
        }

        if ($force) {
            if ( ! isset($this->config['stores'][ $name ])) {
                throw new InvalidArgumentException('Undefined cache config:' . $name);
            }

            $options = $this->config['stores'][ $name ];

            return $this->connect($options, $name);
        }

        $options = $this->config['stores'][ $name ];

        return $this->connect($options, $name);
    }

    /**
     * 连接缓存
     * @access public
     *
     * @param  array  $options 连接参数
     * @param  string $name    连接配置名
     *
     * @return Driver
     */
    public function connect(array $options, string $name = ''): Driver
    {
        $handler = $this->getConnectionPool($name, $options);

        return $handler;
    }

    /**
     * 自动初始化缓存
     * @access public
     *
     * @param  array $options 配置数组
     * @param  bool  $force   强制更新
     *
     * @return Driver
     */
    private function getConnectionPool($name, $config)
    {
        $chche = PoolFactory::getPool($name, CachePool::class, $config)->get();
        $connection = $chche->getConnection();
        if (Coroutine::getCid()) {
            \Yaf\Registry::get('swoole')->defer(function () use ($chche) {
                $chche->release($chche);
            });
        }

        return $connection;
    }

}