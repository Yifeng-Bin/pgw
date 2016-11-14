<?php
namespace Index\Controller;
class WorkerController extends CommonController {
    public function type(){
        $this->pageHeadInfo = array(
            'title' => '找工人-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $id = I('get.id',0,'intval');
        $workTypeInfo = M('WorkType')->field('type_id,type_name')->where(array('type_id' => $id,'status' => 1,'level' => 1))->find();
        if(empty($workTypeInfo)){
            $this->error('工种不存在！');
        }
        $cid = I('get.cid',0,'intval');
        $childTypeInfo = M('WorkType')->field('type_id,type_name')->where(array('type_id' => $cid,'status' => 1,'level' => 2))->find();
        if(empty($childTypeInfo)){
            $cid = 0;
        }
        $work_rank = I('get.type',0,'intval');
        if(!in_array($work_rank, array(0,1,2))){
            $work_rank = 0;
        }
        
        //排序
        $default_sort = array('addtime','desc');
        $allowed_sort_arr = array(        
            'addtime' => array(
                'default' => 'desc',
                'list' => array('desc'),
                'text' => '默认',
            ),
            'num' => array(
                'default' => 'desc',
                'list' => array('desc'),
                'text' => '签单',
            ),  
            'score' => array(
                'default' => 'desc',
                'list' => array('desc'),
                'text' => '口碑',
            ),              
        );
        $sort_fields = array(
            'addtime' => 'add_time',            
            'num' => 'tenders_finish_num',
            'score' => 'score1',
        );
        $sort = I('get.sort', '', '');
        $sort = explode('_', $sort);
        if(empty($sort) || (!isset($allowed_sort_arr[$sort[0]])) || (!in_array($sort[1], $allowed_sort_arr[$sort[0]]['list']))){
            $sort = $default_sort;
        }
        $sortby = $sort_fields[$sort[0]].' '.$sort[1];
        //排序   
        
        $getAttr = array();
        $getAttr['id'] = $id;
        $getAttr['cid'] = $cid;
        $getAttr['type'] = $work_rank;
        $getAttr['sort'] = $sort[0].'_'.$sort[1];
        
        $this->workTypeInfo = $workTypeInfo;
        
        $childWorkList =  M('WorkType')->field('type_id,type_name')->where(array('parent_id' => $id,'status' => 1,'level' => 2))->order('sort asc')->select();
       // var_dump($childWorkList);die();
        array_unshift($childWorkList,array('type_id' => 0,'type_name' => '不选')); 
        $getAttrTemp = $getAttr;
        foreach($childWorkList as &$info){
            if($getAttr['cid'] == $info['type_id']){
                $info['is_current'] = 1;
            }else{
                $info['is_current'] = 0;
            }
            $getAttrTemp['cid'] = $info['type_id'];
            $info['url'] = U('Worker/type',$getAttrTemp); 
        }
        unset($getAttrTemp);
        $this->childWorkList = $childWorkList;
        
        $rankList = array(
            array('rank_id'=> 0,'rank_name' => '不选'),
            array('rank_id'=> 1,'rank_name' => '普通工人'),
            array('rank_id'=> 2,'rank_name' => 'VIP工人'),
        );
        $getAttrTemp = $getAttr;
        foreach($rankList as &$info){
            if($getAttr['type'] == $info['rank_id']){
                $info['is_current'] = 1;
            }else{
                $info['is_current'] = 0;
            }
            $getAttrTemp['type'] = $info['rank_id'];
            $info['url'] = U('Worker/type',$getAttrTemp); 
        }
        unset($getAttrTemp);
        $this->rankList = $rankList;     
        
        $getAttrTemp = $getAttr;
        foreach ($allowed_sort_arr as $k => $sortInfo) {
            $sortArr[$k]['selected'] = ($k == $sort[0]) ? 1 : 0;
            if($sortArr[$k]['selected']){
                $key = array_search($sort[1], $sortInfo['list']);
                if($key !== false){
                    unset($sortInfo['list'][$key]);
                }
                if(empty($sortInfo['list'])){
                    $getAttrTemp['sort'] = $k."_".$sortInfo['default'];
                }else{
                    $getAttrTemp['sort'] = $k."_".array_shift($sortInfo['list']);
                }
            }else{
                $getAttrTemp['sort'] = $k."_".$sortInfo['default'];
            }
            $sortArr[$k]['name'] = $sortInfo['text'];
            $sortArr[$k]['url'] = U('Worker/type',$getAttrTemp);
        }
        $this->sortArr = $sortArr;       
        
        
        $where = array(
            'u.is_delete' => 0,
            'u.status' => 1,
            'u.user_type' => 1,
            'wt.type_id_1' => $getAttr['id'],
            'u.region_id' => REGION_ID,
        );
        if(!empty($getAttr['cid'])){
            $where['wt.type_id_2'] = $getAttr['cid'];
        }
        if(!empty($getAttr['type'])){
            $where['u.work_rank'] = $getAttr['type'];
        }    
        $count  = M('Users')->alias('u')->join('left join __USER_TO_WORK_TYPE__ as wt on u.user_id = wt.user_id')->where($where)->count('distinct u.user_id');
        $Page  = new \Think\Page($count,10);
        //$Page->lastSuffix = false;
        $Page->setConfig('first', '首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '…%TOTAL_PAGE%');
        $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
        $Page->rollPage = 11;
        $this->pageShow = $Page->show();
        $workerList = M('Users')->alias('u')->field('u.user_id,u.avatar,u.user_name,u.real_name,u.day_price,u.enter_year,u.case_num,u.profile,u.service_idea,u.qq,u.score1,u.score2,u.score3,u.tenders_take_num')
                ->join('left join __USER_TO_WORK_TYPE__ as wt on u.user_id = wt.user_id')->where($where)->group('u.user_id')
                ->limit($Page->firstRow.','.$Page->listRows)->order($sortby)->where($where)->select();
        foreach($workerList as &$info){
            $info['url'] = U('Worker/info?id='.$info['user_id']);
        }
        $this->workerList = $workerList;        
        $this->getAttr = $getAttr;
        $this->city_id = M('Region')->where(array('region_id' => REGION_ID))->getField('parent_id');
        $this->province_id = M('Region')->where(array('region_id' => $this->city_id))->getField('parent_id');        
        $this->display();
    }
    
    public function info(){
        $this->pageHeadInfo = array(
            'title' => '详情页面-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $id = I('get.id',0,'intval');
        $where = array(
            'user_id' => $id,
            'user_type' => 1,
            'status' => 1,
            'is_delete' => 0,
        );
        $workerInfo = M('Users')->field('user_id,avatar,user_name,real_name,day_price,attention_num,yuyue_num,case_num,mobile,region_id,qq,score1,score2,score3,is_verified,is_safe,money,work_rank,enter_year,service_idea,profile')
                ->where($where)->find();
        if(empty($workerInfo)){
            $this->error('工人信息不存在');
            exit();
        }
        $workerInfo['is_followed'] = M('Follow')->where(array('user_id' => USER_ID,'follow_user_id' => $workerInfo['user_id']))->count();
        $workerInfo['workTypeList'] = M('UserToWorkType')->alias('uk')->join('left join __WORK_TYPE__ as t on uk.type_id_2 = t.type_id')->where(array('user_id' => USER_ID))->getField('type_name',true);
        $workerInfo['workTypeDesc'] = implode(' ', $workerInfo['workTypeList']);
        $this->city_id = M('Region')->where(array('region_id' => $workerInfo['region_id']))->getField('parent_id');
        $this->province_id = M('Region')->where(array('region_id' => $this->city_id))->getField('parent_id');       
        M('Users')->where(array('user_id' => $id))->setInc('views');  
        $where = array(
          'a.is_delete' => 0,
          'a.cat_id_1' => 49,  
          'is_checked' => 1,
          'user_id' => $id,
        );        
        $caseList = M('Article')->alias('a')->field('article_id,article_name,main_image,follow_num')->where($where)->limit('0,4')->select();
        foreach($caseList as &$info){
            $info['url'] = U('Case/detail?id='.$info['article_id']);
        } 
        if(empty($caseList)){
            $workerInfo['case'] = array();;
        }else{
            $workerInfo['case'] = $caseList;
        }
        $where = array(
            'bidder_id' => $id,
            'comment_score1' => array('gt',0),
        );
        $commentList = M('Tender')->alias('t')->join('left join __USERS__ as u on t.bidder_id = u.user_id')
                ->field('t.comment_score1,t.user_id,t.comment_score2,t.comment_score3,t.comment_content,t.comment_time,u.avatar')->where($where)->limit('0,10')->order('tender_id desc')->select();
        foreach($commentList as &$info){
            $info['comment_time'] = local_date('Y-m-d H:i', $info['comment_time']);
        } 
        if(empty($commentList)){
            $workerInfo['commentList'] = array();
        }else{
            $workerInfo['commentList'] = $commentList;
        }        
        $this->workerInfo = $workerInfo;
        $this->display();
    }
    
    public function follow(){
        if (!USER_ID) {
            $this->error('请登录后再进行本操作');
        }
        $user_id = I('post.user_id',0,'intval');
        $userInfo = M('Users')->field('user_id,user_type')->where(array('user_id' => $user_id,'user_type' => 1))->find();
        if(empty($userInfo)){
            $this->error('工人不存在');
            exit();
        }
        if(USER_ID == $user_id){
            $this->error('无需关注自己！');
            exit();
        }        
        $is_exist = M('Follow')->where(array('user_id' => USER_ID,'follow_user_id' => $user_id))->count();
        if(!empty($is_exist)){
            $this->error('已关注此工人，无需重复操作！');
            exit();
        }else{
            M('Follow')->add(array('user_id' => USER_ID,'follow_user_id' => $user_id,'add_time'=>  gmtime()));
            $this->success('已成功关注此工人！');
            exit();
        }
    }
    
    
    public function comment(){
        $this->pageHeadInfo = array(
            'title' => '评价列表-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $user_id = I('get.user_id',0,'intval');
        
        $where = array(
            'bidder_id' => $user_id,
            'comment_score1' => array('gt',0),
        );
        $count  = M('Tender')->alias('t')->where($where)->count();
        $Page  = new \Think\Page($count,10);
        //$Page->lastSuffix = false;
        $Page->setConfig('first', '首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '…%TOTAL_PAGE%');
        $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
        $Page->rollPage = 11;
        $this->pageShow = $Page->show();
        
        $commentList = M('Tender')->alias('t')->join('left join __USERS__ as u on t.user_id = u.user_id')
                ->field('t.comment_score1,t.comment_score2,t.comment_score3,t.comment_content,t.comment_time,u.avatar,u.user_name')
                ->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($commentList as &$info){
            $info['comment_time'] = local_date('Y-m-d H:i', $info['comment_time']);
        } 

        $this->commentList = $commentList;
        $this->display();        
    }
}