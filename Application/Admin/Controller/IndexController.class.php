<?php
namespace Admin\Controller;
use Think\Auth;
class IndexController extends CommonController {
    public function index() {
        $manageMenu = $this->getManageMenu();
        $firstMenu = array();
        foreach($manageMenu as $key => $menu){
            $firstMenu[] = array(
                'id' => $menu['id'],
                'title' => $menu['text'],
            );
        }
        $this->firstMenu = $firstMenu;
        $this->secondMenu = $manageMenu;
        $this->display();
    }
    
    public function info() {
        $userTotal = M('Users')->field('sum(if(user_type=0,1,0)) as owner_num,'
                . 'sum(if(user_type=1,1,0)) as worker_num,'
                . 'sum(if(user_type=1 and is_verified = 0,1,0)) as no_verified_num,'
                . 'sum(if(user_type=1 and is_verified = 1,1,0)) as is_verified_num,'
                . 'sum(if(user_type=1 and is_verified = 2,1,0)) as wait_verified_num')->find();
        $userTotal['total_num'] = $userTotal['owner_num'] + $userTotal['worker_num'];
        $this->userTotal = $userTotal;
        $this->display();
    }    
    
    protected function getManageMenu() {
        $map = array(
            'is_delete' => 0,
            'is_show' => 1,
            'status' => 1,
        );
        if (IS_ADMIN == 0) {
            //读取用户所属用户组
            $auth = new \Com\Logdd\Auth();
            $groups = $auth->getGroups(ADMIN_USER_ID);
            $ids = array(); //保存用户所属用户组设置的所有权限规则id
            foreach ($groups as $g) {
                $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
            }
            $ids = array_unique($ids);
            if (empty($ids)) {
                return array();
            }
            $map['id'] = array('in', $ids);
        }
        //读取用户组所有权限规则
        $list = M('AdminAuthRule')->where($map)->field('CONCAT("menu_",id) as id,CONCAT("menu_",pid) as pid,name as href,title as text')->order('pid asc,sort desc')->select();
        $refer = array();
        $tree = array();
        foreach ($list as $key => $data) {
            $refer[$data['id']] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data['pid'];
            if($list[$key]['href'] == 'Index/info'){
                $list[$key]['closeable'] = false;
            }
            if(!empty($data['href'])){
                $list[$key]['href'] = U($data['href']);
            }
            if($data['id'] == 'menu_1'){
                 $list[$key]['homePage'] = 'menu_100';
            }
            $is_exist_pid = false;
            if($data['pid'] != 'menu_0'){
                foreach ($refer as $k => $v) {
                    if ($parentId == $k) {
                        $is_exist_pid = true;
                        break;
                    }
                }                
            }
            if ($is_exist_pid) {
                if (isset($refer[$parentId])) {
                    $parent = & $refer[$parentId];
                    if($parent['pid'] == 'menu_0'){
                        $parent['menu'][] = & $list[$key];
                    }else{
                        $parent['items'][] = & $list[$key];
                    }
                }
            } else {
                $tree[] = & $list[$key];
            }
        }
        return $tree;
    }
}
