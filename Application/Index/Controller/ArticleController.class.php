<?php

namespace Index\Controller;

class ArticleController extends CommonController {

    //评价
    public function comment() {
        if(USER_ID == 0){
            $this->error('登录用户才能进行评论');
            exit();
        }
        $id = I('post.id',0,'intval');
        $where = array(
            'is_delete' => 0,
            'is_checked' => 1,
            'article_id' => $id,
        );        
        $arcitleInfo = M('Article')->field('user_id,cat_id_1')->where($where)->find();
        if(empty($arcitleInfo)){
            $this->error('评论内容已线下或不存在');
            exit();            
        }
        if($arcitleInfo['cat_id_1'] != 4 && $arcitleInfo['cat_id_1'] != 51  && $arcitleInfo['cat_id_1'] != 52 ){
            $this->error('不支持此功能');
            exit();            
        }        
        $verify = new \Think\Verify();
        $checked = $verify->check(I('post.verify_code', '', ''), 'comment');
        if (empty($checked)) {
            $this->error('验证码输入错误');
            exit();
        }        
        if($arcitleInfo['user_id'] == USER_ID){
            $this->error('不能对自己发布的内容进行评价');
            exit();            
        }
        $data = array(
            'article_id' => $id,
            'user_id' => USER_ID,
            'cat_id_1' => $arcitleInfo['cat_id_1'],
            'add_time' => gmtime(),
            'is_checked' => 0,
            'content' => I('post.comment_content','','strip_tags,htmlspecialchars'),
            'ip' => get_client_ip(),
        );
        if(empty($data['content'])){
            $this->error('评论内容不能为空');
            exit();              
        }else{
            $uncheck_comment_num = M('ArticleComment')->where(array('user_id' => USER_ID,'article_id' => $id,'is_checked' => 0))->count();
            if($uncheck_comment_num){
                $this->error('有待审核评论，请耐心等待管理员审核！');
                exit();                 
            }
            $result = M('ArticleComment')->add($data);
            
            //奖励积分 预赠送积分写入，后台审核后赠送
            if($arcitleInfo['cat_id_1'] == 51){
                $start_time = gmstr2time('today');
                $end_time = $start_time + 24 * 3600;
                $where =array(
                    'user_id' => USER_ID,
                    'add_time' => array(array('gt',$start_time),array('lt',$end_time),'and'),
                    'cat_id_1' => 51,
                    'comment_id' => array('ELT',$result),
                );
                $times = M('ArticleComment')->where($where)->count();
                $sql = M('ArticleComment')->getLastSql();
                if($times <= C('send_integral_times_by_answer_question')){
                    M('ArticleComment')->where(array('comment_id' => $result))->save(array('pre_integral' => C('send_integral_by_answer_question')));
                }
            }
            //奖励积分            
            $this->success('评论提交成功，请等待管理员审核！'.$sql);
            exit();             
        }
        
    }
    
    //选择问答的最佳答案
    public function choice() {
        if(USER_ID == 0){
            $this->error('登录用户才能进行评论');
            exit();
        }

        $comment_id = I('post.comment_id',0,'intval');
        
        $commentInfo = M('ArticleComment')->field('article_id,comment_id')->where(array('comment_id' => $comment_id))->find();  
        
        if(empty($commentInfo)){
            $this->error('评论记录不存在！');
            exit();            
        }
        
        $article_id = $commentInfo['article_id'];
        $where = array(
            'is_delete' => 0,
            'is_checked' => 1,
            'cat_id_1' => 51,
            'article_id' => $article_id,
        );        
        $arcitleInfo = M('Article')->field('article_id,is_resolved,best_comment_id,user_id')->where($where)->find();
        
        if(empty($arcitleInfo)){
            $this->error('问答信息不存在');
            exit();
        }
        
        if($arcitleInfo['user_id'] != USER_ID){
            $this->error('不能对别人的问题进行操作');
            exit();
        }

        if($arcitleInfo['is_resolved'] == 1){
            $this->error('您已经选择采纳的答案，无需重复提交！');
            exit();
        }

        M('Article')->where(array('article_id' => $arcitleInfo['article_id']))->save(array('is_resolved' => 1,'best_comment_id' => $commentInfo['comment_id']));
        $this->success('成功采纳！');
        exit();                 

    }    
    
    //文章关注
    public function follow(){
        if(USER_ID == 0){
            $this->error('登录用户才能进行关注操作');
            exit();
        }
        $id = I('post.id',0,'intval');
        $where = array(
            'is_delete' => 0,
            'is_checked' => 1,
            'article_id' => $id,
        );        
        $arcitleInfo = M('Article')->field('article_id,cat_id_1')->where($where)->find();
        if(empty($arcitleInfo)){
            $this->error('内容已线下或不存在');
            exit();
        }
        if($arcitleInfo['cat_id_1'] != 2 && $arcitleInfo['cat_id_1'] != 4  && $arcitleInfo['cat_id_1'] != 52 ){
            $this->error('不支持此功能');
            exit();            
        }        
        $where = array(
            'user_id' => USER_ID,
            'article_id' => $arcitleInfo['article_id'],
        );
        $is_exist = M('ArticleFollow')->where($where)->count();
        if(empty($is_exist)){
            $data = array(
                'user_id' => USER_ID,
                'cat_id' => $arcitleInfo['cat_id_1'],
                'article_id' => $arcitleInfo['article_id'],
                'add_time' => gmtime(),
            );
            $result = M('ArticleFollow')->add($data);
            if($result === false){
                $this->error('网站忙，请重试！');
                exit();
            }else{
                M('Article')->where(array('article_id' => $arcitleInfo['article_id']))->setInc('follow_num');
                $this->success('内容关注成功！');
                exit();                  
            }
        }else{
            $this->error('内容已关注');
            exit();             
        }      
    }
    
    public function commentlist(){
        $id = I('get.id',0,'intval');
        $where = array(
            'a.is_delete' => 0,
            'a.article_id' => $id,
        );
        $articleInfo = M('Article')->field('a.article_id')->alias('a')->where($where)->find();
        if(empty($articleInfo)){
            $this->error('信息不存在');
        }
        $count = M('ArticleComment')->where(array('article_id' => $id,'is_delete' => 0 ,'is_checked' => 1))->count();
        $Page = new \Think\Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        //$Page->lastSuffix = false;
        $Page->setConfig('first', '首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '…%TOTAL_PAGE%');
        $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
        $Page->rollPage = 11;
        $this->pageShow = $Page->show();        
        $commentList = M('ArticleComment')->alias('c')->field('c.content,u.user_id,u.avatar,u.user_name')
                ->join('left join __USERS__ as u on c.user_id = u.user_id')
                ->limit($Page->firstRow . ',' . $Page->listRows)->order('c.add_time desc')->where($where)->select();
        foreach($commentList as &$info){
            $info['add_time'] = local_date('Y-m-d H:i:s', $info['add_time']);
        }
        $this->commentList = $commentList;        
        $this->display();
    }
}
