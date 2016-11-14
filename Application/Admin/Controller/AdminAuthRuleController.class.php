<?php

namespace Admin\Controller;

class AdminAuthRuleController extends CommonController {

    public function index() {
        $pid = I('get.pid',0,'intval');
        $authList = D('AdminAuthRule')->field('id,name,title,type,status,sort,pid,is_show')
                ->getRuleListByPid($pid);
        foreach($authList as &$info){
            if($info['name'] == ''){
                $info['url'] = U('AdminAuthRule/index?pid='.$info['id']);
            }else{
                $info['url'] = '';
            }
        }
        if($pid != 0){
            $pInfo = D('AdminAuthRule')->where(array('id' => $pid))->field('pid,title')->find();
            $this->prevUrl = U('AdminAuthRule/index?pid='.$pInfo['pid']);   
        }else{
            $this->prevUrl = '';
        }
        $this->authList = $authList;
        $this->pid = $pid;
        $this->display();
    }

    public function edit() {
        if (!IS_POST) {
            $authInfo = D('AdminAuthRule')->getAuthInfo(I('get.id'));
            if($authInfo === false){
                $error = D('AdminAuthRule')->getModelError();
                $this->error($error);                
            } elseif(empty($authInfo)) {
                $this->error('记录不存在');
            } else {
                $this->success($authInfo);
            }
        } else {
            $this->result = D('AdminAuthRule')->editAuthRule();
            if ($this->result === false) {
                $this->error(D('AdminAuthRule')->getModelError());
            } else {
                $this->success('编辑成功');
            }            
        }
    }

    public function add() {
        $result = D('AdminAuthRule')->addAuthRule();
        if ($result === false) {
            $this->error(D('AdminAuthRule')->getModelError());
        } else {
            $this->success('记录增加成功');
        }
    }

    public function del() {
        $result = D('AdminAuthRule')->delAuthRule();
        if ($result === false) {
            $this->error(D('AdminAuthRule')->getModelError());
        } else {
            $this->success('删除成功');
        }
    }    
    
}
