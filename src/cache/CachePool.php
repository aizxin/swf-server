<?php
/**
 * FileName: CachePool.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 13:06
 */
namespace swf\cache;

use swf\pool\AbstractPool;

class CachePool extends AbstractPool
{
    private $config;
    public function __construct($name = '',$container = '')
    {
        $this->config = \Yaconf::get($name);
        parent::__construct($container,$this->config['pool']);
    }

    /**
     * @return \swf\pool\ConnectionInterface
     * @author: kong | <iwhero@yeah.com>
     * @date  : 2019-06-30 13:13
     */
    protected function createConnection()
    {
        return \think\facade\Cache::init($this->config);
    }
}