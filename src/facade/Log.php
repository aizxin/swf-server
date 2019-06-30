<?php
/**
 * FileName: Log.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 11:23
 */

namespace swf\facade;

class Log extends \think\Facade
{
    protected static function getFacadeClass()
    {
        return \think\Log::class;
    }
}