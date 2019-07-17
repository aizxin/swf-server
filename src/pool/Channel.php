<?php
/**
 * FileName: Channel.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 11:41
 */

namespace swf\pool;
use Swoole\Coroutine\Channel as CoChannel;

class Channel
{
    protected $size;

    /**
     * @var CoChannel
     */
    protected $channel;

    /**
     * @var \SplQueue
     */
    protected $queue;

    public function __construct(int $size)
    {
        $this->size = $size;
        $this->channel = new CoChannel($size);
        $this->queue = new \SplQueue();
    }

    public function pop(float $timeout)
    {
        if ($this->isCoroutine()) {
            return $this->channel->pop($timeout);
        }
        return $this->queue->shift();
    }

    public function push($data)
    {
        if ($this->isCoroutine()) {
            return $this->channel->push($data);
        }
        return $this->queue->push($data);
    }

    public function length(): int
    {
        if ($this->isCoroutine()) {
            return $this->channel->length();
        }
        return $this->queue->count();
    }

    protected function isCoroutine(): bool
    {
        return \Swoole\Coroutine::getCid();
    }

    /**
     * @return CoChannel
     */
    public function stats()
    {
        if ($this->isCoroutine()) {
            return $this->channel->stats();
        }
        return $this->queue;
    }

    /**
     * @return CoChannel
     */
    public function getChannel()
    {
        if ($this->isCoroutine()) {
            return $this->channel;
        }
        return $this->queue;
    }
}