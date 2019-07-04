<?php
/**
 * FileName: ListenerProviderFactory.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-02 23:45
 */

namespace swf\event;


use swf\facade\Log;

class ListenerProviderFactory
{
    public function registerConfig(ListenerProvider $provider, $container,$listenersConfig = []): void
    {
        $listenersConfig = array_merge($listenersConfig,array_values(\Yaconf::get('listener')),ListenerManager::all());


        foreach ($listenersConfig as $listener) {
            $priority = 1;
            if (is_string($listener)) {
                $this->register($provider, $container, $listener, $priority);
            }
        }
    }

    private function register(ListenerProvider $provider, $container, string $listener, int $priority = 1): void
    {
        $instance = $container->make($listener);
        if (method_exists($instance, 'process') && method_exists($instance, 'listen')) {
            foreach ($instance->listen() as $event) {
                $provider->on($event, [$instance, 'process'], $priority);
            }
        }
    }
}