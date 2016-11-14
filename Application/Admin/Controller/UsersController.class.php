<?php

namespace Admin\Controller;

class UsersController extends CommonController {

    public function index() {
        $filter = array(
            'fields_name' => array(
                'user_name' => array(
                    'desc'=>'用户名',
                    'type' => "input",
                    'match_type' => 'like',
                ),
                'user_type' => array(
                    'desc'=>'用户类型',
                    'type' => "select",
                     'values' => array('-1'=>"选择用户类型",'0'=>"业主",'1'=>"工人"),
                     'match_type' => 'eq',
                     'exclude' => array(-1),
                ), 
                'is_verified' => array(
                    'desc'=>'实名认证',
                    'type' => "select",
                     'values' => array('-1'=>"选择实名",'0'=> '无实名信息','1'=>"已实名",2=> '已上传待审'),
                     'match_type' => 'eq',
                     'exclude' => array(-1),
                ),                   
            ),
            'fields_alias' => array(
                'user_name' => '',
                'user_type' => '',
                'is_verified' => '',
            ),
            'fields_value' => array(
                'user_name' => '',
                'user_type' => '-1',
                'is_verified' => '-1',
            ),
            'order' => array(
                "fields" => array(
                    'user_id' => '用户id',
                    'user_name' => '用户名',                    
                ),
                'orderby' => 'user_id',
                'sortby' => 'desc',
            ),
        );
        
        $where = array(
            'u.is_delete' => 0,
        );
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
        
        $page = new \Com\Logdd\Page(D('Users')->alias('u')->where($where)->count(), C('PAGE_SIZE'));
        $this->pageInfo = $page->getPageInfo();       // var_dump( $this->pageInfo);die();
        $this->usersList = D('Users')->alias('u')->field('u.user_id,u.user_name,u.mobile,u.region_id,u.real_name,u.gold,u.money,u.add_time,u.login_time,r.region_name')
                ->join('left join __REGION__ as r on u.region_id = r.region_id')
                ->where($where)->order($order)->getList($page->firstRow, $page->listRows);
        $this->filter = $filter;
        
        $this->display();
    }
    
    //编辑用户
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
                $upload->savePath = $user_id.'/avatar/'; // 设置附件上传根目录         
                $info = $upload->uploadOne($_FILES['Filedata']);
                $path = $upload->rootPath . $info['savepath'] . $info['savename'];
                if (empty($info)) {
                    $this->error($upload->getError(), '', true);
                } else {
                    $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 1);                      
                    if($result !== false){
                        $this->success($result); 
                        exit();
                    }else{
                        $this->error('文件上传出现错误'); 
                        exit();
                    }
                }
            }else{
                $result = D('Users')->editUser();
                if (($result === false)) {
                    $this->error(D('Users')->getModelError());
                } else {
                    $this->success('编辑成功', U('Users/index'));
                }                
            }
        } else {
            $user_id = I('get.id',0,'intval');
            $userInfo = M('Users')->field('user_id,user_name,user_type,work_rank,mobile,region_id,real_name,avatar,gender,birthday,qq,money,enter_year,day_price,profile,service_idea,status,is_safe,is_verified,id_card_image_01,id_card_image_02,id_card_number')
                    ->find($user_id);
            if(!empty($userInfo['id_card_image_01'])){
                $userInfo['id_card_image_01'] = \Com\Logdd\AliyunOssManager::getSignedUrl($userInfo['id_card_image_01']);
            }
            if(!empty($userInfo['id_card_image_02'])){
                $userInfo['id_card_image_02'] = \Com\Logdd\AliyunOssManager::getSignedUrl($userInfo['id_card_image_02']);
            }            
            $regionInfo = M('Region')->field('region_id,region_name,parent_id')->where(array('region_id' => $userInfo['region_id']))->find();
            $this->cityInfo = M('Region')->field('region_id,region_name,parent_id')->where(array('region_id' => $regionInfo['parent_id']))->find();
            $this->provinceInfo = M('Region')->field('region_id,region_name,parent_id')->where(array('region_id' => $this->cityInfo['parent_id']))->find();   
            $userInfo['workTypeList'] = M('UserToWorkType')->where(array('user_id' => $userInfo['user_id']))->getField('type_id_2', TRUE);
            $this->userInfo = $userInfo;
            $this->display(); 
        }
    }
    
    //增加用户
    public function add() {
        if (IS_POST) {
            $result = D('Users')->addUser();
            if (($result === false)) {
                $this->error(D('Users')->getModelError());
            } else {
                $this->success('增加成功', U('Users/index'));
            }                
        } else {
            $this->display(); 
        }
    }      
    
    //删除用户
    public function del() {
        $user_id = I('post.user_id', 0, 'intval');
        admin_log("用户删除(" . $user_id . ")");
        $result = M('Users')->where(array('user_id' => $user_id))->save(array('is_delete' => 1));
        if($result === false){
            $this->error('用户删除失败！',U('Users/index'));
            exit();
        }else{
            $this->success('用户删除成功！',U('Users/index'));
            exit();
        }
    }                 //工人列表    public function workers() {            $filter = array(                'fields_name' => array(                    'user_name' => array(                        'desc'=>'用户名',                        'type' => "input",                        'match_type' => 'like',                    ),                    /*'user_type' => array(                        'desc'=>'用户类型',                        'type' => "select",                        'values' => array('-1'=>"选择用户类型",'0'=>"业主",'1'=>"工人"),                        'match_type' => 'eq',                        'exclude' => array(-1),                    ),*/                    'is_verified' => array(                        'desc'=>'实名认证',                        'type' => "select",                        'values' => array('-1'=>"选择实名",'0'=> '无实名信息','1'=>"已实名",2=> '已上传待审'),                        'match_type' => 'eq',                        'exclude' => array(-1),                    ),                ),                'fields_alias' => array(                    'user_name' => '',                    'user_type' => '',                    'is_verified' => '',                ),                'fields_value' => array(                    'user_name' => '',                    'user_type' => '-1',                    'is_verified' => '-1',                ),                'order' => array(                    "fields" => array(                        'user_id' => '用户id',                        'user_name' => '用户名',                    ),                    'orderby' => 'user_id',                    'sortby' => 'desc',                ),            );                    $where = array(                'u.is_delete' => 0,                'u.user_type' =>1,            );            foreach($filter['fields_name'] as $name => $info){                $value =  I('get.filter_'.$name,'',  'htmlspecialchars');                if($value !== ''){                    if(isset($info['exclude']) && in_array($value,$info['exclude'])){                        continue;                    }                    $filter['fields_value'][$name] = $value;                    if(isset($filter['fields_alias'][$name]) && !empty($filter['fields_alias'][$name])){                        if($info['match_type'] == 'like'){                            $where[$filter['fields_alias'][$name].".".$name] = array('like','%'.$value.'%');                        }else{                            $where[$filter['fields_alias'][$name].".".$name] = $value;                        }                    }else{                        if($info['match_type'] == 'like'){                            $where[$name] = array('like','%'.$value.'%');                        }else{                            $where[$name] = $value;                        }                    }                }            }            $order = array();            $orerby = I('get.orderby','','htmlspecialchars');            $sortby = I('get.sortby','','htmlspecialchars');            if(array_key_exists($orerby, $filter['order']['fields'])){                $filter['order']['orderby'] = $orerby;            }            if(in_array(strtolower($sortby), array('desc','asc'))){                $filter['order']['sortby'] = strtolower($sortby);            }            $order[$filter['order']['orderby']] = $filter['order']['sortby'];                    $page = new \Com\Logdd\Page(D('Users')->alias('u')->where($where)->count(), C('PAGE_SIZE'));            $this->pageInfo = $page->getPageInfo();            $this->usersList = D('Users')->alias('u')->field('u.user_id,u.user_name,u.mobile,u.region_id,u.real_name,u.gold,u.money,u.add_time,u.login_time,r.region_name')            ->join('left join __REGION__ as r on u.region_id = r.region_id')            ->where($where)->order($order)->getList($page->firstRow, $page->listRows);            $this->filter = $filter;                    $this->display();        }            //业主列表    public function owner() {            $filter = array(                'fields_name' => array(                    'user_name' => array(                        'desc'=>'用户名',                        'type' => "input",                        'match_type' => 'like',                    ),                    /*'user_type' => array(                        'desc'=>'用户类型',                        'type' => "select",                        'values' => array('-1'=>"选择用户类型",'0'=>"业主",'1'=>"工人"),                        'match_type' => 'eq',                        'exclude' => array(-1),                    ),*/                    'is_verified' => array(                        'desc'=>'实名认证',                        'type' => "select",                        'values' => array('-1'=>"选择实名",'0'=> '无实名信息','1'=>"已实名",2=> '已上传待审'),                        'match_type' => 'eq',                        'exclude' => array(-1),                    ),                ),                'fields_alias' => array(                    'user_name' => '',                    'user_type' => '',                    'is_verified' => '',                ),                'fields_value' => array(                    'user_name' => '',                    'user_type' => '-1',                    'is_verified' => '-1',                ),                'order' => array(                    "fields" => array(                        'user_id' => '用户id',                        'user_name' => '用户名',                    ),                    'orderby' => 'user_id',                    'sortby' => 'desc',                ),            );                    $where = array(                'u.is_delete' => 0,                'u.user_type' =>0,            );            foreach($filter['fields_name'] as $name => $info){                $value =  I('get.filter_'.$name,'',  'htmlspecialchars');                if($value !== ''){                    if(isset($info['exclude']) && in_array($value,$info['exclude'])){                        continue;                    }                    $filter['fields_value'][$name] = $value;                    if(isset($filter['fields_alias'][$name]) && !empty($filter['fields_alias'][$name])){                        if($info['match_type'] == 'like'){                            $where[$filter['fields_alias'][$name].".".$name] = array('like','%'.$value.'%');                        }else{                            $where[$filter['fields_alias'][$name].".".$name] = $value;                        }                    }else{                        if($info['match_type'] == 'like'){                            $where[$name] = array('like','%'.$value.'%');                        }else{                            $where[$name] = $value;                        }                    }                }            }            $order = array();            $orerby = I('get.orderby','','htmlspecialchars');            $sortby = I('get.sortby','','htmlspecialchars');            if(array_key_exists($orerby, $filter['order']['fields'])){                $filter['order']['orderby'] = $orerby;            }            if(in_array(strtolower($sortby), array('desc','asc'))){                $filter['order']['sortby'] = strtolower($sortby);            }            $order[$filter['order']['orderby']] = $filter['order']['sortby'];                    $page = new \Com\Logdd\Page(D('Users')->alias('u')->where($where)->count(), C('PAGE_SIZE'));            $this->pageInfo = $page->getPageInfo();            $this->usersList = D('Users')->alias('u')->field('u.user_id,u.user_name,u.mobile,u.region_id,u.real_name,u.gold,u.money,u.add_time,u.login_time,r.region_name')            ->join('left join __REGION__ as r on u.region_id = r.region_id')            ->where($where)->order($order)->getList($page->firstRow, $page->listRows);            $this->filter = $filter;                    $this->display();        }         //会员余额    public function balance() {            $filter = array(                'fields_name' => array(                    'user_name' => array(                        'desc'=>'用户名',                        'type' => "input",                        'match_type' => 'like',                    ),                    'user_type' => array(                        'desc'=>'用户类型',                        'type' => "select",                        'values' => array('-1'=>"选择用户类型",'0'=>"业主",'1'=>"工人"),                        'match_type' => 'eq',                        'exclude' => array(-1),                    ),                    'is_verified' => array(                        'desc'=>'实名认证',                        'type' => "select",                        'values' => array('-1'=>"选择实名",'0'=> '无实名信息','1'=>"已实名",2=> '已上传待审'),                        'match_type' => 'eq',                        'exclude' => array(-1),                    ),                ),                'fields_alias' => array(                    'user_name' => '',                    'user_type' => '',                    'is_verified' => '',                ),                'fields_value' => array(                    'user_name' => '',                    'user_type' => '-1',                    'is_verified' => '-1',                ),                'order' => array(                    "fields" => array(                        'user_id' => '用户id',                        'user_name' => '用户名',                    ),                    'orderby' => 'user_id',                    'sortby' => 'desc',                ),            );                    $where = array(                'u.is_delete' => 0,            );            foreach($filter['fields_name'] as $name => $info){                $value =  I('get.filter_'.$name,'',  'htmlspecialchars');                if($value !== ''){                    if(isset($info['exclude']) && in_array($value,$info['exclude'])){                        continue;                    }                    $filter['fields_value'][$name] = $value;                    if(isset($filter['fields_alias'][$name]) && !empty($filter['fields_alias'][$name])){                        if($info['match_type'] == 'like'){                            $where[$filter['fields_alias'][$name].".".$name] = array('like','%'.$value.'%');                        }else{                            $where[$filter['fields_alias'][$name].".".$name] = $value;                        }                    }else{                        if($info['match_type'] == 'like'){                            $where[$name] = array('like','%'.$value.'%');                        }else{                            $where[$name] = $value;                        }                    }                }            }            $order = array();            $orerby = I('get.orderby','','htmlspecialchars');            $sortby = I('get.sortby','','htmlspecialchars');            if(array_key_exists($orerby, $filter['order']['fields'])){                $filter['order']['orderby'] = $orerby;            }            if(in_array(strtolower($sortby), array('desc','asc'))){                $filter['order']['sortby'] = strtolower($sortby);            }            $order[$filter['order']['orderby']] = $filter['order']['sortby'];                    $page = new \Com\Logdd\Page(D('Users')->alias('u')->where($where)->count(), C('PAGE_SIZE'));            $this->pageInfo = $page->getPageInfo();                $this->usersList = D('Users')->alias('u')->field('u.user_id,u.user_name,u.mobile,u.region_id,u.real_name,u.gold,u.money,u.add_time,u.login_time,r.region_name')            ->join('left join __REGION__ as r on u.region_id = r.region_id')            ->where($where)->order($order)->getList($page->firstRow, $page->listRows);            $this->filter = $filter;                    $this->display();        }    
       
}
