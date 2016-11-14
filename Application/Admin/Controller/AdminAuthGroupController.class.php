<?php

namespace Admin\Controller;

class AdminAuthGroupController extends CommonController {

    public function index() {
        $this->groupList = D('AdminAuthGroup')->field('*')->where(array('is_delete' => 0))->select();
        $this->menusList = $this->getMenu();
        $this->display();
    }

    public function edit() {
        if (!IS_POST) {
            $groupInfo = D('AdminAuthGroup')->getGroupInfo(I('get.id'));
            if($groupInfo === false){
                $error = D('AdminAuthGroup')->getModelError();
                $this->error($error);                
            } elseif (empty($groupInfo)) {
                $this->error('记录不存在');
            } else {
                $this->success($groupInfo);
            }
        } else {
            $result = D('AdminAuthGroup')->editGroupInfo();
            if ($result === false) {
                $error = D('AdminAuthGroup')->getModelError();
                if(empty($error)){
                   $error = '编辑失败';
                }
                $this->error($error);
            } else {
                $this->success('编辑成功');
            }
        }
    }

    public function add() {
        if (IS_POST) {
            $result = D('AdminAuthGroup')->addAuthGroup();
            if (empty($result)) {
                $error = D('AdminAuthGroup')->getModelError();
                if(empty($error)){
                   $error = '添加失败';
                }
                $this->error($error);
            } else {
                $this->success('记录增加成功');
            }
        }
    }

    public function del() {
        $this->result = D('AdminAuthGroup')->delGroupInfo();
        if (empty($this->result)) {
            $this->error('删除失败');
        } else {
            $this->success('删除成功');
        }
    }

    protected function getMenu() {
        $map = array(
            'is_delete' => 0,
            'status' => 1,
        );
        $list = M('AdminAuthRule')->where($map)->field('id,pid,title')->order('level asc,sort desc')->select();
        
        $treeModel =  new \Common\Model\TreeModel();
        $list = $treeModel->toTree($list, 'id','pid','children');
        return $list;
    }

}
