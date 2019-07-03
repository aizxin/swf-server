#!/usr/bin/env php
<?php

define('APP_PATH', realpath(getcwd()));


require APP_PATH.'/vendor/autoload.php';

use swf\Swf;
use Yaf\Config\Ini;

//错误信息将写入swoole日志中
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

$config = (new Ini(APP_PATH . "/conf/application.ini",ini_get('yaf.environ')))->toArray();


(new Swf($config))->run($argv);