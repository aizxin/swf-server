<?php
/**
 * FileName: DbPool.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 15:06
 */

namespace swf\model;

class DbPool extends \swf\pool\AbstractPool
{
    private $config;
    public function __construct($name = '',$config = [])
    {
        $this->config = $config;
        parent::__construct($this->config['pool'] ?? []);
    }

    /**
     * @return \swf\pool\ConnectionInterface
     * @author: kong | <iwhero@yeah.com>
     * @date  : 2019-06-30 13:13
     */
    protected function createConnection()
    {
        return new DbConnection($this,$this->config);
    }
}