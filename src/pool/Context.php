<?php
/**
 * FileName: Context.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019/7/17 13:09
 */

namespace swf\pool;

use Swoole\Coroutine as SwCoroutine;

class Context
{
    /**
     * @var string
     */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get a connection from request context.
     */
    public function connection()
    {
        if (static::has($this->name)) {
            return static::get($this->name);
        }

        return null;
    }

    protected static $nonCoContext = [];

    public static function set(string $id, $value)
    {
        if (static::inCoroutine()) {
            SwCoroutine::getContext()[ $id ] = $value;
        } else {
            static::$nonCoContext[ $id ] = $value;
        }

        return $value;
    }

    public static function get(string $id, $default = null, $coroutineId = null)
    {
        if (static::inCoroutine()) {
            if ($coroutineId !== null) {
                return SwCoroutine::getContext($coroutineId)[ $id ] ?? $default;
            }

            return SwCoroutine::getContext()[ $id ] ?? $default;
        }

        return static::$nonCoContext[ $id ] ?? $default;
    }

    public static function has(string $id, $coroutineId = null)
    {
        if (static::inCoroutine()) {
            if ($coroutineId !== null) {
                return isset(SwCoroutine::getContext($coroutineId)[ $id ]);
            }

            return isset(SwCoroutine::getContext()[ $id ]);
        }

        return isset(static::$nonCoContext[ $id ]);
    }

    /**
     * Release the context when you are not in coroutine environment.
     */
    public static function destroy(string $id)
    {
        unset(static::$nonCoContext[ $id ]);
    }

    public static function inCoroutine(): bool
    {
        return SwCoroutine::getCid();
    }

}