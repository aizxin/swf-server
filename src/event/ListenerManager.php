<?php
/**
 * FileName: ListenerProvider.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-02 22:51
 */

namespace swf\event;

class ListenerManager
{
    /**
     * @var array
     */
    protected static $listener = [];

    public static function register($listener): void
    {
        static::$listener[] = $listener;
    }

    public static function all(): array
    {
        return static::$listener;
    }

    public static function clear(): void
    {
        static::$listener = [];
    }
}