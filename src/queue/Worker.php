<?php
/**
 * FileName: Worker.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-05 23:55
 */

namespace swf\queue;


use Exception;

class Worker
{

    /**
     * 执行下个任务
     * @param  string $queue
     * @param  int    $delay
     * @param  int    $sleep
     * @param  int    $maxTries
     * @return array
     */
    public function pop($queue = null, $delay = 0, $sleep = 3, $maxTries = 0)
    {

        $job = $this->getNextJob($queue);

        if (!is_null($job)) {
            return $this->process($job, $maxTries, $delay);
        }

        $this->sleep($sleep);

        return ['job' => null, 'failed' => false];
    }

    /**
     * 获取下个任务
     * @param  string $queue
     * @return Job
     */
    protected function getNextJob($queue)
    {
        if (is_null($queue)) {
            return Queue::pop();
        }

        foreach (explode(',', $queue) as $queue) {
            if (!is_null($job = Queue::pop($queue))) {
                return $job;
            }
        }
    }

    /**
     * Process a given job from the queue.
     * @param \think\queue\Job $job
     * @param  int             $maxTries
     * @param  int             $delay
     * @return array
     * @throws Exception
     */
    public function process(Job $job, $maxTries = 0, $delay = 0)
    {
        if ($maxTries > 0 && $job->attempts() > $maxTries) {
            return $this->logFailedJob($job);
        }

        try {
            $job->fire();

            return ['job' => $job, 'failed' => false];
        } catch (Exception $e) {
            if (!$job->isDeleted()) {
                $job->release($delay);
            }

            throw $e;
        }
    }

    /**
     * Log a failed job into storage.
     * @param  \Think\Queue\Job $job
     * @return array
     */
    protected function logFailedJob(Job $job)
    {
        if (!$job->isDeleted()) {
            try {
                $job->delete();
                $job->failed();
            } finally {
                Hook::listen('queue_failed', $job);
            }
        }

        return ['job' => $job, 'failed' => true];
    }

    /**
     * Sleep the script for a given number of seconds.
     * @param  int $seconds
     * @return void
     */
    public function sleep($seconds)
    {
        sleep($seconds);
    }

}