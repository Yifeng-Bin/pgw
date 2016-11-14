<?php

namespace Index\Controller;

class TeachController extends CommonController {

    /*
    public function home() {
        $this->pageHeadInfo = array(
            'title' => '派工教学-' . C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $this->display();
    }
    */

    public function index() {
        $this->pageHeadInfo = array(
            'title' => '装修学堂-' . C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $queryArr = array(); // 所有检验过的get参数
        //排序
        $default_sort = array('addtime', 'desc');
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
        if (empty($sort) || (!isset($allowed_sort_arr[$sort[0]])) || (!in_array($sort[1], $allowed_sort_arr[$sort[0]]['list']))) {
            $sort = $default_sort;
        }
        $sortby = $sort_fields[$sort[0]] . ' ' . $sort[1];
        $queryArr['sort'] = $sort[0] . '_' . $sort[1];
        //排序

        //下级分类id
        $catArr = array();
        $cid = I('get.cid', '');
        if(!empty($cid)){
            $catArr = explode('_', $cid);
        }
        if(!empty($catArr)){
            $i = 0;
            foreach($catArr as $cat_id){
                $where = array('cat_id' => $cat_id);
                if($i == 0){
                    $where['parent_id'] = 4;
                }
                $catInfo = M('ArticleCategory')->field('cat_id,cat_name')->where($where)->find();
                if(empty($catInfo)){
                    break;
                }
                $i++;
            }
            if($i > 0){
                $catArr = array_slice($catArr,0, $i);
            }else{
                $catArr = array();
            }
        }
        
        if(!empty($catArr)){
            if($i > 1){
                $queryArr['cid'] = implode('_', $catArr);
            }else{
                $queryArr['cid'] = $catArr[0];
            }
        }
        
        //下级分类id
        
        //属性筛选
        $getAttr = I("get.attr", '');
        $getAttr = explode('_', $getAttr);
        $attr = array(); //过滤后的属性值
        $actualAttr = array(); //最终计算
        for ($i = count($getAttr) - 1; $i > 0; $i = $i - 2) {
            if(isset($getAttr[$i])){
                $attr[intval($getAttr[$i - 1])] = intval($getAttr[$i]);
            }
        }
        $attrList = M('ArticleAttr')->field('attr_id,attr_desc,attr_type')->where(array('article_cat_id' => 4, 'status' => 1))->order('sort desc')->select();
        if (!empty($attrList)) {
            foreach ($attrList as $info) {
                if (isset($attr[$info['attr_id']])) {
                    $is_exist = M('ArticleAttrValue')->where(array('attr_id' => $info['attr_id'], 'attr_value_id' => $attr[$info['attr_id']], 'status' => 1))->order('sort desc')->count();
                    if ($is_exist) {
                        $actualAttr[$info['attr_id']] = $attr[$info['attr_id']];
                    }
                }
            }
        }
        $tempAttr = $actualAttr;
        $temp_str = '';
        foreach ($tempAttr as $k => $v) {
            $temp_str.='_' . $k . '_' . $v;
        }
        $queryArr['attr'] = ltrim($temp_str, '_');
        unset($tempAttr);        
        //属性筛选
        
        //循环生成属性url
        $tempQueryArr = $queryArr;
        foreach ($attrList as &$info) {
            $info['valueList'] = M('ArticleAttrValue')->field('attr_value_id,attr_value')->where(array('attr_id' => $info['attr_id'], 'status' => 1))->order('sort desc')->select();
            array_unshift($info['valueList'], array('attr_value_id' => 0, 'attr_value' => '不选'));
            $tempAttr = $actualAttr;
            if (isset($tempAttr[$info['attr_id']])) {
                unset($tempAttr[$info['attr_id']]);
            }
            foreach ($info['valueList'] as &$value) {
                if ((isset($actualAttr[$info['attr_id']]) && $actualAttr[$info['attr_id']] == $value['attr_value_id']) || (!isset($actualAttr[$info['attr_id']]) && $value['attr_value_id'] == 0)) {
                    $value['is_current'] = 1;
                } else {
                    $value['is_current'] = 0;
                    $tempAttr[$info['attr_id']] = $value['attr_value_id'];
                    $temp_str = '';
                    foreach ($tempAttr as $k => $v) {
                        $temp_str.='_' . $k . '_' . $v;
                    }
                    $tempQueryArr['attr'] = ltrim($temp_str, '_');
                    $value['url'] = U('Teach/index',$tempQueryArr);
                }
            }
        }
        unset($tempAttr);
        unset($tempQueryArr);
        //循环生成属性url
        $this->attrList = $attrList;


        //所有下级分类
        $tempQueryArr = $queryArr;
        $catList = array();
        if(empty($catArr)){
            $catList[0] = M('ArticleCategory')->field('cat_id,cat_name')->where(array('parent_id' => 4, 'status' => 1))->order('sort desc')->select();
            array_unshift($catList[0], array('cat_id' => 0, 'cat_name' => '不选'));    
            foreach ($catList[0] as &$catInfo) {
                if($catInfo['cat_id'] != 0){
                    $catInfo['is_current'] = 0;
                }else{
                    $catInfo['is_current'] = 1;
                }
                if($catInfo['cat_id'] != 0){
                    $tempQueryArr['cid'] = $catInfo['cat_id'];
                    $catInfo['cat_url'] = U('Teach/index',$tempQueryArr);                
                }else{
                    unset($tempQueryArr['cid']);
                    $catInfo['cat_url'] = U('Teach/index',$tempQueryArr); 
                }
            }            
        }else{
            $i = 0;
            $temp_cat_str = '';
            foreach($catArr as $cat_id){
                $parent_id = M('ArticleCategory')->where(array('cat_id' => $cat_id))->getField('parent_id');
                $catList[$i] = M('ArticleCategory')->field('cat_id,cat_name')->where(array('parent_id' => $parent_id, 'status' => 1))->order('sort desc')->select();
                array_unshift($catList[$i], array('cat_id' => 0, 'cat_name' => '不选'));    
                foreach ($catList[$i] as &$catInfo) {
                    if($catInfo['cat_id'] != $cat_id){
                        $catInfo['is_current'] = 0;
                    }else{
                        $catInfo['is_current'] = 1;
                    }
                    
                    if($catInfo['cat_id'] != 0){
                        $tempQueryArr['cid'] = $temp_cat_str.'_'.$catInfo['cat_id'];
                        $tempQueryArr['cid'] = ltrim($tempQueryArr['cid'],'_');
                        $catInfo['cat_url'] = U('Teach/index',$tempQueryArr);                
                    }else{
                        $tempQueryArr['cid'] = ltrim($temp_cat_str,'_');
                        $catInfo['cat_url'] = U('Teach/index',$tempQueryArr); 
                    }
                }
                $temp_cat_str = $temp_cat_str."_".$cat_id;               
                $i++;
            }
            
            $temp_cat_str = ltrim($temp_cat_str,'_');
            $catList[$i] = M('ArticleCategory')->field('cat_id,cat_name')->where(array('parent_id' => $cat_id, 'status' => 1))->order('sort desc')->select();
            if(!empty($catList[$i])){
                array_unshift($catList[$i], array('cat_id' => 0, 'cat_name' => '不选'));    
                foreach ($catList[$i] as &$catInfo) {
                    if($catInfo['cat_id'] != 0){
                        $catInfo['is_current'] = 0;
                    }else{
                        $catInfo['is_current'] = 1;
                    }
                    if($catInfo['cat_id'] != 0){
                        $tempQueryArr['cid'] = $temp_cat_str.'_'.$catInfo['cat_id'];
                        $catInfo['cat_url'] = U('Teach/index',$tempQueryArr);                
                    }
                }                 
            }else{
                unset($catList[$i]);
            }
        }
        $this->catList = $catList;
        //所有下级分类


        foreach ($allowed_sort_arr as $k => $sortInfo) {
            $sortArr[$k]['selected'] = ($k == $sort[0]) ? 1 : 0;
            if ($sortArr[$k]['selected']) {
                $key = array_search($sort[1], $sortInfo['list']);
                if ($key !== false) {
                    unset($sortInfo['list'][$key]);
                }
                if (empty($sortInfo['list'])) {
                    $queryArr['sort'] = $k . "_" . $sortInfo['default'];
                } else {
                    $queryArr['sort'] = $k . "_" . array_shift($sortInfo['list']);
                }
            } else {
                $queryArr['sort'] = $k . "_" . $sortInfo['default'];
            }
            $sortArr[$k]['name'] = $sortInfo['text'];
            $sortArr[$k]['url'] = U('Photo/index', $queryArr);
        }
        $this->sortArr = $sortArr;

        $where = array(
            'a.is_delete' => 0,
            'a.cat_id_1' => 4,
        );
        if(!empty($catArr)){
            $i = 2;
            foreach($catArr as $cat_id){
                $where['a.cat_id_'.$i] = $cat_id;
                $i++;
            }
            
        }
        if (!empty($actualAttr)) {
            $where['at.attr_value_id'] = array('in', $actualAttr);
        }
        $count = M('Article')->alias('a')->join('left join __ARTICLE_ATTR_INFO__ as at on at.article_id = a.article_id')->where($where)->count('distinct a.article_id');
        $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        //$Page->lastSuffix = false;
        $Page->setConfig('first', '首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '…%TOTAL_PAGE%');
        $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
        $Page->rollPage = 11;
        $this->pageShow = $Page->show();
        $teachList = M('Article')->field('a.article_id,a.article_name,a.main_image,a.visit_time,a.add_time,a.article_keywords,a.article_brief')->alias('a')->join('left join __ARTICLE_ATTR_INFO__ as at on at.article_id = a.article_id')
                        ->limit($Page->firstRow . ',' . $Page->listRows)->group('a.article_id')->order($sortby)->where($where)->select();
        foreach ($teachList as &$info) {
            $info['url'] = U('Teach/detail?id=' . $info['article_id']);
            $info['main_image'] = str_replace('style_org_img', 'style_thumb_img', $info['main_image']);
            $info['date'] = local_date('Y-m-d', $info['add_time']);
        }
        $this->teachList = $teachList;
        $this->display();
    }

    
    public function detail() {
        $id = I('get.id',0,'intval');
        $where = array(
            'is_delete' => 0,
            'is_checked' => 1,
            'article_id' => $id,
        );
        $teachInfo = M('Article')->field('article_id,article_name,article_brief,article_keywords,cat_id,article_desc,visit_time,add_time')->where($where)->find();
        if(empty($teachInfo)){
            $this->error('教学文章不存在');
            exit();
        }
        if($teachInfo['cat_id'] != 0){
            $catInfoArr = array();
            $tempCatId = $teachInfo['cat_id'];
            while(1){
                $catInfo = M('ArticleCategory')->field('cat_id,cat_name,parent_id')->where(array('cat_id' => $tempCatId))->find();
                if(empty($catInfo)){
                    break;
                }
                if($catInfo['parent_id'] == 0){
                    $catInfo['cat_url'] = '';
                    $catInfoArr[] = $catInfo;   
                    break;
                }else{
                    $catInfo['cat_url'] = U('Teach/index?cid='.$catInfo['cat_id']);
                    $catInfoArr[] = $catInfo;                    
                }
                $tempCatId = $catInfo['parent_id'];
            }
            unset($tempCatId);
            if(empty(!isset($catInfArr[0]) || $catInfArr[0]['cat_id'] != 4)){
                $this->error('教学文章不存在');
                exit();
            }
        }else{
            $this->error('教学文章不存在');
            exit();
        }
        $this->catInfoArr = array_reverse($catInfoArr);
        $this->pageHeadInfo = array(
            'title' => $teachInfo['article_name'].'-' . C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $teachInfo['add_time'] = local_date('Y-m-d H:i:s', $teachInfo['add_time']);
        
        $teachInfo['comment_list'] = M('ArticleComment')->alias('ac')->join('__USERS__ as u on ac.user_id = u.user_id')
                ->field('ac.content,ac.add_time,u.user_name,u.avatar')->where(array('ac.article_id' => $id,'ac.is_checked' => 1))->order('add_time desc')->limit('0,50')->select();

        $this->teachInfo = $teachInfo;
        
        M('Article')->where(array('article_id' => $id))->setInc('visit_time');
        $this->display();
    }    
}
