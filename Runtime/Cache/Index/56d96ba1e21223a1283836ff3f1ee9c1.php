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
    <link href="./css/style.css?v=20160923001" type="text/css" rel="stylesheet">

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
  	<div class="sub_location">
    	<div class="container">
        	<div class="location_tit">
            	您的位置： <a href="<?php echo ($baseurl); ?>">派工网</a> > <a href="<?php echo U('Photo/index');?>">派工学堂</a>
            </div>
        </div>
    </div>
	<div class="sub">
    	<div class="container">
       		<div class="row">
            	<div class="col-lg-9 col-md-9 col-sm-9">
                	<div id="play">
                      <ul class="img_ul">
                          <?php if(is_array($articleInfo["gallery"])): foreach($articleInfo["gallery"] as $key=>$info): if($key == 0): ?><li style="display:block;"><a class="img_a"><img src="<?php echo ($info["url"]); ?>"></a></li>  
                              <?php else: ?>
                                <li><span></span><img src="<?php echo ($info["url"]); ?>"></a></li><?php endif; endforeach; endif; ?>
                      </ul>  
                      <a href="javascript:void(0)" class="prev_a change_a" title="上一张"> <span></span></a>
                      <a href="javascript:void(0)" class="next_a change_a" title="下一张"> <span style="display: inline; "></span> </a>
                    </div>
                    <div class="img_hd">
                      <ul class=" clearfix">
                          <?php if(is_array($articleInfo["gallery"])): foreach($articleInfo["gallery"] as $key=>$info): ?><li><a class="img_a"><img src="<?php echo ($info["thumb_url"]); ?>" onload="imgs_load(this)"></a></li><?php endforeach; endif; ?>                          
                      </ul>
                      <a class="bottom_a prev_a" style="opacity: 0.7; "></a> <a class="bottom_a next_a" style="opacity: 0.7; "></a>
                    </div>
                </div>
                	
                <div class="col-lg-3 col-md-3 col-sm-3">
                	<div class="photo_box">
                    	<div class="photo_border">
                        	<h4><?php echo (htmlspecialchars($articleInfo['article_name'])); ?></h4>
                            <small><?php echo ($articleInfo['add_time']); ?></small>
                            <p><a id="add_follow_content" href="javascript:void(0)" data-id="<?php echo ($articleInfo['article_id']); ?>" class="guanzhu">关注</a> 浏览量（<?php echo ($articleInfo["visit_time"]); ?>）</p>
                        </div>
                        <div class="photo_border">
                            <p>标签</p>
                            <b><?php echo (htmlspecialchars($articleInfo["article_keywords"])); ?></b>
                        </div>
                        <?php echo W('Photo/newPhoto');?>
                        <?php echo W('Photo/hotPhoto');?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="./jquery/jquery-1.12.3.min.js"></script>
    <script src="./js/jquery.SuperSlide.2.1.1.js"></script>
    <script src="./js/main.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="./bootstrap/3.3.5/js/bootstrap.min.js"></script>
 <script>
	var i=0; //图片标识
var img_num=$(".img_ul").children("li").length; //图片个数
$(".img_ul li").hide(); //初始化图片	
play();
$(function(){
	 $(".img_hd ul").css("width",($(".img_hd ul li").outerWidth(true))*img_num); //设置ul的长度
	 
	 $(".bottom_a").css("opacity",0.7);	//初始化底部a透明度
	 //$("#play").css("height",$("#play .img_ul").height());
	 if (!window.XMLHttpRequest) 
	 $(".change_a").focus( function() { this.blur(); } );
	 $(".bottom_a").hover(function(){
		 $(this).css("opacity",1);	
		 },function(){
		$(this).css("opacity",0.7);	 
			 });
	 $(".change_a").hover(function(){
		 $(this).children("span").show();
		 },function(){
		 $(this).children("span").hide();
			 });
	 $(".img_hd ul li").click(function(){
		 i=$(this).index();
		 play();
		 });
	 $(".prev_a").click(function(){
		 //i+=img_num;
		 i--;
		 //i=i%img_num;
		 i=(i<0?0:i);
		 play();
		 }); 
	 $(".next_a").click(function(){
		 i++;
		 //i=i%img_num;
		 i=(i>(img_num-1)?(img_num-1):i);
		 play();
		 }); 
	 }); 
function play(){ 
	var img=new Image(); //图片预加载
	img.onload=function(){img_load(img,$(".img_ul").children("li").eq(i).find("img"))};
	img.src=$(".img_ul").children("li").eq(i).find("img").attr("src");
	//$(".img_ul").children("li").eq(i).find("img").(img_load($(".img_ul").children("li").eq(i).find("img")));
	
	$(".img_hd ul").children("li").eq(i).addClass("on").siblings().removeClass("on");
	if(img_num>7){ //大于7个的时候进行移动
		if(i<img_num-3){ //前3个
		$(".img_hd ul").animate({"marginLeft":(-($(".img_hd ul li").outerWidth()+4)*(i-3<0?0:(i-3)))});
		}
		else if(i>=img_num-3){ //后3个
			$(".img_hd ul").animate({"marginLeft":(-($(".img_hd ul li").outerWidth()+4)*(img_num-7))});
			}
	}
	if (!window.XMLHttpRequest) { //对ie6设置a的位置
	$(".change_a").css("height",$(".change_a").parent().height());}
	}
function img_load(img_id,now_imgid){ //大图片加载设置 （img_id 新建的img,now_imgid当前图片）
    
    if(img_id.width/img_id.height>1)
	{
		if(img_id.width >=$("#play").width()) $(now_imgid).width($("#play").width());
		}
/*	else {
		if(img_id.height>=500) $(now_imgid).height(500);
		}*/
		$(".img_ul").children("li").eq(i).show().siblings("li").hide(); //大小确定后进行显示
	}
function imgs_load(img_id){ //小图片加载设置
	if(img_id.width >=$(".img_hd ul li").width()){img_id.width = 80};
	//if(img_id.height>=$(".img_hd ul li").height()) {img_id.height=$(".img_hd ul li").height();}
	}
        $("#add_follow_content").click(function(){
            var data = {};
            data.act = 'add';
            data.id = $(this).attr('data-id');
            var that = $(this);
            $.post("<?php echo U('Article/follow');?>",data,function(result){
                if(result.status == 0){
                    alert(result.info);
                }else{
                    that.html("已关注");
                    that.unbind("click"); 
                }
            },'json');            
        });         
	</script>
  </body>
</html>