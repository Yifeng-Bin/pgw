<?php if (!defined('THINK_PATH')) exit();?><div class="photo_border">
    <p>相关图片</p>
    <div class="row">
        <?php if(is_array($photoList)): foreach($photoList as $key=>$info): ?><div class="col-lg-4 col-md-4 col-sm-4"><a href="<?php echo ($info["url"]); ?>"><img src="<?php echo ($info["main_image"]); ?>" width="100%"></a></div><?php endforeach; endif; ?>
    </div>
</div>