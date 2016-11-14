<?php

namespace Admin\Controller;

class AdminUserController extends CommonController {

    public function index() {
        $filter = array(
            'fields_name' => array(
                'user_name' => array(
                    'desc'=>'用户名',
                    'type' => "input",
                ),
            ),
            'fields_alias' => array(
                'user_name' => '',
            ),
            'fields_value' => array(
                'user_name' => '',
            ),
            'order' => array(
                "fields" => array(
                    'uid' => '用户id',
                    'user_name' => '用户名',                    
                ),
                'orderby' => 'user_name',
                'sortby' => 'desc',
            ),
        );
        
        $where = array('is_delete' => 0,'is_admin' => 1);
        foreach($filter['fields_name'] as $name => $info){
            $value =  I('get.filter_'.$name,'',  'htmlspecialchars');
            if($value){
                $filter['fields_value'][$name] = $value;
                if(isset($filter['fields_alias'][$name]) && !empty($filter['fields_alias'][$name])){
                    $where[$filter['fields_alias'][$name].".".$name] = array('like','%'.$value.'%');
                }else{
                    $where[$name] = array('like','%'.$value.'%');
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
        
        $page = new \Com\Logdd\Page(D('AdminUser')->where($where)->count(), C('PAGE_SIZE'));
        $this->pageInfo = $page->getPageInfo();
        $this->adminUserList = D('AdminUser')->field('*')->where($where)->order($order)->getList($page->firstRow, $page->listRows);
        $this->authGroupList = M('AdminAuthGroup')->where(array('is_delete' => 0,'is_admin' =>1))->select();
        $this->filter = $filter;
        
        $this->display();
    }

    public function edit() {
        $uid = I('get.uid',0,'intval');
        if (in_array($uid, C('ADMINISTRATOR'))) {
            $this->error('不能修改超级管理员信息');
        }        
        if (!IS_POST) {
            $adminUserInfo = D('AdminUser')->where(array('is_delete' => 0))->getUserInfo($uid);//修改过
            if (empty($adminUserInfo)) {
                $this->error('记录不存在');
            } else {
                $groups = M('AdminAuthGroupAccess')->where(array(
                            'uid' => $uid,
                        ))->getField('group_id', true);
                $adminUserInfo['groups'] = empty($groups) ? '' : implode($groups, ',');
                $this->success($adminUserInfo);
            }
        } else {            
            $result = D('AdminUser')->editAdminUser();
            if ($result === false) {
                $this->error(D("AdminUser")->getModelError());
                $this->error('编辑失败');
            } else {
                $this->success('编辑成功');
            }
        }
    }

    public function add() {
        if (IS_POST) {                        
            $result = D('AdminUser')->addAdminUser();
            if ($result === false) {
                $this->error(D("AdminUser")->getModelError());
            } else {
                $this->success('记录增加成功');
            }
        }
    }

    public function del() {
        $uid = I('request.uid',0,'intval');
        if (in_array($uid, C('ADMINISTRATOR'))) {
            $this->error('不能修改超级管理员信息');
        }        
        $result = D('AdminUser')->delAdminUser();
        if (empty($result)) {
            $this->error('删除失败');
        } else {
            $this->success('删除成功');
        }
    }
    /*
    protected function getMenu() {
        $menus = M('AdminMenu')->index('id')->order('sort desc')->field('id,name')->select();
        $rules = M('AdminAuthRule')->where(array('status' => 1))->field('id,pid,name,title')->order('sort desc')->select();
        foreach ($rules as $rule) {
            $menus[$rule['pid']]['children'][$rule['id']] = $rule;
        }
        foreach ($menus as $m_key => $menu) {
            if (!isset($menu['children']) || empty($menu['children'])) {
                unset($menus[$m_key]);
            }
        }
        return $menus;
    }
     */

}
