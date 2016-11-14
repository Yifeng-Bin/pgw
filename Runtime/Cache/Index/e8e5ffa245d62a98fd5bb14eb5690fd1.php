<?php if (!defined('THINK_PATH')) exit();?>    	<div class="container">
        	<div class="tit">装修效果图<span><a href="<?php echo U('Photo/index');?>">more</a></span></div>
        	<div class="section">
                <ul class="clearfix">
                    <li class="mokuai_1">
                        <?php if(is_array($photoList)): $i = 0; $__LIST__ = array_slice($photoList,0,1,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;?><div class="photo">
                           <a href="<?php echo ($info["url"]); ?>"><img src="<?php if(!empty($info['data_image'])): echo ($info["data_image"]); else: echo ($info["main_image"]); endif; ?>"></a>
                        </div>
                        <div class="text">
                            <a href="<?php echo ($info["url"]); ?>"><h3><?php if(!empty($info['data_text'])): echo (htmlspecialchars($info["data_text"])); else: echo (htmlspecialchars($info["article_name"])); endif; ?></h3></a>
                        </div><?php endforeach; endif; else: echo "" ;endif; ?>                        
 
                    </li>
                    <li class="mokuai_2">
                        <?php if(is_array($photoList)): $i = 0; $__LIST__ = array_slice($photoList,1,1,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;?><div class="photo">
                           <a href="<?php echo ($info["url"]); ?>"><img src="<?php if(!empty($info['data_image'])): echo ($info["data_image"]); else: echo ($info["main_image"]); endif; ?>"></a>
                        </div>
                        <div class="text_2">
                            <a href="<?php echo ($info["url"]); ?>"><h3><?php if(!empty($info['data_text'])): echo (htmlspecialchars($info["data_text"])); else: echo (htmlspecialchars($info["article_name"])); endif; ?></h3></a>
                        </div><?php endforeach; endif; else: echo "" ;endif; ?>
                    </li>
                    <li class="mokuai_3">
                        <?php if(is_array($photoList)): $i = 0; $__LIST__ = array_slice($photoList,2,1,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;?><div class="photo">
                           <a href="<?php echo ($info["url"]); ?>"><img src="<?php if(!empty($info['data_image'])): echo ($info["data_image"]); else: echo ($info["main_image"]); endif; ?>"></a>
                        </div>
                        <div class="text">
                            <a href="<?php echo ($info["url"]); ?>"><h3><?php if(!empty($info['data_text'])): echo (htmlspecialchars($info["data_text"])); else: echo (htmlspecialchars($info["article_name"])); endif; ?></h3></a>
                        </div><?php endforeach; endif; else: echo "" ;endif; ?>                          
                    </li>
                    <li class="mokuai_4">
                        <?php if(is_array($photoList)): $i = 0; $__LIST__ = array_slice($photoList,3,1,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;?><div class="photo">
                           <a href="<?php echo ($info["url"]); ?>"><img src="<?php if(!empty($info['data_image'])): echo ($info["data_image"]); else: echo ($info["main_image"]); endif; ?>"></a>
                        </div>
                        <div class="text_2">
                            <a href="<?php echo ($info["url"]); ?>"><h3><?php if(!empty($info['data_text'])): echo (htmlspecialchars($info["data_text"])); else: echo (htmlspecialchars($info["article_name"])); endif; ?></h3></a>
                        </div><?php endforeach; endif; else: echo "" ;endif; ?>                               
                    </li>
                    <li class="mokuai_4">
                        <?php if(is_array($photoList)): $i = 0; $__LIST__ = array_slice($photoList,4,1,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;?><div class="photo">
                           <a href="<?php echo ($info["url"]); ?>"><img src="<?php if(!empty($info['data_image'])): echo ($info["data_image"]); else: echo ($info["main_image"]); endif; ?>"></a>
                        </div>
                        <div class="text_2">
                            <a href="<?php echo ($info["url"]); ?>"><h3><?php if(!empty($info['data_text'])): echo (htmlspecialchars($info["data_text"])); else: echo (htmlspecialchars($info["article_name"])); endif; ?></h3></a>
                        </div><?php endforeach; endif; else: echo "" ;endif; ?>  
                    </li>
                </ul>
            </div>
        </div>