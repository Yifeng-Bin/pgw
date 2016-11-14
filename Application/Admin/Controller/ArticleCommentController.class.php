<?php

namespace Admin\Controller;

class ArticleCommentController extends CommonController {

    public function index() {
        //筛选
        $filter = array(
            'fields_name' => array(
                'is_checked' => array(
                    'desc'=>'是否已审核',
                    'type' => "select",
                     'values' => array('-1'=>"全部",'0'=>"未审核",'1'=>"已审核"),
                     'match_type' => 'eq',
                     'exclude' => array(-1),
                ),          
            ),
            'fields_alias' => array(
                'is_checked' => 'c',
            ),
            'fields_value' => array(
                'is_checked' => '-1',
            ),
            'order' => array(
                "fields" => array(
                    'add_time' => '增加时间',            
                ),
                'orderby' => 'add_time',
                'sortby' => 'desc',
            ),
        );    
        $where = array(                        'c.is_delete' => 0,
        );                $where['a.cat_id_1']  = array('neq',51);//排除问题回答列表
        foreach($filter['fields_name'] as $name => $info){
            $value =  I('get.filter_'.$name,'',  'htmlspecialchars');
            if($value !== ''){
                if(isset($info['exclude']) && in_array($value,$info['exclude'])){
                    continue;
                }  
                $filter['fields_value'][$name] = $value;
                if(isset($filter['fields_alias'][$name]) && !empty($filter['fields_alias'][$name])){
                    if($info['match_type'] == 'like'){
                        $where[$filter['fields_alias'][$name].".".$name] = array('like','%'.$value.'%');
                    }else{
                        $where[$filter['fields_alias'][$name].".".$name] = $value;
                    }                    
                }else{
                    if($info['match_type'] == 'like'){
                        $where[$name] = array('like','%'.$value.'%');
                    }else{
                        $where[$name] = $value;
                    }                    
                }
            }
        }
        $order = array();
        $orerby = I('get.orderby','','htmlspecialchars');
        $sortby = I('get.sortby','','htmlspecialchars');
        if(array_key_exists($orerby, $filter['order']['fields'])){
            $filter['order']['orderby'] = $orerby;
        }
        if(in_array(strtolower($sortby), array('desc','asc'))){
            $filter['order']['sortby'] = strtolower($sortby);
        }
        $order[$filter['order']['orderby']] = $filter['order']['sortby'];
        //筛选
        
        $page = new \Com\Logdd\Page(D('ArticleComment')->alias('c')->join('__ARTICLE__ as a on c.article_id = a.article_id')->where($where)->count(), 50);
        $this->pageInfo = $page->getPageInfo();
        $commentList = D('ArticleComment')->alias('c')->join('left join __ARTICLE__ as a on c.article_id = a.article_id')                                ->join('left join __ARTICLE_CATEGORY__ as g on a.cat_id = g.cat_id')
                        ->field('c.comment_id,c.article_id,c.user_id,c.add_time,c.is_checked,c.content,c.ip,a.article_name,g.cat_name')
                        ->where($where)->limit($page->firstRow.",".$page->listRows)->order($order)->select();
        foreach($commentList as &$commentInfo){
            $commentInfo['add_time'] = local_date('Y-m-d H:i:s', $commentInfo['add_time']);
        }
        $this->commentList = $commentList;
        $this->filter = $filter;
        
        $this->display();
    }  
    
    public function changeCommentStatus(){
        $comment_ids = I('post.comment_ids', array(), 'intval');
        $comment_ids = array_unique($comment_ids);
        $status = I('post.status', 0, 'intval');
        
        $where = array(
            'comment_id' => array('in', $comment_ids),
        );
        //赠送积分 审核的赠送积分并清零预分配积分
        if($status == 1){
            $preIntegralList = M('ArticleComment')->alias('c')->join('left join __ARTICLE__ as a on c.article_id = a.article_id')
                    ->field('c.comment_id,c.user_id,c.pre_integral,c.article_id,a.article_name')->where(array('c.comment_id' => array('in', $comment_ids),'c.pre_integral' => array('gt',0)))->select();
            foreach($preIntegralList as $info){
                $data = array(
                    'user_id' => $info['user_id'],
                    'change_type' => 2,
                    'change_desc' => '回答问题',
                    'change_time' => gmtime(),
                    'id' => $info['comment_id'],
                    'integral' => $info['pre_integral'],
                    'in_out' => 0,
                );
                M('IntegralLog')->add($data);
                M('Users')->where(array('user_id' => $info['user_id']))->save(array('gold' => array('exp',"gold+".$info['pre_integral'])));
                M('ArticleComment')->where(array('comment_id' => $info['comment_id']))->save(array('pre_integral' => 0));
            }            
        }
        //赠送积分
        $is_checked = ($status == 1 ?  0 : 1);
        $where['is_checked'] = $is_checked;
        $articleCommentNum = M('ArticleComment')->where($where)->field('article_id,count(article_id) as num')->group('article_id')->select();
        foreach($articleCommentNum as $info){
            if($status == 1){
                M('Article')->where(array('article_id' => $info['article_id']))->setInc('comment_num',$info['num']);
            }else{
                M('Article')->where(array('article_id' => $info['article_id'],'comment_num' => array('egt',$info['num'])))->setDec('comment_num',$info['num']);
            }
        }
        M('ArticleComment')->where($where)->data(array('is_checked' => $status))->save();
        $this->success('审核成功');
    }            public function del() {            $comment_id = I('post.comment_id', 0, 'intval');            if(M('ArticleComment')->data(array('is_delete' => 1))->where(array('comment_id' => $comment_id))->save()){                        admin_log("删除成功");                        $this->success('删除成功');        }else{                        admin_log("删除失败");                        $this->error($comment_id);                    }                    }                public function edit() {            if (IS_POST) {                $comment_id = I('post.comment_id', '0', 'intval');                $data = array(                    'content' => I('post.content', '', 'htmlspecialchars'),                    'is_checked' => I('post.is_checked', 0, 'intval'),                );                if(empty($data['content'])){                    $this->error = '答案内容不能为空！';            }                $go_url = U('ArticleComment/index');                if(M('ArticleComment')->where(array('comment_id' => $comment_id))->save($data)){                    $this->success('答案修改成功', $go_url);                }else{                    admin_log("答案修改失败");                    $this->error('答案修改失败');                    return false;                }                }else{                $comment_id = I('get.comment_id', 0, 'intval');                         $this->commentInfo =  M('ArticleComment')->alias('c')->join('__USERS__ as u on c.user_id = u.user_id')->join('__ARTICLE__ as a on c.article_id = a.article_id')                ->field('c.comment_id,c.article_id,c.user_id,c.add_time,c.is_checked,c.content,c.ip,u.user_name,a.article_name')                ->where(array('c.is_delete' => 0,'c.comment_id' => $comment_id))->find();                if (empty($this->commentInfo)) {                    $this->error('答案不存在');                    exit();            }                $this->display();            }            }               
}
