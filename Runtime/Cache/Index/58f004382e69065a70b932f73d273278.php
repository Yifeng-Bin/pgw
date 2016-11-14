<?php if (!defined('THINK_PATH')) exit();?><div class="photo_border">
    <p>大家都在看</p>
    <ol>
        <?php if(is_array($photoList)): foreach($photoList as $key=>$info): ?><li><a href="<?php echo ($info["url"]); ?>"><?php echo (htmlspecialchars($info["article_name"])); ?></a></li><?php endforeach; endif; ?>
    </ol>
</div>