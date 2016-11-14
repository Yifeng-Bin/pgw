<?php
namespace Index\Widget;
use Think\Controller;
class IndexWidget extends Controller {
    
    //首页导航分类挂件
    public function photoList(){
        $content = S('cache:widget_index_pohto_list');
        //$content = false;
        if($content === false){
            $where = array(
                't.position_id' => 3,
                't.status' => 1,
                't.is_delete' => 0,
                'a.is_delete' => 0,
                't.start_time' => array(array('eq',0),array('gt',  gmtime()),'or'),
                't.end_time' => array(array('eq',0),array('lt',  gmtime()),'or'),
            );
            $photoList = M('TemplateData')->field('a.visit_time,a.article_id,a.article_name,a.main_image,t.data_image,t.data_text')->alias('t')
                    ->join('left join __ARTICLE__ as a on t.data_link = a.article_id')->limit('0,5')->order('t.sort desc')->where($where)->select();
            foreach($photoList as &$info){
                $info['url'] = U('Photo/detail?id='.$info['article_id']);
            }
            $this->photoList = $photoList;
            $content = $this->fetch('Widget:IndexPhotoList');
            S('cache:widget_index_pohto_list',$content,2*60*60 + 1);
        }
        echo $content;
    }
    
    //首页工人挂件
    public function workerList(){
        $content = S('cache:widget_index_worker_list:'.REGION_ID);
        
        $content = false;
        if($content === false){
            $where = array(
                't.status' => 1,
                't.is_delete' => 0,
                //'t.region_id' => REGION_ID,
                'u.user_type' => 1,
                'u.is_delete' => 0,
                'u.status' => 1,
                't.start_time' => array(array('eq',0),array('gt',  gmtime()),'or'),
                't.end_time' => array(array('eq',0),array('lt',  gmtime()),'or'),                
            );
            
            //已做代理的开放地区ID
            if(REGION_ID==794||REGION_ID==821){
                $where['t.region_id']=REGION_ID;
            }else{
                $where['t.region_id']=0;
            }
            
            $where['t.position_id'] = 5;
            $workerList_5 = M('TemplateData')->field('u.avatar,u.user_id,u.views,u.user_name,t.data_text,t.data_image')->alias('t')
                    ->join('left join __USERS__ as u on t.data_link = u.user_id')->limit('0,8')->order('t.sort desc')->where($where)->select();
            foreach($workerList_5 as &$info){
                $info['url'] = U('Worker/info?id='.$info['user_id']);
            }
            $this->workerList_5 = $workerList_5;
            
            
            $where['t.position_id'] = 6;
            $workerList_6 = M('TemplateData')->field('u.avatar,u.user_id,u.views,u.user_name,t.data_text,t.data_image')->alias('t')
                    ->join('left join __USERS__ as u on t.data_link = u.user_id')->limit('0,8')->order('t.sort desc')->where($where)->select();
            foreach($workerList_6 as &$info){
                $info['url'] = U('Worker/info?id='.$info['user_id']);
            }
            $this->workerList_6 = $workerList_6;


            $where['t.position_id'] = 7;
            $workerList_7 = M('TemplateData')->field('u.avatar,u.user_id,u.views,u.user_name,t.data_text,t.data_image')->alias('t')
                    ->join('left join __USERS__ as u on t.data_link = u.user_id')->limit('0,8')->order('t.sort desc')->where($where)->select();
            foreach($workerList_7 as &$info){
                $info['url'] = U('Worker/info?id='.$info['user_id']);
            }
            $this->workerList_7 = $workerList_7;
            
            $where['t.position_id'] = 8;
            $workerList_8 = M('TemplateData')->field('u.avatar,u.user_id,u.views,u.user_name,t.data_text,t.data_image')->alias('t')
                    ->join('left join __USERS__ as u on t.data_link = u.user_id')->limit('0,8')->order('t.sort desc')->where($where)->select();
            foreach($workerList_8 as &$info){
                $info['url'] = U('Worker/info?id='.$info['user_id']);
            }
            $this->workerList_8 = $workerList_8;
            
            $where['t.position_id'] = 9;
            $workerList_9 = M('TemplateData')->field('u.avatar,u.user_id,u.views,u.user_name,t.data_text,t.data_image')->alias('t')
                    ->join('left join __USERS__ as u on t.data_link = u.user_id')->limit('0,8')->order('t.sort desc')->where($where)->select();
            foreach($workerList_9 as &$info){
                $info['url'] = U('Worker/info?id='.$info['user_id']);
            }
            $this->workerList_9 = $workerList_9;
            
            $content = $this->fetch('Widget:IndexWorkerList');
            S('cache:widget_index_worker_list:'.REGION_ID,$content,4*60*60);
        }else{
            $content=444;
        }
        echo $content;
    }    
    
    //首页装修课堂挂件
    public function teachList(){
        $content = S('cache:widget_index_teach_list');
        //$content = false;
        if($content === false){
            $where = array(
                't.position_id' => 11,
                't.status' => 1,
                't.is_delete' => 0,
                'a.is_delete' => 0,
                't.start_time' => array(array('eq',0),array('gt',  gmtime()),'or'),
                't.end_time' => array(array('eq',0),array('lt',  gmtime()),'or'),
            );
            $teachList = M('TemplateData')->field('a.article_id,a.article_name,t.data_text')->alias('t')
                    ->join('left join __ARTICLE__ as a on t.data_link = a.article_id')->limit('0,13')->order('t.sort desc')->where($where)->select();
            foreach($teachList as &$info){
                $info['url'] = U('Teach/detail?id='.$info['article_id']);
            }
            $this->teachList = $teachList;
            $content = $this->fetch('Widget:IndexTeachList');
            S('cache:widget_index_teach_list',$content,2*60*60+3);
        }
        echo $content;
    }    
    
    //首页问答挂件
    public function askList(){
        $content = S('cache:widget_index_ask_list');
        //$content = false;
        if($content === false){
            $where = array(
                't.position_id' => 12,
                't.status' => 1,
                't.is_delete' => 0,
                'a.is_delete' => 0,
                'a.is_checked' => 1,
                't.start_time' => array(array('eq',0),array('gt',  gmtime()),'or'),
                't.end_time' => array(array('eq',0),array('lt',  gmtime()),'or'),
            );
            $askList = M('TemplateData')->field('a.article_id,a.article_name,t.data_text,a.is_resolved')->alias('t')
                    ->join('left join __ARTICLE__ as a on t.data_link = a.article_id')->limit('0,13')->order('t.sort desc')->where($where)->select();
            foreach($askList as &$info){
                $info['url'] = U('Ask/detail?id='.$info['article_id']);
            }
            $this->askList = $askList;
            $content = $this->fetch('Widget:IndexAskList');
            S('cache:widget_index_ask_list',$content,2*60*60+4);
        }
        echo $content;
    }  
    
    //首页日志挂件
    public function diaryList(){
        $content = S('cache:widget_index_diary_list');
        //$content = false;
        if($content === false){
            $where = array(
                't.position_id' => 13,
                't.status' => 1,
                't.is_delete' => 0,
                'a.is_delete' => 0,
                'a.is_checked' => 1,
                't.start_time' => array(array('eq',0),array('gt',  gmtime()),'or'),
                't.end_time' => array(array('eq',0),array('lt',  gmtime()),'or'),
            );
            $diaryList = M('TemplateData')->field('a.article_id,a.article_name,t.data_text,a.main_image,t.data_image')->alias('t')
                    ->join('left join __ARTICLE__ as a on t.data_link = a.article_id')->limit('0,6')->order('t.sort desc')->where($where)->select();
            foreach($diaryList as &$info){
                if(!empty($info['main_image'])){
                    $info['main_image'] = str_replace('style_org_img', 'style_thumb_img', $info['main_image']);
                }
                $info['url'] = U('Diary/detail?id='.$info['article_id']);
            }
            $this->diaryList = $diaryList;
            $content = $this->fetch('Widget:IndexDiaryList');
            S('cache:widget_index_diary_list',$content,2*60*60+5);
        }
        echo $content;
    }
    
    //首页需求挂件
    public function dynamicList(){
        $content = S('cache:widget_index_dynamic_list:'.REGION_ID);
        //判断是手机还是pc
        if(WEB_TYPE=='m'){
            $limit=8;
        }else{
            $limit=20;
        }
        //$content = false;
        if($content === false){
            $dynamicList = M('Tender')->alias('t')->field('t.tender_id,t.contacts,t.tender_name,wt.type_name,t.add_time,t.closing_time,t.bidder_num')
                    ->join('left join __WORK_TYPE__ as wt on t.work_type_id = wt.type_id')
                    ->where(array('t.tender_type' => 1,'t.is_checked' => 1,'t.status' => array('neq',0),'t.is_delete' => 0,'t.region_id' => REGION_ID))->order('t.add_time desc')->limit('0,'.$limit)->select();
            foreach($dynamicList as &$info){
                $info['content'] = substr_cut($info['contacts']).'在需求大厅发布了需求信息';
                $info['url'] = U('Requirement/detail?id='.$info['tender_id']);
            }            
            $this->dynamicList = $dynamicList;
            $content = $this->fetch('Widget:IndexDynamicList');
            S('cache:widget_index_dynamic_list:'.REGION_ID,$content,60);
        }
        echo $content;
    }     
}
