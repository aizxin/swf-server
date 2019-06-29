<?php
/**
 * FileName: Http.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-27 13:06
 */

namespace swf;

/**
 * Class Http
 * @package swf
 * Author: kong | <iwhero@yeah.com>
 */

class Http extends Server
{
    protected $app;
    protected $appPath;
    protected $table;
    protected $cachetable;
    protected $server_type;
    protected $lastMtime;
    protected $swoole;
    protected $config = [];
    protected $fieldType = [
        'int'    => Table::TYPE_INT,
        'string' => Table::TYPE_STRING,
        'float'  => Table::TYPE_FLOAT,
    ];

    protected $fieldSize = [
        Table::TYPE_INT    => 4,
        Table::TYPE_STRING => 32,
        Table::TYPE_FLOAT  => 8,
    ];

    /**
     * 架构函数
     * @access public
     */
    public function __construct($config = [])
    {
        $this->config = array_merge($this->config,$config);
    }

    /**
     * @param array $config
     *
     * @return $this
     */
    public function setConfig($config = [])
    {
        $this->config = array_merge($this->config,$config);
        return $this;
    }

    /**
     * @return mixed
     * @author: kong | <iwhero@yeah.com>
     * @date  : 2019-06-29 15:59
     */
    public function getSwoole()
    {
//        $this->init();
        return $this->config;;
    }

    /**
     * @return HttpServer
     */
    private function init($host = '0.0.0.0', $port = 9501, $mode = SWOOLE_PROCESS, $sockType = SWOOLE_SOCK_TCP)
    {
        $this->server_type = $this->config['server_type'];
        $host = $this->config['host'] ?? $host;
        $port = $this->config['port'] ?? $port;
        $mode = $this->config['mode'] ?? $mode;
        $sockType = $this->config['sockType'] ?? $sockType;
        switch ($this->server_type) {
            case 'websocket':
                $this->swoole = new WebSocketServer($host, $port, $mode, $sockType);
                break;
            default:
                $this->swoole = new HttpServer($host, $port, $mode, $sockType);
        }
        $this->option($this->config);
    }
    
    private function table(array $option)
    {
        $size        = !empty($option['size']) ? $option['size'] : 1024;
        $this->table = new Table($size);

        foreach ($option['column'] as $field => $type) {
            $length = null;

            if (is_array($type)) {
                list($type, $length) = $type;
            }

            if (isset($this->fieldType[$type])) {
                $type = $this->fieldType[$type];
            }

            $this->table->column($field, $type, isset($length) ? $length : $this->fieldSize[$type]);
        }
        $this->table->create();
    }

    private function option(array $option)
    {
        // 设置参数
        if (!empty($option)) {
            $this->swoole->set($option);
        }

        foreach ($this->event as $event) {
            // 自定义回调
            if (!empty($option[$event])) {
                $this->swoole->on($event, $option[$event]);
            } elseif (method_exists($this, 'on' . $event)) {
                $this->swoole->on($event, [$this, 'on' . $event]);
            }
        }
    }

    /**
     * 此事件在Worker进程/Task进程启动时发生,这里创建的对象可以在进程生命周期内使用
     *
     * @param $server
     * @param $worker_id
     */
    private function onWorkerStart($server, $worker_id)
    {
        // 应用实例化
        $this->app = new App();
        $this->lastMtime = time();

        $this->initServer($server, $worker_id);

        //只在一个进程内执行定时任务
        if (0 == $worker_id) {
            $this->timer($server);
        }
    }

    /**
     * 自定义初始化Swoole
     * @param $server
     * @param $worker_id
     */
    private function initServer($server, $worker_id)
    {
//        $wokerStart = Config::get('swoole.wokerstart');
//        if ($wokerStart) {
//            if (is_string($wokerStart) && class_exists($wokerStart)) {
//                $obj = new $wokerStart($server, $worker_id);
//                $obj->run();
//                unset($obj);
//            } elseif ($wokerStart instanceof \Closure) {
//                $wokerStart($server, $worker_id);
//            }
//        }
    }

    /**
     * 系统定时器
     *
     * @param $server
     */
    private function timer($server)
    {

    }

    /**
     * request回调
     * @param $request
     * @param $response
     */
    private function onRequest($request, $response)
    {
        // 执行应用并响应
        $this->app->swooleRequest($request, $response);
    }

    /**
     * onOpen回调
     * @param $server
     * @param $frame
     */
    private function onOpen($server, $request)
    {
        // 执行应用并响应
        $this->app->swooleOpen($server, $request);
    }

    /**
     * Message回调
     * @param $server
     * @param $frame
     */
    private function onMessage($server, $frame)
    {
        // 执行应用并响应
        $this->app->swooleWebSocket($server, $frame);
    }

    /**
     * Close回调
     * @param $server
     * @param $frame
     */
    private function onClose($server, $fd)
    {
        // 执行应用并响应
        $this->app->swooleClose($server, $fd);
    }

    /**
     * 任务投递
     * @param HttpServer $serv
     * @param $task_id
     * @param $fromWorkerId
     * @param $data
     * @return mixed|null
     */
    private function onTask($serv, $task_id, $fromWorkerId, $data)
    {
        if (is_string($data) && class_exists($data)) {
            $taskObj = new $data;
            if (method_exists($taskObj, 'run')) {
                $taskObj->run($serv, $task_id, $fromWorkerId);
                unset($taskObj);
                return true;
            }
        }

        if (is_object($data) && method_exists($data, 'run')) {
            $data->run($serv, $task_id, $fromWorkerId);
            unset($data);
            return true;
        }

        if ($data instanceof SuperClosure) {
            return $data($serv, $task_id, $data);
        } else {
            $serv->finish($data);
        }
    }

    /**
     * 任务结束，如果有自定义任务结束回调方法则不会触发该方法
     * @param HttpServer $serv
     * @param $task_id
     * @param $data
     */
    private function onFinish($serv, $task_id, $data)
    {
        if ($data instanceof SuperClosure) {
            $data($serv, $task_id, $data);
        }
    }
}