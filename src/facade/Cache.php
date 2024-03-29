<?php
/**
 * FileName: Cache.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 14:59
 */

namespace swf\facade;


use think\Facade;

class Cache extends Facade
{
    protected static function getFacadeClass()
    {
        return \swf\cache\Cache::class;
    }
}