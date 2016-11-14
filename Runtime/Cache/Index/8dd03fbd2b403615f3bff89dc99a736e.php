<?php if (!defined('THINK_PATH')) exit();?><div class="news">
    <div class="tit">最新动态<span><a href="<?php echo U('Requirement/index');?>">more</a></span></div>
    <div class="news_content">
        <ul>
            <?php if(is_array($dynamicList)): foreach($dynamicList as $key=>$info): ?><li><a href="<?php echo ($info["url"]); ?>"><?php echo (htmlspecialchars($info["content"])); ?></a></li><?php endforeach; endif; ?>
        </ul>
    </div>
</div>