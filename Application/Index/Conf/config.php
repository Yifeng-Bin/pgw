<?php

defined('THINK_PATH') or exit();
return array(
    'DEFAULT_MODULE' => 'Index', // 默认模块
    'SESSION_TYPE' => 'Mysqli',
    'SESSION_OPTIONS' => array(
        'prefix' => 's_',
        'name' => 's_id',
        'path' => RUNTIME_PATH . 'Session',
        'expire' => '21600',
        'domain' => '.58pgw.com',
    ),
    'SESSION_EXPIRE' => '86400',
    'COOKIE_DOMAIN' => '58pgw.com',
    'COOKIE_HTTPONLY' => true,
    
    'URL_MODEL' => 2, // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式    
    'VIEW_PATH' => 'themes/',
    'DEFAULT_THEME'    =>    'default',

    'TMPL_ACTION_SUCCESS' => 'Public:dispatch_jump',
    'TMPL_ACTION_ERROR' => 'Public:dispatch_jump',
    
    'MODULE_DENY_LIST'      =>  array('Common','Runtime','Api'),

    'TAGLIB_PRE_LOAD' => 'Index\\Tag\\Sc',
    
    'TMPL_FILE_DEPR'        =>  '-', //模板文件CONTROLLER_NAME与ACTION_NAME之间的分割符
    'URL_PATHINFO_DEPR'     =>  '/',	// PATHINFO模式下，各参数之间的分割符号
    'URL_HTML_SUFFIX'       =>  '',  // URL伪静态后缀设置
    
    'VAR_PAGE' => 'page',
    
    'STATIC_VAR' => '2016-07-02',
    'STATIC_CACHE_URL' => 'http://cdn-cache.res.58pgw.com',
    
    'SHOW_PAGE_TRACE' => false,
    'DB_DEBUG' => false, // 数据库调试模式 开启后可以记录SQL日志
    
);
