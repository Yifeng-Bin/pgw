<?php

namespace Admin\Controller;

class TemplateDataController extends CommonController {

    protected $positionTypeDesc = array(
        'link' => '链接',
        'worker' => '工人',
        'photo' => '效果图',
        'teach' => '课堂',
        'ask' => '问答',
        'diary' => '日志',
    );
    public function index() {
        $page = new \Com\Logdd\Page(D('TemplateDataPosition')->count(), 50);
        $this->pageInfo = $page->getPageInfo();
        $templateDataPositionList = D('TemplateDataPosition')->field('*')->getList($page->firstRow, $page->listRows);
        foreach($templateDataPositionList as &$templateDataPositionInfo){
            $templateDataPositionInfo['position_type_desc'] = $this->positionTypeDesc[$templateDataPositionInfo['position_type']];
        }
        $this->templateDataPositionList = $templateDataPositionList;
        $this->assign('positionTypeDesc',$this->positionTypeDesc);
        $this->display();
    }

    public function editPosition() {
        $position_id = I('get.position_id', 0, 'intval');
        if (!IS_POST) {
            $positionInfo = D('TemplateDataPosition')->getTemplateDataPosition($position_id);
            if (empty($positionInfo)) {
                $this->error('模版位置不存在');
            } else {
                $this->success($positionInfo);
            }
        } else {
            $result = D('TemplateDataPosition')->editTemplateDataPosition();
            if ($result === false) {
                $this->error(D("TemplateDataPosition")->getModelError());
            } else {
                $this->success('模版位置编辑成功');
            }
        }
    }

    public function addPosition() {
        if (IS_POST) {
            $result = D('TemplateDataPosition')->addTemplateDataPosition();
            if ($result === false) {
                $this->error(D("TemplateDataPosition")->getModelError());
            } else {
                $this->success('模版位置增加成功');
            }
        }
    }

    public function delPosition() {
        $result = D('TemplateDataPosition')->delTemplateDataPosition();
        if (empty($result)) {
            $this->error('删除失败');
        } else {
            $this->success('删除成功');
        }
    }

    public function dataList() {
        $position_id = intval(I('get.position_id'));
        $page = new \Com\Logdd\Page(D('TemplateData')->where(array('position_id'=>$position_id,'is_delete' => 0))->count(), 50);
        $this->pageInfo = $page->getPageInfo();
        $this->position_id = $position_id;
        $this->positionInfo = D('TemplateDataPosition')->getTemplateDataPosition($position_id);
        $this->list = D('TemplateData')->field('*')->where(array('position_id'=>$position_id,'is_delete' => 0))
        ->getList($page->firstRow, $page->listRows); 
             
        $regionList = D('Region')->getAllRegion(3);                        
        array_unshift($regionList,array('region_id'=> '0' ,'region_name'=>"总站",'parent_id'=>"0",'level'=>"0"));       
        $this->regionList = $regionList;
        
        $worktypeList = D('WorkType')->getList();
        array_unshift($worktypeList,array('type_id'=> '0' ,'type_name'=>"请选择工种"));
        $this->worktypeList = $worktypeList;
        
        
        $this->display();
    }
    public function add() {
        if (IS_POST) {
            $act = I('post.act','');
            if($act == 'query'){
                $result = $this->search();
                $this->success($result);
                exit();
            }else{
                $result = D('TemplateData')->addTemplateData();
                if ($result === false) {
                    $this->error(D("TemplateData")->getModelError());
                } else {
                    $this->success('模版数据增加成功');
                }                
            }

        }        
    }
    public function edit() {
        $data_id = I('request.data_id', 0, 'intval');
        if (!IS_POST) {
            $info = D('TemplateData')->getTemplateData($data_id);
            if (empty($info)) {
                $this->error('模版数据不存在');
            } else {
                $info['position_type'] = M('TemplateDataPosition')->where(array('position_id' => $info['position_id']))->getField('position_type');
                if($info['position_type'] == 'worker'){
                    $info['select_value'] = M('Users')->where(array('user_id' => $info['data_link']))->getField('user_id,user_name,mobile',':');
                    $info['select_value'] = $info['select_value'][$info['data_link']];
                }elseif($info['position_type'] == 'photo' || $info['position_type'] == 'teach' || $info['position_type'] == 'ask'){
                    $info['select_value'] = M('Article')->where(array('article_id' => $info['data_link']))->getField('article_name');
                }
                $this->success($info);
            }
        } else {
            $act = I('post.act','');
            if($act == 'query'){
                $result = $this->search();
                $this->success($result);
                exit();
            }else{
                $result = D('TemplateData')->editTemplateData();
                if ($result === false) {
                    $this->error(D("TemplateData")->getModelError());
                    $this->error('模版数据编辑失败');
                } else {
                    $this->success('模版数据编辑成功');
                }              
            }
        }        
    }
    public function del() {
        $result = D('TemplateData')->delTemplateData();
        if (empty($result)) {
            $this->error('删除失败');
        } else {
            $this->success('删除成功');
        }
    }
    
    protected function search() {
        $type = I('post.type','');
        if(!in_array($type, array_keys($this->positionTypeDesc))){
            $this->error('类型错误');
        }
        $query = I('post.query','');
        $region_id = intval(I('post.region_id'));
 
        if($type == 'worker'){       
            $type_id = intval(I('post.type_id'));
            if(empty($type_id)||$type_id==0){
                $this->error('请选择工种！');
            }
            $where = array(
                'u.user_type' => 1,
                'u.is_delete' => 0,
                'u.status' => 1,
                'u.region_id' =>$region_id,
                'w.type_id_1' =>$type_id,
                'u.user_name|u.mobile' => array('like',"%".$query."%"),
            );
            $result = M('Users')->alias('u')->join('left join __USER_TO_WORK_TYPE__ as w on u.user_id = w.user_id')->where($where)->limit('0,40')->getField('u.user_id,u.user_name,u.mobile',':');
        }elseif($type == 'photo'){
            $cat_id = 2;
            $cat_ids = $this->getAllChildCatIds($cat_id);
            $where = array(
                'cat_id' => array('in',$cat_ids),
                'is_delete' => 0,
                'article_name' => array('like',"%".$query."%"),
            );
            $result = M('Article')->where($where)->limit('0,20')->getField('article_id,article_name');              
        }elseif($type == 'teach'){
            $cat_id = 4;
            $cat_ids = $this->getAllChildCatIds($cat_id);            
            $where = array(
                'cat_id' =>  array('in',$cat_ids),
                'is_delete' => 0,
                'article_name' => array('like',"%".$query."%"),
            );
            $result = M('Article')->where($where)->limit('0,20')->getField('article_id,article_name');            
        }elseif($type == 'ask'){
            $cat_id = 51;
            $cat_ids = $this->getAllChildCatIds($cat_id);            
            $where = array(
                'cat_id' =>  array('in',$cat_ids),
                'is_delete' => 0,
                'article_name' => array('like',"%".$query."%"),
            );
            $result = M('Article')->where($where)->limit('0,20')->getField('article_id,article_name');            
        }elseif($type == 'diary'){
            $cat_id = 52;
            $cat_ids = $this->getAllChildCatIds($cat_id);            
            $where = array(
                'cat_id' =>  array('in',$cat_ids),
                'is_delete' => 0,
                'article_name' => array('like',"%".$query."%"),
            );
            $result = M('Article')->where($where)->limit('0,20')->getField('article_id,article_name');            
        }
        return $result;
    } 
    
    protected function getAllChildCatIds($cat_id = 0){
        $arr[] = $cat_id;
        $pArr = $arr;
        while(true){
            $children = M('ArticleCategory')->where(array('parent_id' => array('in',$pArr)))->getField('cat_id',true);
            if(empty($children)){
                break;
            }else{
                $arr = array_merge($arr,$children);
                $pArr = $children;
            }
        }
        return $arr;    //  返回字符串
    }   
}
