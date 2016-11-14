<?php if (!defined('THINK_PATH')) exit();?><div class="detail_content_recommend">
    <h4>相关推荐</h4>
    <ul>
        <?php if(is_array($teachList)): foreach($teachList as $key=>$info): ?><li><a href="<?php echo ($info["url"]); ?>"><?php echo (htmlspecialchars($info["article_name"])); ?></a></li><?php endforeach; endif; ?>
    </ul>
</div>