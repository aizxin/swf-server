<?php
/**
 * FileName: ProcessListeners.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-03 22:19
 */

namespace swf\process\listeners;

use swf\event\ListenerInterface;
use swf\facade\Log;
use swf\process\event\ServerEvent;
use swf\process\ProcessManager;
use think\Container;

class ProcessListeners implements ListenerInterface
{
    public function listen(): array
    {
        return [
            ServerEvent::class,
        ];
    }

    public function process(object $event)
    {
        if ($event instanceof ServerEvent) {
            $server = $event->server;
            if (class_exists('Yaconf')) {
                $processes = \Yaconf::get('process');
            } else {
                $processes = (new \Yaf\Config\Ini(APP_PATH . "/conf/process.ini"))->toArray();
            }
            $processes = array_merge(array_values($processes ?? []), ProcessManager::all());

            foreach ($processes as $process) {
                if (is_string($process)) {
                    $instance = Container::getInstance()->make($process);
                } else {
                    $instance = $process;
                }
                if (method_exists($instance, 'bind') && $instance->isEnable()) {
                    $instance->bind($server);
                }
            }
        }
    }
}