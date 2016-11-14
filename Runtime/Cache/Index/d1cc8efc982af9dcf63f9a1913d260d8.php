<?php if (!defined('THINK_PATH')) exit();?><div class="teach_list_news">
    <div class="teach_list_right_tit">
        <span>最新文章</span>
    </div>
    <div class="teach_news_content">
        <ul>
            <?php if(is_array($teachList)): foreach($teachList as $key=>$info): ?><li><a href="<?php echo ($info["url"]); ?>"><?php echo (htmlspecialchars($info["article_name"])); ?></a></li><?php endforeach; endif; ?>
        </ul>
    </div>
</div>