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
            $this->errorException($e);
        } catch (\Exception $e) {
            $this->errorException($e);
        } catch (\Throwable $e) {
            $this->errorException($e);
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
        try {
            $this->yafApp->getDispatcher()->dispatch($request);
        } catch (\Yaf\Exception $e) {
            $this->errorException($e);
        }  catch (\Exception $e) {
            $this->errorException($e);
        } catch (\Throwable $e) {
            $request->setParam('error',$e);
            $this->errorException($e);
        }
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
        try {
            $this->yafApp->getDispatcher()->dispatch($request);
        } catch (\Yaf\Exception $e) {
            $this->errorException($e);
        }  catch (\Exception $e) {
            $this->errorException($e);
        } catch (\Throwable $e) {
            $this->errorException($e);
        }
    }

    /**
     * 错误 处理
     * @param $e
     *
     * @author: kong | <iwhero@yeah.com>
     * @date  : 2019-07-02 22:17
     */
    private function errorException($e)
    {
        $request = new Http('error/error', '/');
        $request->setParam('error',$e);
        $this->yafApp->getDispatcher()->dispatch($request);
    }
}