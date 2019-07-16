<?php
declare (strict_types = 1);

/**
 * FileName: Model.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 15:11
 */

namespace swf\model;

class Model extends \think\Model{

    /**
     * 设置Db对象
     * @access public
     * @param DbManager $db Db对象
     * @return void
     */
    public function setDb($db)
    {
        $this->db = $db;
    }
}