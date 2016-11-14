<?php
namespace Index\Controller;
class PhotoController extends CommonController {
    //装修效果图
    public function index(){
        $this->pageHeadInfo = array(
            'title' => '装修效果图-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );     
        
        
        //排序
        $default_sort = array('addtime','desc');
        $allowed_sort_arr = array(        
            'addtime' => array(
                'default' => 'desc',
                'list' => array('desc'),
                'text' => '最新',
            ),
            'visittime' => array(
                'default' => 'desc',
                'list' => array('desc'),
                'text' => '人气',
            ),         
        );
        $sort_fields = array(
            'addtime' => 'a.add_time',            
            'visittime' => 'a.visit_time',
        );
        $sort = I('get.sort', '', '');
        $sort = explode('_', $sort);
        if(empty($sort) || (!isset($allowed_sort_arr[$sort[0]])) || (!in_array($sort[1], $allowed_sort_arr[$sort[0]]['list']))){
            $sort = $default_sort;
        }
        $sortby = $sort_fields[$sort[0]].' '.$sort[1];
        //排序        

        $getAttr =  I("get.attr",'');
        $getAttr = explode('_', $getAttr);
        $attr = array(); //过滤后的属性值
        $actualAttr = array(); //最终计算
        for($i = count($getAttr) -1 ; $i > 0 ; $i=$i-2){
            $attr[intval($getAttr[$i-1])] = intval($getAttr[$i]);
        }
        $attrList = M('ArticleAttr')->field('attr_id,attr_desc,attr_type')->where(array('article_cat_id' => 2,'status' => 1))->order('sort desc')->select();
        if(!empty($attrList)){
            foreach($attrList as $info){
                if(isset($attr[$info['attr_id']])){
                    $is_exist = M('ArticleAttrValue')->where(array('attr_id' => $info['attr_id'],'attr_value_id' =>$attr[$info['attr_id']] ,'status' => 1))->order('sort desc')->count();
                    if($is_exist){
                        $actualAttr[$info['attr_id']] = $attr[$info['attr_id']];
                    }
                }
            }
            
            foreach($attrList as &$info){
                $info['valueList'] = M('ArticleAttrValue')->field('attr_value_id,attr_value')->where(array('attr_id' => $info['attr_id'],'status' => 1))->order('sort desc')->select();
                array_unshift($info['valueList'],array('attr_value_id' => 0,'attr_value' => '不选')); 
                $tempAttr = $actualAttr;
                if(isset($tempAttr[$info['attr_id']])){
                    unset($tempAttr[$info['attr_id']]);
                }
                foreach($info['valueList'] as &$value){
                    if((isset($actualAttr[$info['attr_id']]) && $actualAttr[$info['attr_id']] == $value['attr_value_id']) || (!isset($actualAttr[$info['attr_id']]) && $value['attr_value_id'] == 0)){
                        $value['is_current'] = 1;
                    }else{
                        $value['is_current'] = 0;
                        $tempAttr[$info['attr_id']] = $value['attr_value_id'];
                        $temp_str = '';
                        foreach($tempAttr as $k => $v){
                            $temp_str.='_'.$k.'_'.$v;
                        }
                       $value['url'] = U('Photo/index?attr='.ltrim($temp_str,'_')); 
                    }
                }
            }
            unset($tempAttr);
        }

        $this->attrList = $attrList;
        
        
        $queryArr = array();
        $temp_str = '';
        foreach($actualAttr as $k => $v){
            $temp_str.='_'.$k.'_'.$v;
        }        
        $queryArr['attr'] = ltrim($temp_str,'_');
        foreach ($allowed_sort_arr as $k => $sortInfo) {
            $sortArr[$k]['selected'] = ($k == $sort[0]) ? 1 : 0;
            if($sortArr[$k]['selected']){
                $key = array_search($sort[1], $sortInfo['list']);
                if($key !== false){
                    unset($sortInfo['list'][$key]);
                }
                if(empty($sortInfo['list'])){
                    $queryArr['sort'] = $k."_".$sortInfo['default'];
                }else{
                    $queryArr['sort'] = $k."_".array_shift($sortInfo['list']);
                }
            }else{
                $queryArr['sort'] = $k."_".$sortInfo['default'];
            }
            $sortArr[$k]['name'] = $sortInfo['text'];
            $sortArr[$k]['url'] = U('Photo/index',$queryArr);
        }
        $this->sortArr = $sortArr;        
        
        $where = array(
          'a.is_delete' => 0,
          'a.cat_id' => 2,  
        );
        if(!empty($actualAttr)){
           $where['at.attr_value_id'] = array('in',$actualAttr);
        }
        $count  = M('Article')->alias('a')->join('left join __ARTICLE_ATTR_INFO__ as at on at.article_id = a.article_id')->where($where)->count('distinct a.article_id');
        $Page  = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        //$Page->lastSuffix = false;
        $Page->setConfig('first', '首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '…%TOTAL_PAGE%');
        $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
        $Page->rollPage = 11;
        $this->pageShow = $Page->show();
        $photoList = M('Article')->field('a.article_id,a.article_name,a.photo_num,a.main_image')->alias('a')->join('left join __ARTICLE_ATTR_INFO__ as at on at.article_id = a.article_id')
                ->limit($Page->firstRow.','.$Page->listRows)->group('a.article_id')->order($sortby)->where($where)->select();
        foreach($photoList as &$info){
            $info['url'] = U('Photo/detail?id='.$info['article_id']);
            $info['main_image'] = str_replace('style_org_img', 'style_thumb_img', $info['main_image']);
        }
        $this->photoList = $photoList;
        $this->display();
    }
    
    public function detail(){
        $this->pageHeadInfo = array(
            'title' => '装修效果图-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $article_id = i('get.id',0,'intval');
        $where = array(
          'a.is_delete' => 0,
          'a.cat_id' => 2,  
          'a.article_id' => $article_id,
        );
        $articleInfo = M('Article')->field('a.article_id,a.article_name,a.add_time,a.visit_time,a.article_keywords')->alias('a')->where($where)->find();
        $articleInfo['add_time'] = local_date('Y-m-d H:i:s', $articleInfo['add_time']);
        $articleInfo['gallery'] = M('ArticlePhoto')->where(array('article_id' => $article_id))->order('sort desc')->select();  
        foreach($articleInfo['gallery'] as &$info){
            $info['thumb_url'] = str_replace('style_org_img', 'style_thumb_img', $info['url']);
        }
        M('Article')->where(array('article_id' => $article_id))->setInc('visit_time');
        $this->articleInfo = $articleInfo;
        $this->display();
    }
}
