<?php
// crm入口文件
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
define('APP_NAME','crm.zhuniu.com');
define('APP_DEBUG',true);
define('APP_PATH','./Application/');
$rootPath     = dirname(dirname(__FILE__));
$runtime_Path = $rootPath . '/dynamic/runtime/'; //定义目录
$upload_paths = $rootPath.'/dynamic';

define('RUNTIME_PATH', $runtime_Path . APP_NAME . '/'); //定义缓存目录
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';