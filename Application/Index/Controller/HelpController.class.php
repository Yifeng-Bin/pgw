<?php

namespace Index\Controller;

class HelpController extends CommonController {

    public function index() {
        $this->pageHeadInfo = array(
            'title' => '帮助中心-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        //$this->display();
    }
    
    public function detail(){
        $this->pageHeadInfo = array(
            'title' => '帮助中心-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        
        
        
        
        $article_id = i('get.id',0,'intval');
        $where = array(
          'a.is_delete' => 0,
          'a.cat_id_1' => 1,
          'a.article_id' => $article_id,
        );
        $articleInfo = M('Article')->field('article_name,article_desc')->alias('a')->where($where)->find();
        if(empty($articleInfo)){
            $this->error('帮助文档不存在');
            exit();
        }
        M('Article')->where(array('article_id' => $article_id))->setInc('visit_time');
        $this->articleInfo = $articleInfo;
        
        $bottomxHelpList = array();
        $helpList = M('ArticleCategory')->field('cat_id,cat_name')->where(array('parent_id' => 1))->order('sort desc')->select();
        foreach($helpList as &$info){
            $info['list'] = M('Article')->field('article_id,article_name')->where(array('cat_id_2' => $info['cat_id'],'is_delete' => 0))->select();
            foreach($info['list'] as &$child){
                $child['url'] = U('Help/detail?id='.$child['article_id']);
            }
        }
        $this->bottomHelpList = $helpList;        
        
        $this->display();
    }      
}
