<?php
/**
 * FileName: Db.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 15:28
 */

namespace swf\model;


use swf\pool\PoolFactory;
use Swoole\Coroutine;

class Db extends \think\Db
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->run();
    }

    /**
     * @param string $name
     *
     * @author: kong | <iwhero@yeah.com>
     * @date  : 2019-06-30 15:13
     */
    private function run($config = [])
    {
        $name = $config['type'] ?? 'mysql';
        $mysql = (new PoolFactory())->getPool($name,DbPool::class,$config);
        $connection = $mysql->get();
        if (Coroutine::getCid()) {
            \Yaf\Registry::get('swoole')->defer(function () use ($mysql, $connection) {
                $mysql->release($connection);
            });
        }
        // 重置数据库查询次数
        $connection->clearQueryTimes();
        // 重置数据库执行次数
        // \think\facade\Db::$executeTimes = 0;
        $this->connection = $connection->getConnection();

        return $this->connection;
    }

    /**
    * 创建一个新的查询对象
    * @access public
    * @param string|array $connection 连接配置信息
    * @return mixed
    */
    public function buildQuery($connection = [])
    {
        return $this->newQuery($this->run($connection));
    }
    
}