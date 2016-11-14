<?php
namespace Index\Tag;
use Think\Template\TagLib;
class Sc extends TagLib {
    // 标签定义
    protected $tags = array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'ads' => array('attr' => 'position_id'),
    );

    public function _ads($tag, $content) {
        $position_id = $tag['position_id'];
        $parseStr   =   '<?php $adList = D("Ad")->getAdList('.$position_id.'); ?>';
        $parseStr  .=   '<?php if(is_array($adList)): foreach($adList as $key => $adInfo): ?>';
        $parseStr  .=   $this->tpl->parse($content);
        $parseStr  .=   '<?php endforeach; endif; ?>';
        if(!empty($parseStr)) {
            return $parseStr;
        }
        return ;
    }

}
