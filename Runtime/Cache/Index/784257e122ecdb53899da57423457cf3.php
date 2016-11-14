<?php if (!defined('THINK_PATH')) exit();?><div class="sub_right">
    <div class="sub_tit"><a href="<?php echo U('Teach/index');?>">派工教学</a></div>
    <div class="recommend_content">
        <ul>
            <?php if(is_array($teachList)): foreach($teachList as $key=>$info): ?><li><a href="<?php echo ($info["url"]); ?>"><?php echo (htmlspecialchars($info["article_name"])); ?></a></li><?php endforeach; endif; ?>
        </ul>
    </div>
</div>