<?php
/**
 * FileName: Timer.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 09:09
 */

namespace swf\timer;

use swf\task\Task;
use Swoole\Timer as SwooleTimer;
use XCron\CronExpression;

/**
 * Class Timer
 * 可以执行回调函数，同时可以执行定时器模板
 * @package xavier\swoole
 */
class Timer
{
    private static $timerlists = [];
    private $config = [];

    public function __construct()
    {
        //获取配置信息
        if (class_exists('Yaconf')) {
            $this->config = \Yaconf::get('timer');
        } else {
            $this->config = (new \Yaf\Config\Ini(APP_PATH . "/conf/timer.ini"))->toArray();
        }

        if (empty($this->config)) {
            $this->config = [];
        }
    }

    /**
     * 开始执行定时器任务
     *
     * @param $serv 服务对象
     */
    public function run($serv)
    {
        if (count(self::$timerlists) > 0) {
            $this->startTask();
        } else {
            $this->initimerlists();
        }
    }

    /**
     * 到期后执行定时任务
     */
    public function startTask()
    {
        foreach (self::$timerlists as &$one) {
            if ($one['next_time'] <= time()) {
                $cron = CronExpression::factory($one['key']);

                $one['next_time'] = $cron->getNextRunDate()->getTimestamp();

                $this->syncTask($one['val']);
            }
        }
        unset($one);
    }

    /**
     * 根据定时配置计算下次执行时间并存储相关信息
     * @throws \Exception
     */
    public function initimerlists()
    {
        self::$timerlists = [];
        foreach ($this->config as $key => $val) {
            if ( ! class_exists($val)) {
                continue;
            }
            try {
                $cron = CronExpression::factory($key);
                $time = $cron->getNextRunDate()->getTimestamp();

                self::$timerlists[] = [
                    'key'       => $key,
                    'val'       => $val,
                    'next_time' => $time,
                ];
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    /**
     * 异步投递任务到task worker
     *
     * @param string $class
     */
    public function syncTask($class)
    {
        if (is_string($class) && class_exists($class)) {
            Task::async(function () use ($class) {
                $obj = new $class();
                $obj->run();
                unset($obj);
            });
        }
    }

    /**
     * 每隔固定时间执行一次
     *
     * @param int   $time     间隔时间
     * @param mixed $callback 可以是回调 可以是定时器任务模板
     *
     * @return bool
     */
    public function tick($time, $callback)
    {
        if ($callback instanceof \Closure) {
            return SwooleTimer::tick($time, $callback);
        } elseif (is_object($callback) && method_exists($callback, 'run')) {
            return SwooleTimer::tick($time, function () use ($callback) {
                $callback->run();
            });
        }

        return false;
    }

    /**
     * 延迟执行
     *
     * @param int   $time     间隔时间
     * @param mixed $callback 可以是回调 可以是定时器任务模板
     *
     * @return bool
     */
    public function after($time, $callback)
    {
        if ($callback instanceof \Closure) {
            return SwooleTimer::after($time, $callback);
        } elseif (is_object($callback) && method_exists($callback, 'run')) {
            return SwooleTimer::after($time, function () use ($callback) {
                $callback->run();
                unset($callback);
            });
        }

        return false;
    }

    /**
     * 清除定时器
     *
     * @param int $timerId
     *
     * @return bool
     */
    public function clear($timerId)
    {
        return SwooleTimer::clear($timerId);
    }
}