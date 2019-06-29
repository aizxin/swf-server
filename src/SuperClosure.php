<?php
/**
 * FileName: SuperClosure.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-29 11:43
 */

namespace swf;


use think\Container;

class SuperClosure
{
    private $closure;
    private $serialized;

    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    final public function __sleep()
    {
        $serializer = new Serializer();
        $this->serialized = $serializer->serialize($this->closure);
        unset($this->closure);

        return ['serialized'];
    }

    final public function __wakeup()
    {
        $serializer = new Serializer();
        $this->closure = $serializer->unserialize($this->serialized);
    }

    final public function __invoke(...$args)
    {
        return Container::getInstance()->invokeFunction($this->closure, $args);
    }

    final public function call(...$args)
    {
        return Container::getInstance()->invokeFunction($this->closure, $args);
    }
}