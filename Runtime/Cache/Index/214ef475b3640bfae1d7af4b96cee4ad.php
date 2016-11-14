<?php if (!defined('THINK_PATH')) exit();?><div class="row">
    <?php if(is_array($bottomHelpList)): foreach($bottomHelpList as $key=>$info): ?><div class="col-md-2 col-sm-2">
        <div class="footer_content_list">
            <ul>
                <li class="list_tit"><a href="#"><?php echo ($info["cat_name"]); ?></a></li>
                <?php if(is_array($info["list"])): foreach($info["list"] as $key=>$child): ?><li><a href="<?php echo ($child["url"]); ?>"><?php echo (htmlspecialchars($child["article_name"])); ?></a></li><?php endforeach; endif; ?>
            </ul>
        </div>
    </div><?php endforeach; endif; ?>
    <div class="col-md-2 col-sm-2">
        <div class="footer_mobile">
            <div class="mobile_tit">手机扫一扫</div>
            <div class="mobile_pic"><img src="images/footer/code.png" width="100%"></div>
        </div>
    </div>
</div>