<?php

namespace Admin\Model;

class AdminUserModel extends CommonModel {



    protected $fields = array('uid','is_admin', 'user_name','region_id','contact','phone','email', 'password', 'status', 'reg_time','error_times','last_login_time','is_delete',

        '_type'=>array(

            'uid'=>'mediumint(8) unsigned',
            
            'is_admin'=>'tinyint(1) unsigned [0]',

            'user_name'=>'char(50) []',

            'password'=>'char(60) []',
            
            'region_id'=>'smallint(5) unsigned [0]',
            
            'contact'=>'char(50) []',
            
            'phone'=>'char(20) []',
            
            'email'=>'char(50) []',

            'status'=>'tinyint(1) unsigned [1]',

            'reg_time'=>'int(11) unsigned [0]',

            'error_times'=>'smallint(3) unsigned [0]',

            'last_login_time'=>'int(11) unsigned [0]',

            'is_delete'=>'tinyint(1) unsigned [0]',

        )

    );

    protected $pk = 'uid';

    protected $_validate = array(
        array('user_name', 'require', '管理员名称不能为空！'),
        array('password', 'require', '请设置密码！'),
        //array('region_id',0,'请选择所属城市！',0,'notequal'),
        //array('region_id', 'require', '请选择所属城市！'),
        array('contact', 'require', '联系人姓名不能为空！'),
        array('phone', 'require', '联系电话不能为空！'),
        array('email', 'email', '请填写有效邮箱！'),

    );

    public $modelErrorInfo = array(

        'field_duplicate' => '管理员已存在',

    );

    public function getList($page, $size = 20) {

        return $this->limit($page . ',' . $size)->select();

    }

    
    //总站管理员添加
    public function addAdminUser() {
    
        $data = array(
    
            'is_admin' => intval(1),
    
            'user_name' => I('post.user_name', '', 'htmlspecialchars'),
    
            'contact' => I('post.contact', '', 'htmlspecialchars'),
    
            'phone' => I('post.phone', '', 'htmlspecialchars'),
    
            'email' => I('post.email', '', 'htmlspecialchars'),
    
            'region_id' => intval(0),
    
            'status' => intval(I('post.status')) == 0 ? 0 : 1,
    
            'reg_time' => time(),
    
        );
    
        if($this->check_delete($data['user_name'])){
    
            $this->error = '管理员名称不符合规范';
    
            return false;
    
        }
    
        import('Common/PasswordHash', COMMON_PATH, '.php');
    
        $pHash = new \PasswordHash(8, false);
    
        $data['password'] = $pHash->HashPassword(I('post.password'));
    
        if (!$this->create($data, self::MODEL_INSERT)) {
    
            admin_log("管理员增加失败");
    
            return false;
    
        } else {
    
            $uid = $this->add();
    
        }
    
        if ($uid === false) {
    
            return $uid;
    
        } else {
    
            $ids = (!isset($_POST['groups'])) ? array() : array_unique(array_filter($_POST['groups'], "intval"));
    
            foreach ($ids as $k => $v) {
    
                if ($v > 0) {
    
                    M('AdminAuthGroupAccess')->data(array(
    
                    'uid' => $uid,
    
                    'group_id' => $v,
    
                    ))->add();
    
                }
    
            }
    
            $data['uid'] = $uid;
    
            $data['groups'] = $ids;
    
            admin_log("管理员增加成功",  json_encode($data));
    
            return $uid;
    
        }
    
    }
    
    
    //分站管理员添加
    public function addFenzhanUser() {
    
        $data = array(
    
            'is_admin' => intval(0),
    
            'user_name' => I('post.user_name', '', 'htmlspecialchars'),
    
            'contact' => I('post.contact', '', 'htmlspecialchars'),
    
            'phone' => I('post.phone', '', 'htmlspecialchars'),
    
            'email' => I('post.email', '', 'htmlspecialchars'),
    
            'region_id' => intval(I('post.region_id')),
    
            'status' => intval(I('post.status')) == 0 ? 0 : 1,
    
            'reg_time' => time(),
    
        );
    
        if($this->check_delete($data['user_name'])){
    
            $this->error = '管理员名称不符合规范';
    
            return false;
    
        }
        
        if($data['region_id']==0||empty($data['region_id'])){
        
            $this->error = '请选择所属城市！';
        
            return false;
        
        }
    
        import('Common/PasswordHash', COMMON_PATH, '.php');
    
        $pHash = new \PasswordHash(8, false);
    
        $data['password'] = $pHash->HashPassword(I('post.password'));
    
        if (!$this->create($data, self::MODEL_INSERT)) {
    
            admin_log("管理员增加失败");
    
            return false;
    
        } else {
    
            $uid = $this->add();
    
        }
    
        if ($uid === false) {
    
            return $uid;
    
        } else {
    
            $ids = (!isset($_POST['groups'])) ? array() : array_unique(array_filter($_POST['groups'], "intval"));
    
            foreach ($ids as $k => $v) {
    
                if ($v > 0) {
    
                    M('AdminAuthGroupAccess')->data(array(
    
                    'uid' => $uid,
    
                    'group_id' => $v,
    
                    ))->add();
    
                }
    
            }
    
            $data['uid'] = $uid;
    
            $data['groups'] = $ids;
    
            admin_log("管理员增加成功",  json_encode($data));
    
            return $uid;
    
        }
    
    }
    

//总站管理员编辑
    public function editAdminUser() {

        $uid = intval(I('post.uid'));

        $data = array(
            'is_admin' => intval(1),
            'user_name' => I('post.user_name', '', 'htmlspecialchars'),
            'region_id' => intval(0),                    
            'contact' => I('post.contact', '', 'htmlspecialchars'),
            'phone' => I('post.phone', '', 'htmlspecialchars'),
            'email' => I('post.email', '', 'htmlspecialchars'),
            'status' => I('post.status', 0, 'intval') == 0 ? 0 : 1,

        );

        if($this->check_delete($data['user_name'])){

            $this->error = '管理员名称不符合规范';

            return false;

        }

        $password = I('post.password', '', 'trim');

        if (!empty($password)) {

            import('Common/PasswordHash', COMMON_PATH, '.php');

            $pHash = new \PasswordHash(8, false);

            $data['password'] = $pHash->HashPassword($password);

        }

        if (!$this->create($data, self::MODEL_UPDATE)) {

            admin_log("管理员更新失败");

            return false;

        } else {

            $result = $this->where(array('uid' => $uid))->save();

        }

        if ($result === false) {

            return $result;

        } else {

            M('AdminAuthGroupAccess')->where(array('uid' => $uid))->delete();

            $ids = (!isset($_POST['groups'])) ? array() : array_unique(array_filter($_POST['groups'], "intval"));

            foreach ($ids as $v) {

                if ($v > 0) {

                    M('AdminAuthGroupAccess')->data(array(

                    'uid' => $uid,

                    'group_id' => $v,

                    ))->add();

                }

            }

            $data['uid'] = $uid;

            $data['groups'] = $ids;

            admin_log("管理员更新成功",  json_encode($data));

            return $result;

        }

    }
    
    //分站管理员编辑
    public function editFenzhanUser() {
    
        $uid = intval(I('post.uid'));
    
        $data = array(
            'is_admin' => intval(0),
            'user_name' => I('post.user_name', '', 'htmlspecialchars'),
            'region_id' => intval(I('post.region_id')),
            'contact' => I('post.contact', '', 'htmlspecialchars'),
            'phone' => I('post.phone', '', 'htmlspecialchars'),
            'email' => I('post.email', '', 'htmlspecialchars'),
            'status' => I('post.status', 0, 'intval') == 0 ? 0 : 1,
    
        );
    
        if($this->check_delete($data['user_name'])){
    
            $this->error = '管理员名称不符合规范';
    
            return false;
    
        }
    
        if($data['region_id']==0){
        
            $this->error = '请选择所属城市！';
        
            return false;
        
        }
        
        $password = I('post.password', '', 'trim');
    
        if (!empty($password)) {
    
            import('Common/PasswordHash', COMMON_PATH, '.php');
    
            $pHash = new \PasswordHash(8, false);
    
            $data['password'] = $pHash->HashPassword($password);
    
        }
    
        if (!$this->create($data, self::MODEL_UPDATE)) {
    
            admin_log("管理员更新失败");
    
            return false;
    
        } else {
    
            $result = $this->where(array('uid' => $uid))->save();
    
        }
    
        if ($result === false) {
    
            return $result;
    
        } else {
    
            M('AdminAuthGroupAccess')->where(array('uid' => $uid))->delete();
    
            $ids = (!isset($_POST['groups'])) ? array() : array_unique(array_filter($_POST['groups'], "intval"));
    
            foreach ($ids as $v) {
    
                if ($v > 0) {
    
                    M('AdminAuthGroupAccess')->data(array(
    
                    'uid' => $uid,
    
                    'group_id' => $v,
    
                    ))->add();
    
                }
    
            }
    
            $data['uid'] = $uid;
    
            $data['groups'] = $ids;
    
            admin_log("管理员更新成功",  json_encode($data));
    
            return $result;
    
        }
    
    }



    public function delAdminUser() {

        admin_log("管理员删除,id为".intval(I('post.uid')));

        return $this->where(array('uid' => intval(I('post.uid')),'is_delete' => 0))->save(array('is_delete' => 1,'name' => array('exp',"CONCAT('del',name)")));

    }
    
    public function delFenzhanUser() {
    
        admin_log("分站管理员删除,id为".intval(I('post.uid')));
    
        return $this->where(array('uid' => intval(I('post.uid')),'is_delete' => 0))->save(array('is_delete' => 1,'name' => array('exp',"CONCAT('del',name)")));
    
    }



    public function login() {

        $return = array(

            'error' => 0,

            'message' => '',

        );

        import('Common/PasswordHash', COMMON_PATH, '.php');

        $rs = $this->field('uid,user_name,password,status,error_times,last_login_time')->where(array(

            'user_name' => I('post.username',''),

            'is_delete' => 0,

        ))->find();

        if (!$rs) {

            $return['error'] = 1;

            $return['message'] = '用户名不存在';

            return $return;

        }

        if ($rs['error_times'] >= 10 && $rs['last_login_time'] > gmtime() - 600) {

            $return['error'] = 1;

            $return['message'] = '登录错误次数超过限制，10分钟后重试！';

            return $return;

        }

        else{

            M("AdminUser")->where(array('uid'=> $rs['uid']))->save(array('error_times'=> 0));

        }



        $pHash = new \PasswordHash(8, false);

        if (!$pHash->CheckPassword(I('post.password'), $rs['password'])) {

            $return['error'] = 2;

            $return['message'] = '用户名或密码错误';

            $data = array(

                'last_login_time' =>gmtime(),

                'error_times' => array('exp','error_times+1'),

            );

            M("AdminUser")->where(array('uid'=> $rs['uid']))->save($data);

            return $return;

        } else {

            $return['uid'] = $rs['uid'];

            $data = array(

                'last_login_time' => \gmtime(),

                'error_times' => 0,

            );

            M("AdminUser")->where(array('uid'=> $rs['uid']))->save($data);

        }

        if(isset($return['uid'])){

            admin_log("用户登录成功,管理员id为".$return['uid'],'',$return['uid']);

        }else{

            admin_log("用户".I('post.username','')."登录错误",'',0);

        }

        return $return;

    }



    public function getUserInfo($uid) {

        return $this->field('uid,user_name,is_admin,region_id,contact,phone,email,status,last_login_time')->find($uid);

    }



}

