<?php

namespace Admin\Controller;

class RequirementController extends CommonController {

    public function index() {
        $where = array(
            't.is_delete' => 0,
        );
        //筛选
        $filter = array(
            'fields_name' => array(
                'tender_name' => array(
                    'desc' => '项目名称',
                    'type' => "input",
                    'match_type' => 'like',
                ),
                'is_checked' => array(
                    'desc' => '是否审核',
                    'type' => "select",
                    'values' => array(array('value' => '', 'desc' => '不选'), array('value' => '1', 'desc' => '已审核'), array('value' =>'0', 'desc' => '未审核')),
                    'match_type' => 'eq',
                ),
            ),
            'fields_alias' => array(
                'tender_name' => '',
                'is_checked' => '',
            ),
            'fields_value' => array(
                'tender_name' => '',
                'is_checked' => '',
            ),
            'order' => array(
                "fields" => array(
                    'add_time' => '发布时间',
                ),
                'orderby' => 'add_time',
                'sortby' => 'desc',
            ),
        );
        foreach ($filter['fields_name'] as $name => $info) {
            $value = I('get.filter_' . $name, '', 'htmlspecialchars');
            if ($value !== '') {
                $filter['fields_value'][$name] = $value;
                if (isset($filter['fields_alias'][$name]) && !empty($filter['fields_alias'][$name])) {
                    if ($info['match_type'] == 'like') {
                        $where[$filter['fields_alias'][$name] . "." . $name] = array('like', '%' . $value . '%');
                    } else {
                        $where[$filter['fields_alias'][$name] . "." . $name] = $value;
                    }
                } else {
                    if ($info['match_type'] == 'like') {
                        $where[$name] = array('like', '%' . $value . '%');
                    } else {
                        $where[$name] = $value;
                    }
                }
            }else{
                $filter['fields_value'][$name] = '';
            }
        }
        $order = array();
        $orerby = I('get.orderby', '', 'htmlspecialchars');
        $sortby = I('get.sortby', '', 'htmlspecialchars');
        if (array_key_exists($orerby, $filter['order']['fields'])) {
            $filter['order']['orderby'] = $orerby;
        }
        if (in_array(strtolower($sortby), array('desc', 'asc'))) {
            $filter['order']['sortby'] = strtolower($sortby);
        }
        $order[$filter['order']['orderby']] = $filter['order']['sortby'];
        //筛选

        $page = new \Com\Logdd\Page(M('Tender')->alias('t')->where($where)->count(), 20);
        $this->pageInfo = $page->getPageInfo();
        $tenderList = M('Tender')->alias('t')->field('t.tender_id,t.tender_name,t.work_type_id,t.region_id,t.user_id,t.bidder_id,t.contacts,t.mobile,t.budget,t.days,t.is_checked,t.status,t.add_time,t.work_rank,wt.type_name,r.region_name,u.user_name')
                        ->join('left join __WORK_TYPE__ as wt on wt.type_id = t.work_type_id')
                        ->join('left join __REGION__ as r on r.region_id = t.region_id')
                        ->join('left join __USERS__ as u on u.user_id = t.user_id')
                        ->where($where)->order($order)->limit($page->firstRow . "," . $page->listRows)->select();
        foreach ($tenderList as &$list) {
            $list['add_time'] = local_date('Y-m-d H:i:s', $list['add_time']);
            //$list['work_type_name'] = M('');
        }
        $this->tenderList = $tenderList;
        $this->filter = $filter;
        session("goUrl.tenderListUrl", get_page_url());
        $this->display();
    }

    public function add() {
        if (IS_POST) {
            $act = I('post.act', '');
            if ($act == 'upload_attachment') {
                $user_id = I('post.user_id',0,'intval');
                $user_id = M("Users")->where(array('user_id' => $user_id))->getField('user_id');
                if(empty($user_id)){
                    $this->error('用户不存在'); 
                    exit();                      
                }
                $upload = new \Think\Upload();
                $upload->maxSize   =     1024*1024 ;// 设置附件上传大小 1M
                $upload->exts = array('jpg', 'bmp', 'png'); // 设置附件上传类型
                $upload->rootPath = 'userfiles/'; // 设置附件上传根目录         
                $upload->savePath = $user_id.'/requirement/'; // 设置附件上传根目录         
                $info = $upload->uploadOne($_FILES['Filedata']);
                $path = $upload->rootPath . $info['savepath'] . $info['savename'];
                if (empty($info)) {
                    $this->error($upload->getError(), '', true);
                } else {
                    $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 2);
                    if($result !== false){
                        $url = $result['url'];
                        $arr = array(
                            'user_id' => $user_id,
                            'url' => $url,
                            'path' => $path,
                        );
                        $attachment_id = M('TenderAttachment')->add($arr);
                        if($attachment_id){
                            $result['attachment_id'] = $attachment_id;
                            unlink($path);
                            $this->success($result); 
                        }else{
                            $this->error('服务器处理错误，请重试'); 
                            exit();                        
                        }
                    }else{
                        $this->error('文件上传出现错误'); 
                        exit();
                    }
                }                
            }
            elseif($act == 'search_user'){
                $user_name = I('post.user_name','','trim');
                $userList = M('UserAuths')->alias('ua')->join('left join __USERS__ as u on u.user_id = ua.user_id')
                        ->where(array('ua.user_name' => array('like','%'.$user_name.'%')))->limit('0,20')->getField('ua.user_id,u.user_name,u.mobile',':');
                $this->success($userList);
                exit();
            }else{
                $result = D('Tender')->addInfo();
                if (($result === false)) {
                    $this->error(D('Tender')->getModelError());
                } else {
                    $go_url = U('Requirement/index');
                    if (session("goUrl.tenderListUrl")) {
                        $go_url = htmlentities(session("goUrl.tenderListUrl"));
                    }
                    $this->success('增加成功', $go_url);
                }                
            }
        } else {
            $user_id = I('get.user_id',0,'intval');
            $userInfo = M("Users")->field('user_id')->where(array('user_id' => $user_id))->find();
            if(empty($userInfo)){
                $this->error('请选择用户后再进行添加');
                exit();
            }else{
               $this->user_id = $user_id;
               $this->display(); 
            }
        }
    }

    public function edit() {
        if (IS_POST) {
            $act = I('post.act', '');
            if ($act == 'upload_attachment') {
                $user_id = I('post.user_id',0,'intval');
                $user_id = M("Users")->where(array('user_id' => $user_id))->getField('user_id');
                if(empty($user_id)){
                    $this->error('用户不存在'); 
                    exit();                      
                }
                $upload = new \Think\Upload();
                $upload->maxSize   =     1024*1024 ;// 设置附件上传大小 1M
                $upload->exts = array('jpg', 'bmp', 'png'); // 设置附件上传类型
                $upload->rootPath = 'userfiles/'; // 设置附件上传根目录         
                $upload->savePath = $user_id.'/requirement/'; // 设置附件上传根目录         
                $info = $upload->uploadOne($_FILES['Filedata']);
                $path = $upload->rootPath . $info['savepath'] . $info['savename'];
                if (empty($info)) {
                    $this->error($upload->getError(), '', true);
                } else {
                    $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 2);                      
                    if($result !== false){
                        $url = $result['url'];
                        $arr = array(
                            'user_id' => $user_id,
                            'url' => $url,
                            'path' => $path,
                        );
                        $attachment_id = M('TenderAttachment')->add($arr);
                        if($attachment_id){
                            $result['attachment_id'] = $attachment_id;
                            unlink($path);
                            $this->success($result); 
                        }else{
                            $this->error('服务器处理错误，请重试'); 
                            exit();                        
                        }
                    }else{
                        $this->error('文件上传出现错误'); 
                        exit();
                    }
                }                
            }else{
                $result = D('Tender')->editInfo();
                if (($result === false)) {
                    $this->error(D('Tender')->getModelError());
                } else {
                    $go_url = U('Requirement/index');
                    if (session("goUrl.tenderListUrl")) {
                        $go_url = htmlentities(session("goUrl.tenderListUrl"));
                    }
                    $this->success('编辑成功', $go_url);
                }                
            }
        } else {
            $tender_id = I('get.id', 0, 'intval');
            $this->tenderInfo = D('Tender')->where(array('is_delete' => 0))->getInfo($tender_id);
            if (empty($this->tenderInfo)) {
                $this->error('需求不存在');
            }
            $this->display();
        }
    }
    
    public function del() {
        $tender_id = I('post.id', 0, 'intval');
        D('Tender')->data(array('is_delete' => 1))->where(array('tender_id' => $tender_id))->save();
        admin_log("需求删除成功");
        $this->success('删除成功');
    }

}
