<?php
namespace Fenzhan\Controller;

class FenzhanUserController extends CommonController
{

    public function index()
    {
        $filter = array(
            
            'fields_name' => array(
                
                'user_name' => array(
                    
                    'desc' => '用户名',
                    
                    'type' => "input"
                )
            ),
            
            'fields_alias' => array(
                
                'user_name' => ''
            ),
            
            'fields_value' => array(
                
                'user_name' => ''
            ),
            
            'order' => array(
                
                "fields" => array(
                    
                    'uid' => '用户id',
                    
                    'user_name' => '用户名'
                ),
                
                'orderby' => 'user_name',
                
                'sortby' => 'desc'
            )
        );
        
        $where = array(
            'a.is_delete' => 0,
            'a.is_admin' => 0,
            'a.region_id' => ADMIN_CITY_ID
        );
        
        foreach ($filter['fields_name'] as $name => $info) {
            
            $value = I('get.filter_' . $name, '', 'htmlspecialchars');
            
            if ($value) {
                
                $filter['fields_value'][$name] = $value;
                
                if (isset($filter['fields_alias'][$name]) && ! empty($filter['fields_alias'][$name])) {
                    
                    $where[$filter['fields_alias'][$name] . "." . $name] = array(
                        'like',
                        '%' . $value . '%'
                    );
                } else {
                    
                    $where[$name] = array(
                        'like',
                        '%' . $value . '%'
                    );
                }
            }
        }
        
        $order = array();
        
        $orerby = I('get.orderby', '', 'htmlspecialchars');
        
        $sortby = I('get.sortby', '', 'htmlspecialchars');
        
        if (array_key_exists($orerby, $filter['order']['fields'])) {
            
            $filter['order']['orderby'] = $orerby;
        }
        
        if (in_array(strtolower($sortby), array(
            'desc',
            'asc'
        ))) {
            
            $filter['order']['sortby'] = strtolower($sortby);
        }
        
        $order[$filter['order']['orderby']] = $filter['order']['sortby'];
        
        $page = new \Com\Logdd\Page(D('AdminUser')->alias('a')
            ->where($where)
            ->count(), C('PAGE_SIZE'));
        
        $this->pageInfo = $page->getPageInfo();
        
        $this->FenzhanUserList = D('AdminUser')->alias('a')
            ->field('a.uid,a.user_name,a.region_id,a.contact,a.phone,a.email,a.status,a.reg_time,a.error_times,a.last_login_time,a.is_delete,b.region_name')
            ->join('left join __REGION__ as b on a.region_id = b.region_id')
            ->where($where)
            ->order($order)
            ->getList($page->firstRow, $page->listRows);
        
        $this->authGroupList = M('AdminAuthGroup')->where(array(
            'is_delete' => 0,
            'is_admin' => 0
        ))->select();
        
        $this->filter = $filter;
        
        $this->display();
    }

    public function edit()
    {
        $uid = I('get.uid', 0, 'intval');
        
        if (in_array($uid, C('ADMINISTRATOR'))) {
            
            $this->error('不能修改超级管理员信息');
        }
        
        if (! IS_POST) {
            
            $FenzhanUserInfo = D('AdminUser')->where(array(
                'is_delete' => 0
            ))->getUserInfo($uid);
            
            if (empty($FenzhanUserInfo)) {
                
                $this->error('记录不存在');
            } else {
                
                $groups = M('AdminAuthGroupAccess')->where(array(
                    
                    'uid' => $uid
                ))->getField('group_id', true);
                
                $FenzhanUserInfo['groups'] = empty($groups) ? '' : implode($groups, ',');
                
                $this->success($FenzhanUserInfo);
            }
        } else {
            
            $result = D('AdminUser')->editFenzhanUser();
            
            if ($result === false) {
                
                $this->error(D("AdminUser")->getModelError());
                
                $this->error('编辑失败');
            } else {
                
                $this->success('编辑成功');
            }
        }
    }

    public function add()
    {
        if (IS_POST) {
            
            $result = D('AdminUser')->addFenzhanUser();
            
            if ($result === false) {
                
                $this->error(D("AdminUser")->getModelError());
            } else {
                
                $this->success('记录增加成功');
            }
        }
    }

    public function del()
    {
        $uid = I('request.uid', 0, 'intval');
        
        if (in_array($uid, C('ADMINISTRATOR'))) {
            
            $this->error('不能修改超级管理员信息');
        }
        
        $result = D('AdminUser')->delFenzhanUser();
        
        if (empty($result)) {
            
            $this->error('删除失败');
        } else {
            
            $this->success('删除成功');
        }
    }
}