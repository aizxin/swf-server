<?php
/**
 * FileName: PoolInterface.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 11:34
 */

namespace swf\pool;

interface PoolInterface
{
    /**
     * Get a connection from the connection pool.
     */
    public function get();

    /**
     * Release a connection back to the connection pool.
     */
    public function release($connection): void;

    /**
     * Close and clear the connection pool.
     */
    public function flush(): void;
}