<?php
/**
 * FileName: Db.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 15:30
 */

namespace swf\facade;


use think\Facade;

class Db extends Facade
{
    protected static function getFacadeClass()
    {
        return \swf\model\Db::class;
    }
}