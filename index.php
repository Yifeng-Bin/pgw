<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用入口文件
// 检测PHP环境
//ini_set('display_errors','1');
//error_reporting(E_ALL);

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);
// 定义应用目录
define('APP_PATH','./Application/');
//定义thinkphp路径
define('THINK_PATH',realpath('./ThinkPHP').'/');
// 定义应用目录
define('RUNTIME_PATH', realpath('./Runtime').'/');
//绑定模块名
define('BIND_MODULE', 'Index');
//定义存储类型
define('STORAGE_TYPE',   'File');
// 应用模式 普通模式 
define('APP_MODE',       'common');
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单
