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

use Swoole\WebSocket\Server as WebSocketServer;
use Swoole\Http\Server as HttpServer;

use Yaf\Registry;

class Http extends Server
{
    protected $app;
    protected $lastMtime;
    protected $config = [];
    protected $yafApp;

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
        $this->option = array_merge($this->option,$this->config['swoole']);
        return $this;
    }

    /**
     * @return mixed
     * @author: kong | <iwhero@yeah.com>
     * @date  : 2019-06-29 15:59
     */
    public function getSwoole()
    {
        $this->run();
        return $this->swoole;
    }
    
    private function run()
    {
        $host = $this->option['host'] ?? $this->host;
        $port = $this->option['port'] ?? $this->port;
        $mode = $this->option['mode'] ?? $this->mode;
        $sockType = $this->option['sockType'] ?? $this->sockType;
        switch ($this->option['server_type'] ?? '') {
            case 'websocket':
                $this->swoole = new WebSocketServer($host, $port, $mode, $sockType);
                break;
            default:
                $this->swoole = new HttpServer($host, $port, $mode, $sockType);
        }
        $this->setOption($this->option);
        $this->initYafApp($this->swoole);
    }

    private function setOption($option = [])
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
     * @param $server
     */
    public function onStart($server) {
        @swoole_set_process_name("swf-server");
    }

    /**
     * 此事件在Worker进程/Task进程启动时发生,这里创建的对象可以在进程生命周期内使用
     *
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart($server, $worker_id)
    {
        $this->initServer($server, $worker_id);
        //只在一个进程内执行定时任务
        if (0 == $worker_id) {
            $this->timer($server);
        }
        $this->lastMtime = time();
    }


    /**
     * peceive回调
     * @param $server
     * @param $fd
     * @param $reactor_id
     * @param $data
     */
    public function onReceive($server, $fd, $reactor_id, $data)
    {
        // 执行应用并响应
        $this->app->swooleReceive($server, $fd, $reactor_id, $data);
    }

    
    /**
     * request回调
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response)
    {
        // 执行应用并响应
        $this->app->swooleRequest($request, $response);
    }

    /**
     * onOpen回调
     * @param $server
     * @param $frame
     */
    public function onOpen($server, $request)
    {
        // 执行应用并响应
        $this->app->swooleOpen($server, $request);
    }

    /**
     * Message回调
     * @param $server
     * @param $frame
     */
    public function onMessage($server, $frame)
    {
        // 执行应用并响应
        $this->app->swooleWebSocket($server, $frame);
    }

    /**
     * Close回调
     * @param $server
     * @param $frame
     */
    public function onClose($server, $fd)
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
    public function onTask($serv, $task_id, $fromWorkerId, $data)
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
    public function onFinish($serv, $task_id, $data)
    {
        if ($data instanceof SuperClosure) {
            $data($serv, $task_id, $data);
        }
    }

    /**
     * 自定义初始化Swoole
     * @param $server
     * @param $worker_id
     */
    private function initServer($server, $worker_id)
    {
        $wokerStart = $this->option['wokerstart'] ?? '';
        if ($wokerStart) {
            if (is_string($wokerStart) && class_exists($wokerStart)) {
                $obj = new $wokerStart($server, $worker_id);
                $obj->run();
                unset($obj);
            } elseif ($wokerStart instanceof \Closure) {
                $wokerStart($server, $worker_id);
            }
        }
    }

    /**
     * @author: kong | <iwhero@yeah.com>
     * @date  : 2019-07-03 10:19
     */
    private function initYafApp($server)
    {
        // 注入 config 和 swoole服务
        Registry::set('swoole', $server);
        Registry::set('config', $this->config);
        $this->yafApp = new \Yaf\Application($this->config);
        $this->yafApp->bootstrap();
        // 应用实例化
        $this->app = new App();
        // 注入 yaf app
        $this->app->setApp($this->yafApp,$this->config);
    }

    /**
     * 系统定时器
     *
     * @param $server
     */
    private function timer($server)
    {

    }

}