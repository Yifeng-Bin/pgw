<?php
defined('THINK_PATH') or exit();
return array(
    'DEFAULT_MODULE'        =>  'Admin',  // 默认模块
    'SESSION_OPTIONS'       =>  array(
        'prefix' => 'sadmin_',
        'name' => 's_a_id',
        'path' => RUNTIME_PATH.'Session',
        'expire' => '86400',
    ),
    //超级管理员id,拥有全部权限,只要用户uid在这个角色组里的,就跳出认证.可以设置多个值,如array('1','2','3')
    'ADMINISTRATOR' => array('1'),    
    'URL_MODEL'             =>  1,  // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式    
    
    //提示跳转模板
    'TMPL_ACTION_SUCCESS' => 'Public:dispatch_jump',   
    'TMPL_ACTION_ERROR' => 'Public:dispatch_jump',
    
    'AUTH_CONFIG'=>array(
        'AUTH_USER'         => C('DB_PREFIX').'admin_user', 
        'AUTH_GROUP'        => C('DB_PREFIX').'admin_auth_group',        // 用户组数据表名
        'AUTH_GROUP_ACCESS' => C('DB_PREFIX').'admin_auth_group_access', // 用户-用户组关系表
        'AUTH_RULE'         => C('DB_PREFIX').'admin_auth_rule',         // 权限规则表    
    ),
    
    'DB_DEBUG' => false, // 数据库调试模式 开启后可以记录SQL日志
    'SHOW_PAGE_TRACE' => false,
    'VAR_PAGE' => 'p',
    
    'DEFAULT_FILTER'        =>  '', // 默认参数过滤方法 用于I函数...
);