<?php
/**
 * FileName: Config.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 12:03
 */

namespace swf\pool;


class Config
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var callable
     */
    public $callback;

    /**
     * @var array
     */
    public $option;

    public function __construct(string $name, callable $callback, array $option)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->option = $option;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Config
    {
        $this->name = $name;
        return $this;
    }

    public function getCallback(): callable
    {
        return $this->callback;
    }

    public function setCallback(callable $callback): Config
    {
        $this->callback = $callback;
        return $this;
    }

    public function getOption(): array
    {
        return $this->option;
    }

    public function setOption(array $option): Config
    {
        $this->option = $option;
        return $this;
    }
}