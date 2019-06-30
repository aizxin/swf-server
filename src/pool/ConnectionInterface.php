<?php
/**
 * FileName: ConnectionInterface.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 11:39
 */

namespace swf\pool;


interface ConnectionInterface
{
    /**
     * Get the real connection from pool.
     */
    public function getConnection();

    /**
     * Reconnect the connection.
     */
    public function reconnect(): bool;

    /**
     * Check the connection is valid.
     */
    public function check(): bool;

    /**
     * Close the connection.
     */
    public function close(): bool;

    /**
     * Release the connection to pool.
     */
    public function release(): void;
}