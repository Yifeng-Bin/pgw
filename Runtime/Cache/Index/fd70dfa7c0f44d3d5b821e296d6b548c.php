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
    <link href="./bootstrap/3.3.5/css/bootstrap.min.css" type="text/css" rel="stylesheet">
    <link href="./css/style.css?v=20160922002" type="text/css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="/pgw/Statics/js/html5shiv.min.js"></script>
            <script src="/pgw/Statics/js/respond.min.js"></script>        
            <link href="/pgw/Statics/js/respond-proxy.html" id="respond-proxy" rel="respond-proxy" />
            <link href="<?php echo ($baseurl); ?>cross-domain/respond.proxy.gif" id="respond-redirect" rel="respond-redirect" />
            <script src="<?php echo ($baseurl); ?>cross-domain/respond.proxy.js"></script>        
        <![endif]-->
  </head>
  <body>
  	<div class="top">
    <div class="container">
        <div class="top_user"><a href="<?php echo U('User/index');?>"><?php echo (htmlspecialchars($userInfo["user_name"])); ?></a></div>
        <div class="top_tel">服务热线：<?php echo C('service_phone');?></div>
        <div class="top_operation">
            <a href="<?php echo ($baseurl); ?>">[网站主页]</a>
            <a href="<?php echo U('User/index');?>">[个人中心]</a>
            <a href="<?php echo U('Help/detail?id=507');?>">[帮助中心]</a>
            <a href="<?php echo U('User/logout?code='.session('xss_code'));?>">[注销退出]</a>
        </div>
    </div>
</div>
<div class="top_content">
    <div class="container">
    	<img src="./images/User-mid/icon/logo.png">
        <h2>帮助中心</h2>
    </div>
</div>
  	<div id="help">
  		<div class="container" id="conid">
        	<div class="help_left">
            	<div class="help_tit">用户指南</div>
                <div class="help_menu">
                    <ul>
                    <?php if(is_array($bottomHelpList)): foreach($bottomHelpList as $key=>$info): ?><li class="help_bor">
                            <a href="#"><?php echo (htmlspecialchars($info["cat_name"])); ?></a>
                            <ul>
                                <?php if(is_array($info["list"])): foreach($info["list"] as $key=>$child): ?><li><a href="<?php echo ($child["url"]); ?>"><?php echo (htmlspecialchars($child["article_name"])); ?></a></li><?php endforeach; endif; ?>
                            </ul>
                        </li><?php endforeach; endif; ?>                        
                    </ul>
                </div>
            </div>
            <div class="help_right">
            	<div class="help_right_tit"><span><?php echo (htmlspecialchars($articleInfo["article_name"])); ?></span></div>
                <div class="help_right_content">
                <?php echo ($articleInfo["article_desc"]); ?>
                </div>
            </div>
        </div>
  	

	<div class="User_foot"   id="footerid"  style="position: static;">

    	<p>&copy;Copyright2012-2015 All Rights Reserved ICP 备案：湘ICP 备 14014275号</p>

        <p>

            <img src="images/footer/foot_icon_1.png">

            <img src="images/footer/foot_icon_2.png">

            <img src="images/footer/foot_icon_3.png">

        </p>

    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="./jquery/jquery-1.12.3.min.js"></script>
    <script src="./js/showdate.js"></script>
    <script src="/pgw/Statics/js/areaChange.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="./bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/pgw/Statics/plupload/plupload.full.min.js"></script>
    <script type="text/javascript" src="/pgw/Statics//plupload/i18n/zh_CN.js"></script> 

<script>
//获取对象定位尺寸信息
var getOffset = function(obj, scrOnOff) {
	obj=$(obj).get(0);
	var left = 0, top = 0;
	var width = obj.offsetWidth, height = obj.offsetHeight;
	do { left += obj.offsetLeft - (scrOnOff ? obj.scrollLeft : 0), top += obj.offsetTop - (scrOnOff ? obj.scrollTop : 0); } while (obj = obj.offsetParent);
	return { 'left': left, 'top': top, 'width': width, 'height': height };
};
//获取屏幕尺寸信息
var getClient=function() {
	/*获取document.documentElement信息*/
	var dc = document.documentElement || document.body;
	return  { width: dc.clientWidth, height: dc.clientHeight };
}
function setFooter(conid,footerid){
	var conOfffset=getOffset(conid);//获取内容区高度
	var client=getClient();//获取屏幕高度
	if(conOfffset.height<client.height-94){
		$(footerid).css({position:'absolute',bottom:0,left:0});//
	}else{
		$(footerid).css({position:'static'});
	}
}

setFooter('#conid','#footerid');
</script>

  </body>
</html>