<?php
/**
 * FileName: App.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-29 12:18
 */

namespace swf;

use Yaf\Registry;
use Yaf\Request\Http;

class App
{
    protected $yafApp;
    protected $config;

    public function setApp($yafApp,$config)
    {
        $this->yafApp = $yafApp;
        $this->config = $config;
    }
    
    /**
     * request回调
     * @param $request
     * @param $response
     */
    public function swooleReceive($server, $fd, $reactor_id, $data)
    {

    }
    
    /**
     * request回调
     * @param $request
     * @param $response
     */
    public function swooleRequest($request, $response)
    {
        //请求过滤,会请求2次
        if(in_array('/favicon.ico', [$request->server['path_info'],$request->server['request_uri']])){
            return $response->end();
        }

        Registry::set('request', $request);
        Registry::set('response', $response);

        ob_start();
        $yafRequest = new Http($request->server['request_uri'],'/');

        try {
            $this->yafApp->getDispatcher()->dispatch($yafRequest);
        } catch (\Yaf\Exception $e ) {
            $yafRequest->setParam('error',$e);
            $yafRequest->setControllerName($this->config['http']['controllerName'] ?? 'error');
            $yafRequest->setActionName($this->config['http']['actionName'] ?? 'error');
            $this->yafApp->getDispatcher()->dispatch($yafRequest);
        } catch (\Exception $e) {
            $yafRequest->setParam('error',$e);
            $yafRequest->setControllerName($this->config['http']['controllerName'] ?? 'error');
            $yafRequest->setActionName($this->config['http']['actionName'] ?? 'error');
            $this->yafApp->getDispatcher()->dispatch($yafRequest);
        } catch (\Throwable $e) {
            $yafRequest->setParam('error',$e);
            $yafRequest->setControllerName($this->config['http']['controllerName'] ?? 'error');
            $yafRequest->setActionName($this->config['http']['actionName'] ?? 'error');
            $this->yafApp->getDispatcher()->dispatch($yafRequest);
        }
        $result = ob_get_contents();
        ob_end_clean();
        // add Header
        $response->header('Content-Type', 'application/json; charset=utf-8');
        // add cookies
        // set status
        $response->end($result);
    }

    /**
     * onOpen回调
     * @param $server
     * @param $frame
     */
    public function onOpen($server, $request)
    {
        
    }

    /**
     * Message回调
     * @param $server
     * @param $frame
     */
    public function onMessage($server, $frame)
    {
        $data = json_decode($frame->data, true);
        $request = new Http($data['uri'], '/');
        $request->setParam('param',[
            'fd' => $frame->fd,
            'data' => $data
        ]);
        ob_start();
        try {
            $this->yafApp->getDispatcher()->dispatch($request);
        } catch (\Yaf\Exception $e) {
            $request->setParam('error',$e);
            $request->setModuleName($this->config['websocket']['moduleName'] ?? 'error');
            $request->setControllerName($this->config['websocket']['controllerName'] ?? 'error');
            $request->setActionName($this->config['websocket']['actionName'] ?? 'error');
            $this->yafApp->getDispatcher()->dispatch($request);
        }  catch (\Exception $e) {
            $request->setParam('error',$e);
            $request->setModuleName($this->config['websocket']['moduleName'] ?? 'error');
            $request->setControllerName($this->config['websocket']['controllerName'] ?? 'error');
            $request->setActionName($this->config['websocket']['actionName'] ?? 'error');
            $this->yafApp->getDispatcher()->dispatch($request);
        } catch (\Throwable $e) {
            $request->setParam('error',$e);
            $request->setModuleName($this->config['websocket']['moduleName'] ?? 'error');
            $request->setControllerName($this->config['websocket']['controllerName'] ?? 'error');
            $request->setActionName($this->config['websocket']['actionName'] ?? 'error');
            $this->yafApp->getDispatcher()->dispatch($request);
        }
        ob_end_clean();
    }

    /**
     * Close回调
     * @param $server
     * @param $frame
     */
    public function swooleClose($server, $fd)
    {
        $request = new Http('close/index', '/');
        $request->setParam('fd',[
            'fd' => $fd,
            'server' => $server
        ]);
        ob_start();
        try {
            $this->yafApp->getDispatcher()->dispatch($request);
        } catch (\Yaf\Exception $e) {
            $this->yafApp->getDispatcher()->dispatch($request);
        }  catch (\Exception $e) {
            $this->yafApp->getDispatcher()->dispatch($request);
        } catch (\Throwable $e) {
            $this->yafApp->getDispatcher()->dispatch($request);
        }
        ob_end_clean();
    }
}