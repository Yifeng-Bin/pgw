<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

    <head>

        <meta charset="utf-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php echo (htmlspecialchars($pageHeadInfo["title"])); ?></title>

        <meta name="keywords" content="<?php echo (htmlspecialchars($pageHeadInfo["keywords"])); ?>">

        <meta name="description" content="<?php echo (htmlspecialchars($pageHeadInfo["description"])); ?>">

        <!-- Bootstrap -->

        <link href="http://localhost/pgw/themes/default/bootstrap/3.3.5/css/bootstrap.min.css" type="text/css" rel="stylesheet">

        <link href="http://localhost/pgw/themes/default/css/style.css?v=2016102701" type="text/css" rel="stylesheet">



        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->

        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

        <!--[if lt IE 9]>

            <script src="/Statics/js/html5shiv.min.js"></script>

            <script src="/Statics/js/respond.min.js"></script>        

            <link href="/Statics/js/respond-proxy.html" id="respond-proxy" rel="respond-proxy" />

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

    <div class="banner">

        <div class="container">

            <div class="row">

                <div class="col-lg-9 col-md-9 col-sm-9">

                    <div class="row">

                        <div class="col-md-12 col-sm-12" style="height:282px">

                            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">

                                <!-- Indicators -->

                                <ol class="carousel-indicators">

                                    <?php $adList = D("Ad")->getAdList(1); if(is_array($adList)): foreach($adList as $key => $adInfo): ?><li data-target="#carousel-example-generic" data-slide-to="<?php echo ($key); ?>" <?php if($key == 0): ?>class="active"<?php endif; ?>></li><?php endforeach; endif; ?>

                                </ol>

                                <!-- Wrapper for slides -->

                                <div class="carousel-inner" role="listbox">

                                    <?php $adList = D("Ad")->getAdList(1); if(is_array($adList)): foreach($adList as $key => $adInfo): ?><div <?php if($key == 0): ?>class="item active"<?php else: ?>class="item"<?php endif; ?>>

                                            <?php if($adInfo["ad_link"] != ''): ?><a href="<?php echo ($adInfo["ad_link"]); ?>"><img src="<?php echo ($adInfo["ad_image"]); ?>" /></a>

                                                <?php else: ?>

                                                <img src="<?php echo ($adInfo["ad_image"]); ?>" /><?php endif; ?>

                                            <div class="carousel-caption">

                                                <?php echo (htmlspecialchars($adInfo["ad_text"])); ?>

                                            </div>

                                        </div><?php endforeach; endif; ?>                                        

                                </div>



                                <!-- Controls -->

                                <a class="left carousel-control" style="height:282px" href="#carousel-example-generic" role="button" data-slide="prev">

                                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>

                                    <span class="sr-only">Previous</span>

                                </a>

                                <a class="right carousel-control" style="height:282px" href="#carousel-example-generic" role="button" data-slide="next">

                                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>

                                    <span class="sr-only">Next</span>

                                </a>

                            </div>

                        </div>

                        <div class="col-md-3 col-sm-3">

                            <div class="thumbnail margin_top">

                                <div class="caption">

                                    <h3>装修知识</h3>

                                    <p>装修知识全面解答</p>

                                </div>

                                <a href="<?php echo U('Teach/index');?>"><img class="img_four" src="./images/banner/knowledge.png"></a>

                            </div>

                        </div>

                        <div class="col-md-3 col-sm-3">

                            <div class="thumbnail margin_top">

                                <div class="caption">

                                    <h3>装修问答</h3>

                                    <p>装修问题全民互动</p>

                                </div>

                                <a href="<?php echo U('Ask/index');?>"><img class="img_four" src="./images/banner/question.png"></a>

                            </div>

                        </div>

                        <div class="col-md-3 col-sm-3">

                            <div class="thumbnail margin_top">

                                <div class="caption">

                                    <h3>装修日记</h3>

                                    <p>真实的装修全记录</p>

                                </div>

                                <a href="<?php echo U('Diary/index');?>"><img class="img_four" src="./images/banner/diary.png"></a>

                            </div>

                        </div>

                        <div class="col-md-3 col-sm-3">

                            <div class="thumbnail margin_top">

                                <div class="caption">

                                    <h3>装修效果图</h3>

                                    <p>让您找到更多装修灵感</p>

                                </div>

                                <a href="<?php echo U('Photo/index');?>"><img class="img_four" src="./images/banner/design.png"></a>

                            </div>

                        </div>

                    </div>

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

                    <div class="count">

                        <div class="count_content">

                            <div class="count_sub border_right border_bottom">

                                <div class="count_sub_pic"><a href="<?php echo U('index/tuliao');?>"><img src="./images/banner/count_pic_01.png" width="100%"></a></div>

                                <div class="count_sub_text"><a href="<?php echo U('index/tuliao');?>">涂料计算器</a></div>

                            </div>

                            <div class="count_sub border_right border_bottom">

                                <div class="count_sub_pic"><a href="<?php echo U('index/diban');?>"><img src="./images/banner/count_pic_02.png" width="100%"></a></div>

                                <div class="count_sub_text"><a href="<?php echo U('index/diban');?>">地板计算器</a></div>

                            </div>

                            <div class="count_sub border_bottom">

                                <div class="count_sub_pic"><a href="<?php echo U('index/bizhi');?>"><img src="./images/banner/count_pic_03.png" width="100%"></a></div>

                                <div class="count_sub_text"><a href="<?php echo U('index/bizhi');?>">壁纸计算器</a></div>

                            </div>

                            <div class="count_sub border_right">

                                <div class="count_sub_pic"><a href="<?php echo U('index/qiangzhuan');?>"><img src="./images/banner/count_pic_04.png" width="100%"></a></div>

                                <div class="count_sub_text"><a href="<?php echo U('index/qiangzhuan');?>">墙砖计算器</a></div>

                            </div>

                            <div class="count_sub border_right">

                                <div class="count_sub_pic"><a href="<?php echo U('index/chuanglian');?>"><img src="./images/banner/count_pic_05.png" width="100%"></a></div>

                                <div class="count_sub_text"><a href="<?php echo U('index/chuanglian');?>">窗帘计算器</a></div>

                            </div>

                            <div class="count_sub">

                                <div class="count_sub_pic"><a href="<?php echo U('index/dizhuan');?>"><img src="./images/banner/count_pic_06.png" width="100%"></a></div>

                                <div class="count_sub_text"><a href="<?php echo U('index/dizhuan');?>">地砖计算器</a></div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="workman">

        <div class="container">

            <div class="row">

                <div class="col-lg-9 col-md-9 col-sm-9">

                    <?php echo W('Index/workerList');?>

                </div>

                <div class="col-lg-3 col-md-3 col-sm-3">

                    <?php echo W('Index/dynamicList');?>

                </div>

            </div>

        </div>

    </div>



    <div class="xiaoguo">

        <?php echo W('Index/photoList');?>

    </div>

    <div class="teach">

        <div class="container">

            <div class="tit">派工教学<span><a href="#">more</a></span></div>

            <div class="row">

                <div class="col-lg-4 col-md-4 col-sm-4">

                    <div class="decoration_class">

                        <div class="decoration_tit"><a href="<?php echo U('Teach/index');?>">装修课堂</a></div>

                        <div class="class_content">

                            <?php echo W('Index/teachList');?>

                        </div>

                    </div>

                </div>

                <div class="col-lg-4 col-md-4 col-sm-4">

                    <div class="decoration_question">

                        <div class="decoration_tit"><a href="#">知识问答</a></div>

                        <div class="question_content">

                            <?php echo W('Index/askList');?>

                        </div>

                    </div>

                </div>

                <div class="col-lg-4 col-md-4 col-sm-4">

                    <div class="decoration_journal">

                        <div class="journal_tit"><a href="#">装修日志</a></div>

                        <div class="journal_content">

                            <?php echo W('Index/diaryList');?>

                        </div>

                    </div>

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