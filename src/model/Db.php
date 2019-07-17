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


use swf\facade\Log;
use swf\pool\Channel;
use swf\pool\Context;
use swf\pool\PoolFactory;
use Swoole\Coroutine;
use think\Container;
use think\DbManager;
use think\db\Connection;

class Db extends DbManager
{
    /**
     * 架构函数
     * @access public
     */
    public function __construct()
    {
        $this->modelMaker();
    }

    /**
     * 获取配置参数
     * @access public
     *
     * @param  string $config  配置参数
     * @param  mixed  $default 默认值
     *
     * @return mixed
     */
    public function getConfig(string $name = '', $default = null)
    {
        if ('' === $name) {
            return $this->config;
        }

        return $this->config[ $name ] ?? $default;
    }

    /**
     * 注入模型对象
     * @access public
     * @return void
     */
    protected function modelMaker()
    {
        Model::maker(function (Model $model) {
            $model->setDb($this);

            if (is_object($this->event)) {
                $model->setEvent($this->event);
            }

            $isAutoWriteTimestamp = $model->getAutoWriteTimestamp();

            if (is_null($isAutoWriteTimestamp)) {
                // 自动写入时间戳
                $model->isAutoWriteTimestamp($this->getConfig('auto_timestamp', true));
            }

            $dateFormat = $model->getDateFormat();

            if (is_null($dateFormat)) {
                // 设置时间戳格式
                $model->setDateFormat($this->getConfig('datetime_format', 'Y-m-d H:i:s'));
            }
        });
    }


    /**
     * 创建数据库连接实例
     * @access protected
     *
     * @param string|null $name  连接标识
     * @param bool        $force 强制重新连接
     *
     * @return Connection
     */
    protected function instance($name = null, $force = false): Connection
    {
        if (empty($name)) {
            $name = $this->getConfig('default', 'mysql');
        }

        $connections = $this->getConfig('connections');

        $config = $connections[ $name ];

        if ($force) {
            if ( ! isset($connections[ $name ])) {
                throw new InvalidArgumentException('Undefined db config:' . $name);
            }

            return $this->getConnectionPool($name, $config);
        }

        return $this->getConnectionPool($name, $config);
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
    /**
     * @param string $name
     *
     * @author: kong | <iwhero@yeah.com>
     * @date  : 2019-06-30 15:13
     */
    protected function getConnectionPool($name, $config)
    {
        $connection = null;
        $id = $this->getContextKey($name);

        if (Context::has($id)) {
            $connection = Context::get($id);
        }

        if ( ! $connection instanceof DbConnection) {
            $connection = PoolFactory::getPool($id, DbPool::class, $config)->get();
            Context::set($id, $connection);
            if (Context::inCoroutine()) {
                \Yaf\Registry::get('swoole')->defer(function () use ($connection) {
                    $connection->release();
                });
            }
        }
        // 重置数据库查询次数
        $this->clearQueryTimes();

        return $connection->getConnection();
    }

    /**
     * The key to identify the connection object in coroutine context.
     */
    private function getContextKey($name): string
    {
        return sprintf('database.connection.%s', $name);
    }

}