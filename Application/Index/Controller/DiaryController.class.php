<?php
namespace Index\Controller;
class DiaryController extends CommonController {
    public function index(){
        $this->pageHeadInfo = array(
            'title' => '装修日志-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        
        $where = array(
            'a.is_delete' => 0,
            'a.cat_id_1' => 52,
            'a.is_checked' => 1,
        );
        
        $count = M('Article')->alias('a')->where($where)->count();
        $Page = new \Think\Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        //$Page->lastSuffix = false;
        $Page->setConfig('first', '首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '…%TOTAL_PAGE%');
        $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
        $Page->rollPage = 11;
        $this->pageShow = $Page->show();        
        $diaryList = M('Article')->alias('a')->field('a.article_id,a.article_name,a.article_brief,a.comment_num,a.follow_num,a.visit_time,a.add_time,c.cat_name,u.user_id,u.avatar,u.user_name')
                ->join('left join __USERS__ as u on a.user_id = u.user_id')
                ->join('left join __ARTICLE_CATEGORY__ as c on a.cat_id = c.cat_id')
                ->limit($Page->firstRow . ',' . $Page->listRows)->order('add_time desc')->where($where)->select();
        foreach($diaryList as &$info){
            $info['add_time'] = local_date('Y-m-d', $info['add_time']);
            $info['url'] = U('Diary/detail?id='.$info['article_id']);
            $info['worker_url'] = U('Worker/info?id=' . $info['user_id']);
        }
        $this->diaryList = $diaryList;
        $this->display();        
    } 
    public function detail(){
        $this->pageHeadInfo = array(
            'title' => '装修日志-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        
        $id = I('get.id',0,'intval');
        $where = array(
            'a.is_delete' => 0,
            'a.is_checked' => 1,
            'a.cat_id_1' => 52,
            'a.article_id' => $id,
        );
        $diaryInfo = M('Article')->alias('a')->field('a.article_id,a.article_name,a.article_brief,a.article_keywords,a.cat_id,a.article_desc,a.visit_time,a.add_time,u.user_name,u.avatar')
                ->join('left join __USERS__ as u on a.user_id = u.user_id')
                ->where($where)->find();
        if(empty($diaryInfo)){
            $this->error('日志不存在');
            exit();
        }
        
        $this->pageHeadInfo = array(
            'title' => '装修日志-' . C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $diaryInfo['add_time'] = local_date('Y-m-d H:i:s', $diaryInfo['add_time']);
        
        $diaryInfo['comment_list'] = M('ArticleComment')->alias('ac')->join('left join __USERS__ as u on ac.user_id = u.user_id')
                ->field('ac.content,ac.add_time,u.user_name,u.avatar')->where(array('ac.article_id' => $id,'ac.is_checked' => 1))->order('add_time desc')->limit('0,50')->select();

        $this->diaryInfo = $diaryInfo;
        
        M('Article')->where(array('article_id' => $id))->setInc('visit_time');
        $this->display();
    }    
}