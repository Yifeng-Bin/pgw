<?php

namespace Index\Controller;

class CaseController extends CommonController {

    public function detail(){
        $this->pageHeadInfo = array(
            'title' => '案例展示-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $article_id = i('get.id',0,'intval');
        $where = array(
          'a.is_delete' => 0,
          'a.cat_id_1' => 49,  
          'a.article_id' => $article_id,
        );
        $articleInfo = M('Article')->field('a.article_id,a.article_name,a.visit_time,a.article_desc,a.photo_num,a.add_time,a.is_checked')->alias('a')->where($where)->find();
        $articleInfo['add_time'] = local_date('Y-m-d H:i:s', $articleInfo['add_time']);
        $articleInfo['gallery'] = M('ArticlePhoto')->where(array('article_id' => $article_id))->order('sort desc')->select();  
        foreach($articleInfo['gallery'] as &$info){
            //$info['thumb_url'] = str_replace('style_org_img', 'style_thumb_img', $info['url']);
        }
        if($articleInfo['is_checked'] == 0){
            $this->error('文章未审核！');
            exit();
        }
        M('Article')->where(array('article_id' => $article_id))->setInc('visit_time');
        $this->articleInfo = $articleInfo;
        $this->display();
    }
}
