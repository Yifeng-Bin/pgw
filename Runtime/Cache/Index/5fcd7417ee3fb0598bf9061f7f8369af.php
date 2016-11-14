<?php if (!defined('THINK_PATH')) exit();?><div>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#gcgl" aria-controls="gcgl" role="tab" data-toggle="tab">工程管理</a></li>
        <li role="presentation"><a href="#jzgj" aria-controls="jzgj" role="tab" data-toggle="tab">家装工匠</a></li>
        <li role="presentation"><a href="#jjwx" aria-controls="jjwx" role="tab" data-toggle="tab">家居维修</a></li>
        <li role="presentation"><a href="#cpaz" aria-controls="cpaz" role="tab" data-toggle="tab">成品安装</a></li>
        <li role="presentation"><a href="#zgpy" aria-controls="zgpy" role="tab" data-toggle="tab">杂工搬运</a></li>
        <span><a href="#">more</a></span>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="gcgl">
            <div class="row">
                <?php if(is_array($workerList_5)): foreach($workerList_5 as $key=>$info): ?><div class="col-md-3 col-sm-3">
                    <div class="thumbnail margin_fenge">
                        <a href="<?php echo ($info["url"]); ?>"><img src="<?php echo ($info["avatar"]); ?>" width="100%"></a>
                        <div class="caption">
                            <h4><?php echo ($info["data_text"]); ?></h4>
                            <h6>浏览量<span>：<?php echo ($info["views"]); ?></span></h6>
                        </div>
                    </div>
                </div><?php endforeach; endif; ?>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="jzgj">
            <div class="row">
                <div class="col-md-3 col-sm-3">
                    <div class="thumbnail margin_fenge">
                        <a href="#"><img src="./images/workman/workman_02.png" width="100%"></a>
                        <div class="caption">
                            <h4>室内设计师：罗波</h4>
                            <h6>浏览量<span>：222</span></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="jjwx">
            <div class="row">
                <div class="col-md-3 col-sm-3">
                    <div class="thumbnail margin_fenge">
                        <a href="#"><img src="./images/workman/workman_03.png" width="100%"></a>
                        <div class="caption">
                            <h4>室内设计师：罗波</h4>
                            <h6>浏览量<span>：222</span></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="cpaz">
            <div class="row">
                <div class="col-md-3 col-sm-3">
                    <div class="thumbnail margin_fenge">
                        <a href="#"><img src="./images/workman/workman_04.png" width="100%"></a>
                        <div class="caption">
                            <h4>室内设计师：罗波</h4>
                            <h6>浏览量<span>：222</span></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="zgpy">
            <div class="row">
                <div class="col-md-3 col-sm-3">
                    <div class="thumbnail margin_fenge">
                        <a href="#"><img src="./images/workman/workman_05.png" width="100%"></a>
                        <div class="caption">
                            <h4>室内设计师：罗波</h4>
                            <h6>浏览量<span>：222</span></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>