<?php
/**
 * FileName: PoolFactory.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 12:04
 */

namespace swf\pool;


use swf\facade\Log;
use think\Container;

class PoolFactory
{
    /**
     * @var Pool[]
     */
    protected static $pools = [];

    public static function getPool(string $name, $pool, $config = [])
    {

        if (isset(static::$pools[ $name ])) {
            return static::$pools[ $name ];
        }

        $pool = new $pool($name, $config);
        static::$pools[ $name ] = $pool;

        
        
        return $pool;
    }
}