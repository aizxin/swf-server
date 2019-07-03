<?php
/**
 * FileName: EventDispatcher.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-02 22:54
 */

namespace swf\event;


class EventDispatcher
{
    /**
     * @var ListenerProviderInterface
     */
    private $listeners;

    public function __construct(ListenerProvider $listeners)
    {
        $this->listeners = $listeners;
    }

    /**
     * Provide all listeners with an event to process.
     *
     * @param object $event The object to process
     *
     * @return object The Event that was passed, now modified by listeners
     */
    public function dispatch(object $event)
    {
        foreach ($this->listeners->getListenersForEvent($event) as $listener) {
            $listener($event);
        }
        return $event;
    }
}