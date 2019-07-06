<?php
/**
 * FileName: QueueProcess.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-06 00:05
 */

namespace swf\queue\process;


use swf\process\AbstractProcess;
use swf\queue\Worker;

class QueueProcess extends AbstractProcess
{
    public $name = 'queue_process';

    public $nums = 4;

    public function handle(): void
    {
        while (true) {
            $this->job();
        }
    }

    public function job()
    {
        $worker = new Worker();
        $worker->pop();
        unset($worker);
    }
}