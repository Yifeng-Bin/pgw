<?php
defined('THINK_PATH') or exit();
return array(	    //'配置项'=>'配置值'		'DB_TYPE'=>'mysql',  	 //设置数据库类型		'DB_HOST' => 'localhost',//设置主机		'DB_USER' => 'root',     //设置用户名		'DB_PWD' => '',          //设置密码		'DB_PORT' => '3306',     //设置端口号 		'DB_NAME' => 'newpgw',  //设置数据库名		'DB_PREFIX' => 'pgw_',  //设置表前缀				'URL_MODEL'             =>  3,				'TMPL_PARSE_STRING' => array(        '__ROOT__' => __ROOT__, // 当前网站地址        '__APP__' => __APP__, // 当前应用地址        '__MODULE__' => __MODULE__,        '__ACTION__' => __ACTION__, // 当前操作地址        '__SELF__' => __SELF__, // 当前页面地址        '__CONTROLLER__' => __CONTROLLER__,        '__URL__' => __CONTROLLER__,        '__PUBLIC__' => __ROOT__, // 站点公共目录                '__STATICS__' => __ROOT__ . '/Statics',		),		'URL_ROUTER_ON'=>true,  //开启路由		'URL_PARAMS_BIND'       =>  false, // URL变量绑定到Action方法参数				//'URL_ROUTE_RULES'=>array(      //静态路由地址  		//	'/g/(\d+)/'=>'/a/down/id/$1', //资源url		//),			  /*'MODULE_ALLOW_LIST'    =>    array('Home','Admin','Back','A','M','Data','ce'),		'DEFAULT_MODULE'        =>    'A',  // 默认模块		'DEFAULT_CONTROLLER'    =>  'A', // 默认控制器名称		'DEFAULT_ACTION'        =>  'index', // 默认操作名称		'APP_SUB_DOMAIN_DEPLOY'   =>    1, // 开启子域名或者IP配置		'APP_SUB_DOMAIN_RULES'    =>    array(   		 	'back.shouyouzuan.com'  => 'back',   // back.shouyouzuan.com域名指向Admin模块		 	'a.shouyouzuan.com'  => 'a',         // a.shouyouzuan.com域名指向Admin模块		 	'm.shouyouzuan.com'   => 'm',        // m.shouyouzuan.com域名指向Test模块			'data.shouyouzuan.com'  => 'data', 			'api.shouyouzuan.com'  => 'api', 			'tui.shouyouzuan.com'  => 'tui', 			'd.shouyouzuan.com'  => 'd',			'updata.shouyouzuan.com'  => 'updata',			'ce.shouyouzuan.com'  => 'ce',		),		'TMPL_ACTION_SUCCESS' => 'Public:success',  //成功时跳转页面		'TMPL_ACTION_ERROR' => 'Public:error', 		//失败时跳转页面		'SESSION_AUTO_START' => true,			'TMPL_L_DELIM'=>'<{',	//修改左定界符		'TMPL_R_DELIM'=>'}>', 	//修改右定界符		'APP_DEBUG' => false,		'DB_LIKE_FIELDS' => 'title|content',		'DEFAULT_TIMEZONE'      => 'PRC',				//'SHOW_PAGE_TRACE'=>true,//开启页面Trace				//'TMPL_TEMPLATE_SUFFIX'=>'.html',//更改模板文件后缀名				//'TMPL_FILE_DEPR'=>'_',//修改模板文件目录层次				//'DEFAULT_THEHE'='my', //默认模板主题				//'TMPL_DETECT_THEME'=>true, //自动侦测模板主题				//'THEME_LIST'=>'your,my', //支持的模板主题列表				'TMPL_PARSE_STRING'=>array(           //添加自己的模板变量规则			'__CSS__'=>__ROOT__.'/Public/Css',			'__JS__'=>__ROOT__.'/Public/Js',			'__UPLOAD__'=>__ROOT__.'/Public/Uploads',			'__IMG__'=>__ROOT__.'/Public/Images'		),				//'URL_MODULE_MAP'    =>   array('test'=>'admin.php'),				'LAYOUT_ON'=>true, 			 //开启模板渲染		'LAYOUT_NAME'=>'layout',	 //设置布局入口文件名LAYOUT_NAME				'URL_CASE_INSENSITIVE'=>true,//url不区分大小写				'URL_HTML_SUFFIX'=>'.html',  //限制伪静态的后缀  HTML_FILE_SUFFIX' => '.html'				//'URL_ROUTER_ON'=>true,  //开启路由				//'URL_ROUTE_RULES'=>array(      //静态路由地址  		//	'/g/(\d+)/'=>'/a/down/id/$1', //资源url		//),				'URL_MODEL'         	=>  '2', 	//REWRITE模式		'URL_PATHINFO_DEPR'     =>  '/',	//更改URL的分隔符				'LOG_RECORD'            =>  true,   // 默认不记录日志		'LOG_TYPE'              =>  'File', // 日志记录类型 默认为文件方式		'LOG_LEVEL'             =>  'EMERG,ALERT,CRIT,ERR',// 允许记录的日志级别		'LOG_EXCEPTION_RECORD'  =>  true,    // 是否记录异常信息日志		*/
);