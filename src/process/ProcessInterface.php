<?php
/**
 * FileName: ProcessInterface.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 21:38
 */

namespace swf\process;

use Swoole\Server;

interface ProcessInterface
{
    /**
     * Create the process object according to process number and bind to server.
     */
    public function bind(Server $server): void;

    /**
     * Determine if the process should start ?
     */
    public function isEnable(): bool;

    /**
     * The logical of process will place in here.
     */
    public function handle(): void;
}