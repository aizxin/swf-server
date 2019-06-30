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

class Cache extends \think\Cache
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->handler = $this->run();
    }

    /**
     * 自动初始化缓存
     * @access public
     * @param  array $options 配置数组
     * @param  bool  $force   强制更新
     * @return Driver
     */
    private function run($name = 'cache')
    {
        $chche = (new PoolFactory())->getPool('cache',CachePool::class);
        $connection = $chche->get();
        if (Coroutine::getCid()) {
            \Yaf\Registry::get('swoole')->defer(function () use ($chche, $connection) {
                $chche->release($connection);
            });
        }
        return $connection;
    }
}