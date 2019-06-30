<?php
/**
 * FileName: Task.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 11:09
 */

namespace swf\task;

use Yaf\Registry;

class Task
{
    /**
     * 异步投递任务
     * @param mixed     $task  任务，可以是闭包可以是任务模板
     * @param mixed     $finishCallback 任务执行完成回调 可以为空
     * @param int       $taskWorkerId 指定task worker 来执行任务，不指定，自动分配
     * @return mixed
     */
    public static function async($task, $finishCallback = null, $taskWorkerId = -1)
    {
        if ($task instanceof \Closure) {
            try {
                $task = new SuperClosure($task);
            } catch (\Throwable $throwable) {
                \Trigger::throwable($throwable);
                return false;
            }
        }

        return Registry::get('swoole')->task($task, $taskWorkerId);
    }
}