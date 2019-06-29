#!/usr/bin/env php
<?php

require 'vendor/autoload.php';

use swf\SwfRun;
use Yaf\Config\Ini;

define('APP_PATH', dirname(__DIR__));
//错误信息将写入swoole日志中
error_reporting(-1);
ini_set('display_errors', 1);

$config = (new Ini(APP_PATH . "/conf/application.ini",ini_get('yaf.environ')))->toArray();

(new SwfRun($config))->run(array_shift($argv));