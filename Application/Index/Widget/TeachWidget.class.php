<?php
namespace Index\Widget;
use Think\Controller;
class TeachWidget extends Controller {

    //最新文章
    public function newArticle(){
        $content = S('cache:widget_teach_new_article');
        $content = false;
        if($content === false){
            $where = array(
                'is_delete' => 0,
                'cat_id_1' => 4,
                'is_checked' => 1,
            );
            $teachList = M('Article')->field('article_id,article_name')->where($where)->order('add_time desc')->limit('0,10')->select();
            foreach($teachList as &$info){
                $info['url'] = U('Teach/detail?id=' . $info['article_id']);
            }
            $this->teachList = $teachList;
            $content = $this->fetch('Widget:TeachNewArticle');
            S('cache:widget_teach_new_article',$content,60*60);
        }
        echo $content;
    }    
    
    //最新文章
    public function hotArticle(){
        $content = S('cache:widget_teach_hot_article');
        $content = false;
        if($content === false){
            $where = array(
                'is_delete' => 0,
                'cat_id_1' => 4,
                'is_checked' => 1,
            );
            $teachList = M('Article')->field('article_id,article_name')->where($where)->order('visit_time desc')->limit('0,10')->select();
            foreach($teachList as &$info){
                $info['url'] = U('Teach/detail?id=' . $info['article_id']);
            }
            $this->teachList = $teachList;
            $content = $this->fetch('Widget:TeachHotArticle');
            S('cache:widget_teach_hot_article',$content,60*60);
        }
        echo $content;
    }     
    
    //最新文章
    public function recommendArticle(){
        $content = S('cache:widget_teach_recommend_article');
        $content = false;
        if($content === false){
            $where = array(
                'is_delete' => 0,
                'cat_id_1' => 4,
                'is_checked' => 1,
            );
            $teachList = M('Article')->field('article_id,article_name')->where($where)->order('sort desc')->limit('0,10')->select();
            foreach($teachList as &$info){
                $info['url'] = U('Teach/detail?id=' . $info['article_id']);
            }
            $this->teachList = $teachList;
            $content = $this->fetch('Widget:TeachRecommendArticle');
            S('cache:widget_teach_recommend_article',$content,60*60);
        }
        echo $content;
    }         
}
