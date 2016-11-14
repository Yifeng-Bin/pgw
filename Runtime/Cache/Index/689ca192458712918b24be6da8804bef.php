<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="<?php echo ($baseurl); ?>favicon.ico" />
    <title><?php echo (htmlspecialchars($pageHeadInfo["title"])); ?></title>
    <meta name="keywords" content="<?php echo (htmlspecialchars($pageHeadInfo["keywords"])); ?>">
    <meta name="description" content="<?php echo (htmlspecialchars($pageHeadInfo["description"])); ?>">

    <!-- Bootstrap -->
    <link href="./bootstrap/3.3.5/css/bootstrap.min.css" type="text/css" rel="stylesheet">
    <link href="./css/style.css?v=20160704001" type="text/css" rel="stylesheet">

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
  	<div class="sub_location">
    	<div class="container">
        	<div class="location_tit">
            	您的位置： <a href="<?php echo ($baseurl); ?>">派工网</a> > <a href="<?php echo U('Requirement/index');?>">需求大厅</a>
            </div>
        </div>
    </div>
	<div class="sub">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-9 col-md-9 col-sm-9">
                	<table class="table table-striped">
                      <thead>
                        <tr>
                          <th>业主</th>
                          <th>项目名称</th>
                          <th>类型</th>
                          <th>发布时间</th>
                          <th>查看详情</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if(is_array($requirementList)): foreach($requirementList as $key=>$list): ?><tr>
                          <td><?php echo (htmlspecialchars($list["contacts"])); ?></td>
                          <td><?php echo (htmlspecialchars($list["tender_name"])); ?></td>
                          <td><?php echo ($list["type_name"]); ?></td>
                          <td class="add_time" add_time="<?php echo ($list["add_time"]); ?>"></td>
                          <td><a href="<?php echo U('Requirement/detail?id='.$list['tender_id']);?>">查看</a></td>
                        </tr><?php endforeach; endif; ?>                                                    
                      </tbody>
                    </table>
                    <div class="page"><?php echo ($pageShow); ?></div>
                </div>
                
                <div class="col-lg-3 col-md-3 col-sm-3">
                	<div class="release">
                    	<div class="sub_tit"><a href="#">马上发布需求，立即解决</a></div>
                        <div class="release_content">
                        	<div class="media">
                              <div class="media-left">
                                <a href="#">
                                  <img class="media-object" src="./images/need/need_icon_1.png" class="img-circle" width="41">
                                </a>
                              </div>
                              <div class="media-body">
                                <h4 class="media-heading">发布需求后</h4>
                                2个小时内收到服务商响应
                              </div>
                            </div>
                            <div class="media">
                              <div class="media-left">
                                <a href="#">
                                  <img class="media-object" src="./images/need/need_icon_2.png" class="img-circle" width="41">
                                </a>
                              </div>
                              <div class="media-body">
                                <h4 class="media-heading">每个需求</h4>
                                平均有2个以上工人报价
                              </div>
                            </div>
                            <div class="media">
                              <div class="media-left">
                                <a href="#">
                                  <img class="media-object" src="./images/need/need_icon_3.png" class="img-circle" width="41">
                                </a>
                              </div>
                              <div class="media-body">
                                <h4 class="media-heading">95%以上的需求</h4>
                                都得到了圆满解决
                              </div>
                            </div>
                        </div>
                    </div>
                    <?php echo W('Common/recommendWorker');?>
                    <?php echo W('Common/recommendTeach');?>
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
<script type="text/javascript">
$(function (){
        $(".add_time").html(function () {
            var d = new Date();
            var now_time = Date.parse(d);
            var now_time = parseInt(now_time / 1000) + parseInt(d.getTimezoneOffset() * 60);            
            var add_time = $(this).attr('add_time');
            var add_second = now_time - add_time ;
            if (add_second <= 0)
            {
                return "";
            } else
            {
                var __ed = parseInt(add_second / (24 * 60 * 60));
                var __eh = parseInt((add_second / 3600) % 24);
                var __em = parseInt((add_second/60)%60);
                var __es = parseInt(add_second % 60);
                var show_info = '';
                if (__ed > 0) {
                    show_info = __ed + "天前";
                } else if (__eh > 0) {
                    show_info = __eh + "小时前";
                } else if (__em > 0) {
                    show_info = __em + "分钟前";
                } else if (__es > 0) {
                    show_info = + __es + "秒前";
                }
                return show_info;
            }
        });

});

</script>    
  </body>
</html>