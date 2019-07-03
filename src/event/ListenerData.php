<?php
/**
 * FileName: ListenerData.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-02 22:48
 */

namespace swf\event;


class ListenerData
{
    /**
     * @var string
     */
    public $event;

    /**
     * @var callable
     */
    public $listener;

    /**
     * @var int
     */
    public $priority;

    public function __construct(string $event, callable $listener, int $priority)
    {
        $this->event = $event;
        $this->listener = $listener;
        $this->priority = $priority;
    }
}