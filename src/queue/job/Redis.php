<?php
/**
 * FileName: Redis.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-05 23:38
 */

namespace swf\queue\job;

use swf\queue\Job;
use swf\queue\connector\Redis as RedisQueue;

class Redis extends Job
{

    /**
     * The redis queue instance.
     * @var RedisQueue
     */
    protected $redis;

    /**
     * The database job payload.
     * @var Object
     */
    protected $job;

    public function __construct(RedisQueue $redis, $job, $queue)
    {
        $this->job   = $job;
        $this->queue = $queue;
        $this->redis = $redis;
    }

    /**
     * Fire the job.
     * @return void
     */
    public function fire()
    {
        $this->resolveAndFire(json_decode($this->getRawBody(), true));
    }

    /**
     * Get the number of times the job has been attempted.
     * @return int
     */
    public function attempts()
    {
        return json_decode($this->job, true)['attempts'];
    }

    /**
     * Get the raw body string for the job.
     * @return string
     */
    public function getRawBody()
    {
        return $this->job;
    }

    /**
     * 删除任务
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();

        $this->redis->deleteReserved($this->queue, $this->job);
    }

    /**
     * 重新发布任务
     *
     * @param  int $delay
     * @return void
     */
    public function release($delay = 0)
    {
        parent::release($delay);

        $this->delete();

        $this->redis->release($this->queue, $this->job, $delay, $this->attempts() + 1);
    }
}
