<?php if (!defined('THINK_PATH')) exit();?><div class="sub_right">
    <div class="sub_tit"><a href="<?php echo U('Worker/index');?>">推荐工人</a></div>
    <div class="recommend_content">
        <ul>
            <?php if(is_array($workerList)): foreach($workerList as $key=>$info): ?><li>
                <div class="<?php if($key < 3): ?>order_head<?php else: ?>order_foot<?php endif; ?>"><?php echo ($key+1); ?></div>
                <div class="recommend_text"><a href="<?php echo ($info["url"]); ?>"><?php echo (htmlspecialchars($info["user_name"])); ?></a><span>浏览量：<?php echo ($info["views"]); ?></span></div>
            </li><?php endforeach; endif; ?>
        </ul>
    </div>
</div>