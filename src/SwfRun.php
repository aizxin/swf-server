<?php
/**
 * FileName: SwfRun.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-29 23:59
 */

namespace swf;


use Swoole\Process;

class SwfRun
{
    protected $config;
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function run($argv)
    {
        $action = isset($argv[1]) ? $argv[1] : '';
        $d = isset($argv[2]) ? $argv[2] : '';
        if ($d){
            $this->config['swoole']['daemonize'] = true;
        }
        switch ($action) {
            case 'start':
                $this->start($action);
                break;
            case 'stop':
                $this->stop($action);
                break;
            case 'reload':
                $this->reload($action);
                break;
            case 'restart':
                $this->restart();
                break;
            default:
                echo 'No Swoole Server'.PHP_EOL;
                break;
        }
    }
    
    private function start($action)
    {
        $this->opCacheClear();
        if ($this->isRunning($this->getMasterPid())){
            echo 'Swoole http server process is already running.'.PHP_EOL;
            return false;
        }
        echo $this->echoStr($action);
        $this->init()->getSwoole()->start();
    }
    
    private function restart()
    {
        $pid = $this->getMasterPid();

        if ($this->isRunning($pid)) {
            $this->stop();
        }

        $this->start();
    }
    
    private function reload($action)
    {
        $this->opCacheClear();
        $pid = $this->getMasterPid();
        if (!$this->isRunning($pid)){
            echo 'No Swoole server process running.'.PHP_EOL;
            return false;
        }
        echo $this->echoStr($action);
        Process::kill($pid, SIGUSR1);
    }

    private function stop($action = 'stop')
    {
        $pid = $this->getMasterPid();
        if (!$this->isRunning($pid)){
            echo 'No Swoole server process running.'.PHP_EOL;
            return false;
        }
        echo $this->echoStr($action);
        Process::kill($pid, SIGTERM);
        $this->removePid();
    }

    private function init(){
        return (new Http())->setConfig($this->config);
    }

    private function echoStr($action = 'start')
    {
        $str = 'Swoole Server for Yaf'.PHP_EOL;
        $str .= "Http <{$action}>: <{$this->config['swoole']['host']}:{$this->config['swoole']['port']}>".PHP_EOL;
        if (!$this->config['swoole']['daemonize']){
            $str .= 'You can exit with <info>`CTRL-C`</info>'.PHP_EOL;
        }
        return $str;
    }

    private function opCacheClear()
    {
        if (function_exists('apc_clear_cache')) {
            apc_clear_cache();
        }
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }
    
    private function getMasterPid()
    {
        $pidFile = $this->config['swoole']['pid_file'];

        if (is_file($pidFile)) {
            $masterPid = (int) file_get_contents($pidFile);
        } else {
            $masterPid = 0;
        }

        return $masterPid;
    }

    /**
     * 删除PID文件
     * @access protected
     * @return void
     */
    protected function removePid()
    {
        $masterPid = $this->config['swoole']['pid_file'];

        if (is_file($masterPid)) {
            unlink($masterPid);
        }
    }

    /**
     * @param $pid
     *
     * @return bool|mixed
     * @author: kong | <iwhero@yeah.com>
     * @date  : 2019-06-30 00:47
     */
    protected function isRunning($pid)
    {
        if (empty($pid)) {
            return false;
        }

        return Process::kill($pid, 0);
    }
}