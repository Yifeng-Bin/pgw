<?php if (!defined('THINK_PATH')) exit(); if(C('LAYOUT_ON')) { echo ''; } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>跳转提示</title>
<link rel="stylesheet" type="text/css" href="/themes/default/css/jump_base.css" />
<link rel="stylesheet" type="text/css" href="/themes/default/css/jump_style.css?v=2016100701" />
</head>
<body>
	<div class="pop-tips">
			<div class="top">
				<img src="/themes/default/images/logo.jpg" />
				<a href="<?php echo($jumpUrl); ?>"></a>
			</div>
			<div class="tit"><span>操作提示</span></div>
			<div class="con">
			<?php if(isset($message)) {?>
				<p class="success" style="margin-top:40px;"><b><?php echo($message); ?></b></p>
			<?php }else{?>
				<p class="error" style="margin-top:40px;"><b><?php echo($error); ?><b></p>
			<?php }?>
				<span><b id="wait"><?php echo($waitSecond); ?></b>秒后返回继续操作</span>
			</div>
			<div class="bottom">
				如果您的浏览器没反应，请<a id="href" href="<?php echo($jumpUrl); ?>">点击这里</a>
			</div>
		</div>

<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>