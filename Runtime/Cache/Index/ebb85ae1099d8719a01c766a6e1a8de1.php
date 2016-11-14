<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="././images/photoDetail/favicon.ico" />
    <title>装修套餐</title>
    <meta name="keywords" content="<?php echo ($pageHeadInfo["keywords"]); ?>">
    <meta name="description" content="<?php echo ($pageHeadInfo["description"]); ?>">

    <!-- Bootstrap -->
    <link href="./bootstrap/3.3.5/css/bootstrap.min.css" type="text/css" rel="stylesheet">
    <link href="./css/style.css?v=20161027001" type="text/css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
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
            	您的位置： <a href="<?php echo ($baseurl); ?>">派工网</a> > <a href="/house/my">装修套餐</a>
            </div>
        </div>
    </div>
	
    <div id="teach_list">
    	<div class="container">
            <div class="row">             
                <div class="col-lg-9 col-md-9 col-sm-9">
                	<div class="row">
                    	<div class="col-lg-12 col-md-12 col-sm-12">
                        	
                            <div class="screen">
                                    <div class="screen_inline">
                                        <div class="screen_inline_left">类型</div>
                                        <div class="screen_inline_right">
                                            <ul>
                                                <li><a href="javascript:;" js-select="xq"  class="on">小区套餐</a></li>
												 <li><a href="javascript:;" js-select="mj">面积套餐</a></li>
                                            </ul>
                                        </div>
                                    </div>
              						<div class="screen_inline" js-select-box="xq">
                                        <div class="screen_inline_left">区域</div>
                                        <div class="screen_inline_right">
                                            <ul>
												<li><a href="<?php echo U('House/my');?>">不限</a></li>
                                                <li><a href="<?php echo U('House/my?x=1');?>">武广新区</a></li>
												<li><a href="<?php echo U('House/my?x=2');?>">五岭版块</a></li>
												<li><a href="<?php echo U('House/my?x=3');?>">龙泉版块</a></li>
												<li><a href="<?php echo U('House/my?x=4');?>">东塔版块</a></li>
												<li><a href="<?php echo U('House/my?x=5');?>">骆仙版块</a></li>
												<li><a href="<?php echo U('House/my?x=6');?>">梨树山版块</a></li>
												<li><a href="<?php echo U('House/my?x=7');?>">爱莲湖片区</a></li>
												<li><a href="<?php echo U('House/my?x=8');?>">王仙岭版块</a></li>
												<li><a href="<?php echo U('House/my?x=9');?>">南塔版块</a></li>
												<li><a href="<?php echo U('House/my?x=10');?>">苏仙岭版块</a></li>
												<li><a href="<?php echo U('House/my?x=11');?>">北湖版块</a></li>
												<li><a href="<?php echo U('House/my?x=12');?>">下湄桥版块</a></li>
                                            </ul>
                                        </div>
                                    </div>
									<div class="screen_inline" js-select-box="mj">
										<div class="screen_inline_left" >级别</div>
										<div class="screen_inline_right" >
											<ul>
												<li><a href="javascript:;" onclick="mianji(11)" class="on" data="1">一级套餐</a></li>
												<li><a href="javascript:;" onclick="mianji(22)" data="2">二级套餐</a></li>
												<li><a href="javascript:;" onclick="mianji(33)" data="3">三级套餐</a></li>
												<li><a href="javascript:;" onclick="mianji(44)" data="4">高级套餐</a></li>
												<li><a href="javascript:;" onclick="mianji(55)" data="5">顶级套餐</a></li>
											</ul>
										</div>
									</div>
									<div class="screen_inline" js-select-box="mj">
                                        <div class="screen_inline_left">面积</div>
                                        <div class="screen_inline_right">
                                            <ul>
                                                <li><a href="javascript:;" onclick="mianji(1)" data="1">30-50m²</a></li>
												<li><a href="javascript:;" onclick="mianji(2)" data="2">50-70m²</a></li>
												<li><a href="javascript:;" onclick="mianji(3)" data="3">70-90m²</a></li>
												<li><a href="javascript:;" onclick="mianji(4)" data="4">90-120m²</a></li>
												<li><a href="javascript:;" onclick="mianji(5)" data="5">120-150m²</a></li>
												<li><a href="javascript:;" onclick="mianji(6)" data="6">150-180m²</a></li>
												<li><a href="javascript:;" onclick="mianji(7)" data="7">180-210m²</a></li>
												<li><a href="javascript:;" onclick="mianji(8)" data="8">210以上</a></li>
                                            </ul>
                                        </div>
                                    </div>
									
                            </div>
                            
                        </div>
                    </div>
                	<!--小区套餐开始-->
                    <div class="house_content" js-select-box="xq">
						 <?php if(is_array($loupan_list)): foreach($loupan_list as $key=>$info): ?><div class="house_box">
                        	<div class="hose_box_left">
                            	<img src="<?php echo ($info['thumb']); ?>">
                            </div>
                            <div class="house_box_right">
                            	<h5><a href="<?php echo U('House/detail?id='.$info['loupan_id']);?>?type=1" ><?php echo ($info['loupan_name']); ?></a><small><span><?php echo ($info['visit_time']); ?></span>人查看了该楼盘装修特餐</small></h5>
                                <p><img src="images/house/price.png">施工均价：<?php echo ($info['loupan_price']); ?>元</p>
                                <p><img src="images/house/add.png">地址：<?php echo (htmlspecialchars($info['loupan_address'])); ?></p>
                                
								<a href="<?php echo U('House/detail?id='.$info['loupan_id']);?>?type=1">成功案例</a>
								<a href="<?php echo U('House/detail?id='.$info['loupan_id']);?>?type=2">在建工程</a>
								<a href="<?php echo U('House/detail?id='.$info['loupan_id']);?>?type=3">小区活动</a>
                                <a href="<?php echo U('House/detail?id='.$info['loupan_id']);?>?type=1" class="a_btn">查看套餐</a>
                            </div>
                        </div><?php endforeach; endif; ?>
						
                    </div> 
					<!--小区套餐结束-->
					<!--活动列表开始-->
                    <ul class="activityInfo" js-select-box="mj">
						
					</ul>
					<!--活动列表结束-->
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3">
                	<div class="teach_list_count">
                        <div class="teach_list_right_tit">
                            <span>装修工具</span>
                        </div>
                        <div class="teach_count">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 bor_right bor_bottom">
								<a href="<?php echo U('index/tuliao');?>">
                                    <img src="./images/banner/count_pic_01.png" width="100%"></a>
                                    <p><a href="<?php echo U('index/tuliao');?>">涂料计算器</a></p>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 bor_right bor_bottom">
								<a href="<?php echo U('index/diban');?>">
                                    <img src="./images/banner/count_pic_02.png" width="100%">
									</a>
                                    <p><a href="<?php echo U('index/diban');?>">地板计算器</a></p>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 bor_bottom">
								<a href="<?php echo U('index/bizhi');?>">
                                    <img src="./images/banner/count_pic_03.png" width="100%">
									</a>
                                    <p><a href="<?php echo U('index/bizhi');?>">壁纸计算器</a></p>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 bor_right">
								<a href="<?php echo U('index/qiangzhuan');?>">
                                    <img src="./images/banner/count_pic_04.png" width="100%"></a>
                                    <p><a href="<?php echo U('index/qiangzhuan');?>">墙砖计算器</a></p>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 bor_right">
								<a href="<?php echo U('index/chuanglian');?>">
                                    <img src="./images/banner/count_pic_05.png" width="100%">
									</a>
                                    <p><a href="<?php echo U('index/chuanglian');?>">窗帘计算器</a></p>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
								<a href="<?php echo U('index/dizhuan');?>">
                                    <img src="./images/banner/count_pic_06.png" width="100%">
									</a>
                                    <p><a href="<?php echo U('index/dizhuan');?>">地砖计算器</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="teach_list_news">
                    	<div class="teach_list_right_tit">
                            <span>最新文章</span>
                        </div>
                        <div class="teach_news_content">
                        	<ul>
							 <?php if(is_array($addtime)): foreach($addtime as $key=>$info): ?><li><a href="<?php echo U('Teach/detail?id='.$info['article_id']);?>"><?php echo ($info['article_name']); ?></a></li><?php endforeach; endif; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="teach_list_news">
                    	<div class="teach_list_right_tit">
                            <span>最热文章</span>
                        </div>
                        <div class="teach_news_content">
                        	<ul>
								 <?php if(is_array($visittime)): foreach($visittime as $key=>$info): ?><li><a href="<?php echo U('Teach/detail?id='.$info['article_id']);?>"><?php echo ($info['article_name']); ?></a></li><?php endforeach; endif; ?>
                            </ul>
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
	<script>
	$(function(){
		$('[js-select]').click(function(){
			var name=$(this).attr('js-select');
			$('[js-select-box]').hide();
			$('[js-select-box="'+name+'"]').show();
			console.log(name);
			if(name=='mj'){
				var url = "/House/mianji";
				var region = "<?php echo $_COOKIE['region_id']; ?>"; 
				var data = {region:region};
				$.post(url,data,function(data){
					console.log(data); 
					var len = data.length; 
					for(var i=0;i<len;i++){ 
						var end = getLocalTime(data[i]['closing_time']); 
						var html = "<li><div class='pic'><a href='<?php echo U('House/activity');?>/lid/"+data[i]['loupan_id']+"/id/"+data[i]['tender_id']+"'><img src='"+data[i]['path']+"'/></a><div class='text'>活动中&nbsp;"+end+"结束</div></div><div class='title'></div><div class='num'><a href='<?php echo U('House/activity');?>/lid/"+data[i]['loupan_id']+"/id/"+data[i]['tender_id']+"' class='btn ljcy'>立即参与</a></div></li>";
						activityInfo.append(html);
					}
				},'json');
			}
		})
		$('[js-select]:eq(0)').click();
	})
	
	function getLocalTime(nS) { 
		return new Date(parseInt(nS) * 1000).toLocaleString().substr(0,17)
	}

	function mianji(obj){ 
		var activityInfo = $(".activityInfo");
		activityInfo.find('li').remove();
		var url = "/House/mianji";
		var region = "<?php echo $_COOKIE['region_id']; ?>"; 
		var data = {id:obj,region:region};
		$.post(url,data,function(data){
			console.log(data); 
			var len = data.length; 
			for(var i=0;i<len;i++){ 
				var end = getLocalTime(data[i]['closing_time']); 
				var html = "<li><div class='pic'><a href='<?php echo U('House/activity');?>/lid/"+data[i]['loupan_id']+"/id/"+data[i]['tender_id']+"'><img src='"+data[i]['path']+"'/></a><div class='text'>活动中&nbsp;"+end+"结束</div></div><div class='title'></div><div class='num'><a href='<?php echo U('House/activity');?>/lid/"+data[i]['loupan_id']+"/id/"+data[i]['tender_id']+"' class='btn ljcy'>立即参与</a></div></li>";
				activityInfo.append(html);
			}
		},'json');
	}
	
</script>
  </body>
</html>