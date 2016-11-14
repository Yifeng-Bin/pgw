<?php if (!defined('THINK_PATH')) exit();?><ul>
    <?php if(is_array($teachList)): foreach($teachList as $key=>$info): ?><li><a href="<?php echo ($info["url"]); ?>"><?php if(!empty($info['data_text'])): echo (htmlspecialchars($info["data_text"])); else: echo (htmlspecialchars($info["article_name"])); endif; ?></a></li><?php endforeach; endif; ?>
</ul>