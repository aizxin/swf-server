<?php
/**
 * FileName: ServerEvent.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-03 22:05
 */

namespace swf\process\event;

class ServerEvent
{
    public $server;

    public function __construct($server)
    {
        $this->server = $server;
    }
}