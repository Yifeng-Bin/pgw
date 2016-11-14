<?php
namespace Index\Widget;
use Think\Controller;
class CommonWidget extends Controller {
    
    public function bottomHelp(){
        $content = S('cache:widget_common_bottom_help');
        //$content = false;
        if($content === false){
            $bottomxHelpList = array();
            $bottomxHelpList = M('ArticleCategory')->field('cat_id,cat_name')->where(array('parent_id' => 1))->order('sort desc')->select();
            foreach($bottomxHelpList as &$info){
                $info['list'] = M('Article')->field('article_id,article_name')->where(array('cat_id_2' => $info['cat_id'],'is_delete' => 0))->select();
                foreach($info['list'] as &$child){
                    $child['url'] = U('Help/detail?id='.$child['article_id']);
                }
            }
            $this->bottomHelpList = $bottomxHelpList;
            $content = $this->fetch('Widget:CommonBottomHelp');
            S('cache:widget_common_bottom_help',$content,3600);
        }
        echo $content;
    }
    
    //工人推荐
    public function recommendWorker(){
        $content = S('cache:widget_common_recommend_worker');
        $content = false;
        if($content === false){
            $where = array(
                'user_type' => 1,
                'region_id' => REGION_ID,
                'is_delete' => 0,
                'status' => 1,
            );
            $workerList = M('Users')->field('user_id,user_name,views')->where($where)->order('views desc')->limit('0,8')->select();
            foreach($workerList as &$info){
                $info['url'] = U('Worker/info?id='.$info['user_id']);
            }
            $this->workerList = $workerList;
            $content = $this->fetch('Widget:CommonRecommendWorker');
            S('cache:widget_common_recommend_worker',$content,60*60);
        }
        echo $content;
    }

    //派工教学
    public function recommendTeach(){
        $content = S('cache:widget_common_recommend_teach');
        $content = false;
        if($content === false){
            $where = array(
                'is_delete' => 0,
                'cat_id_1' => 4,
            );
            $teachList = M('Article')->field('article_id,article_name,visit_time')->where($where)->order('visit_time desc')->limit('0,8')->select();
            foreach($teachList as &$info){
                $info['url'] = U('Teach/detail?id=' . $info['article_id']);
            }
            $this->teachList = $teachList;
            $content = $this->fetch('Widget:CommonRecommendTeach');
            S('cache:widget_common_recommend_teach',$content,60*60);
        }
        echo $content;
    }    
}
