<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="<?php echo ($baseurl); ?>favicon.ico" />
    <title><?php echo ($pageHeadInfo["title"]); ?></title>
    <meta name="keywords" content="<?php echo ($pageHeadInfo["keywords"]); ?>">
    <meta name="description" content="<?php echo ($pageHeadInfo["description"]); ?>">

    <!-- Bootstrap -->
    <link href="http://localhost/pgw/themes/default/bootstrap/3.3.5/css/bootstrap.min.css" type="text/css" rel="stylesheet">
    <link href="http://localhost/pgw/themes/default/css/style.css?v=20160920001" type="text/css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="__STATICS__/js/html5shiv.min.js"></script>
            <script src="__STATICS__/js/respond.min.js"></script>        
            <link href="__STATICS__/js/respond-proxy.html" id="respond-proxy" rel="respond-proxy" />
            <link href="<?php echo ($baseurl); ?>cross-domain/respond.proxy.gif" id="respond-redirect" rel="respond-redirect" />
            <script src="<?php echo ($baseurl); ?>cross-domain/respond.proxy.js"></script>        
        <![endif]-->
  </head>
  <body>
    	<header>
    	<div class="header_top">
        	<div class="container">
            	<div class="row">
                	<div class="col-lg-3 col-md-3 col-sm-3 text-left">
                            <?php if(USER_ID == 0): ?><img src="./images/header/login_off.png"> <a href="<?php echo U('User/login');?>">登录</a> | <a href="<?php echo U('User/register');?>">注册</a>
                            <?php else: ?>
                                <span><a href="<?php echo U('User/index');?>"><?php echo (htmlspecialchars($userInfo["user_name"])); ?></a></span><a href="<?php echo U('User/logout?code='.session('xss_code'));?>"> &nbsp;退出</a><?php endif; ?>
                        </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-lg-offset-3 col-md-offset-3 col-sm-offset-2 text-right"><a href="<?php echo U('Requirement/submit');?>">发布需求</a></div>
                    <div class="col-lg-1 col-md-1 col-sm-1 text-right"><a href="<?php echo U('User/order');?>">我的预约</a></div>
	<div class="col-lg-1 col-md-1 col-sm-1 text-right"><a href="<?php echo U('House/zhaoshang');?>">分站加盟</a></div>
                    <div class="col-lg-3 col-md-3 col-sm-3 text-right"><img src="./images/header/tel.png"> 服务热线：<span><?php if(empty($tel)): echo C('service_phone'); else: echo (htmlspecialchars($tel["data_text"])); endif; ?></span></div>
                </div>
            </div>
        </div>
        <div class="header_content">
        	<div class="container">
            	<div class="row">
                	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                    	<div class="header_logo">
                        	<a href="<?php echo ($baseurl); ?>"><img src="./images/header/logo.png" width="100%"></a>
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                    	<div class="header_address">
                        	<img src="./images/header/add.png"> <?php echo ($regionInfo["region_name"]); ?> <br /><a href="<?php echo U('City/change');?>">[切换城市]</a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-5 col-xs-5">
                    	<div class="header_search">
                        	<form id="search_form" action="<?php echo U('Search/worker');?>" method="get">
                                <div class="search_left">
                                    <select class="group_gray" onChange="$('#search_form').attr('action',$(this).val())">
                                      <option value="<?php echo U('Search/worker');?>">找工人</option> 
                                      <option value="<?php echo U('Search/teach');?>">派工教学</option>  
                                    </select>
                                </div>
                                <div class="search_middle">
                                    <input type="text" class="in_green" name="keywords">
                                </div>
                                <div class="search_right">
                                    <button type="submit" class="btn_green">搜 索</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-lg-offset-1 col-xs-2">
                    	<div class="header_advert">
                        	<img src="./images/header/guanggao.png" width="100%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav>
        	<div class="container">
            	<div class="row">
                	<div class="col-lg-10 col-md-10 col-sm-10">
                        <div class="nav">
                        <!--导航条-->
                            <ul class="nav-main">
                                <li><a href="<?php echo ($baseurl); ?>">首页</a></li>
                                <li id="li-1"><a href="<?php echo U('Requirement/index');?>">需求大厅</a></li>
                                <li id="li-2"><a href="#">找工人<span class="caret"></span></a></li>
                                <li id="li-5"><a href="<?php echo U('House/my');?>">装修套餐</a></li>
								<li id="li-3"><a href="#">找效果图</a></li>
								 <li id="li-4"><a href="<?php echo U('Photo/index');?>">派工学堂<span class="caret"></span></a></li>
								
                               
                            </ul>
                            <!--隐藏盒子-->
                            <div id="box-2" class="hidden-box hidden-loc-us">
                                <ul>
                                    <li><a href="<?php echo U('Worker/type?id=35');?>">工程管理</a></li>
                                    <li><a href="<?php echo U('Worker/type?id=36');?>">装修工匠</a></li>
                                    <li><a href="<?php echo U('Worker/type?id=40');?>">家居维修</a></li>
                                    <li><a href="<?php echo U('Worker/type?id=42');?>">成品安装</a></li>
                                    <li><a href="<?php echo U('Worker/type?id=43');?>">杂工搬运</a></li>
                                </ul>
                            </div>
                            <div id="box-4" class="hidden-box hidden-loc-info box04" style="left:65.5%;">
                                <ul>
                                    <li><a href="<?php echo U('Teach/index');?>">装修课堂</a></li>
                                    <li><a href="<?php echo U('Ask/index');?>">知识问答</a></li>
                                    <li><a href="<?php echo U('Diary/index');?>">装修日志</a></li>
                                </ul>
                            </div>
                        </div>
                	</div>
                    <div class="col-lg-2 col-md-2 col-sm-2">
                    	<div class="need">
                            <div class="need_pic"><img src="./images/header/sanjiao.png"></div>
                            <div class="need_text"><a href="<?php echo U('Requirement/submit');?>">发布需求</a></div>
                    	</div>
                    </div>
            	</div>
            </div>
        </nav>	
    </header>
  <div class="city-change">
  	<div class="container">
    	<div class="city-in"><a type="button" class="btn_city" href="http://beihuqu.city.58pgw.com">点击进入北湖区</a></div>
        <div class="city_content">
            <div class="row">
                <?php if(is_array($regionList)): foreach($regionList as $key=>$regionInfo): ?><div class="col-lg-1 col-md-1 col-sm-1"><span><?php echo (htmlspecialchars($regionInfo["region_name"])); ?></span></div>
                    <div class="col-lg-11 col-md-11 col-sm-11">
                        <?php if(is_array($regionInfo["list"])): foreach($regionInfo["list"] as $key=>$child): ?><div class="col-md-1 col-md-1 col-sm-1"><a class="active" href="<?php echo ($child["url"]); ?>"><?php echo (htmlspecialchars($child["region_name"])); ?></a></div><?php endforeach; endif; ?>
                    </div><?php endforeach; endif; ?>

            </div>
        </div>
    </div>
  </div>
   
  <footer>
  	<div class="footer_content">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-8 col-md-8 col-sm-8 col-md-offset-2 col-sm-offset-2 col-lg-offset-2">
                    <?php echo W('Common/bottomHelp');?>
                </div>
            </div>
        </div>
    </div>
    <div class="footer_copyright">
    	<div class="container">
        	<div class="foot_copyright_text">Copyright2012-2015 All Rights Reserved ICP备案：湘ICP备14014275号</div>
            <div class="foot_copyright_icon">
                <img src="./images/footer/foot_icon_1.png" width="67"> 
                <img src="./images/footer/foot_icon_2.png" width="69"> 
                <img src="./images/footer/foot_icon_3.png" width="20">
                <script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1259797645'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s11.cnzz.com/z_stat.php%3Fid%3D1259797645%26show%3Dpic1' type='text/javascript'%3E%3C/script%3E"));</script>
            </div>
        </div>
    </div>	
 </footer>	
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="./jquery/jquery-1.12.3.min.js"></script>
    <script src="./js/main.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="./bootstrap/3.3.5/js/bootstrap.min.js"></script>
  </body>
</html>