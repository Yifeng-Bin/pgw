<?php
namespace Fenzhan\Model;
class UsersModel extends CommonModel {

    protected $_validate = array(
        array('user_name', 'require', '用户名称不能为空！'),
    );
    public $modelErrorInfo = array(
        'field_duplicate' => '用户已存在',
    );
    public function getList($page, $size = 20) {
        return $this->limit($page . ',' . $size)->select();
    }
    
    public function addUser(){
        $data = array(
            'user_name' => I('post.user_name', '', 'htmlspecialchars'),
            'user_type' => I('post.user_type', 0, 'intval'),
            'work_rank' => I('post.work_rank', 0, 'intval'),
            'password' => I('post.password', '', 'htmlspecialchars'),
            'mobile' => I('post.mobile', '', 'htmlspecialchars'),
            'region_id' => I('post.region_id', 0, 'intval'),
            'real_name' => I('post.real_name', '', 'htmlspecialchars'),
            'avatar' => '',
            'gender' => I('post.gender', 0, 'intval'),
            'birthday' => I('post.birthday', '', 'htmlspecialchars'),
            'qq' => I('post.qq', '', 'htmlspecialchars'),
            'money' => I('post.money', 0, 'intval'),
            'enter_year' => I('post.enter_year', 0, 'intval'),
            'day_price' => I('post.day_price', 0, 'intval'),
            'profile' => I('post.profile', '', 'htmlspecialchars'),
            'service_idea' => I('post.service_idea', '', 'htmlspecialchars'),
            'add_time' => gmtime(),
            'status' => I('post.status', 0, 'intval'),
            'is_safe' => I('post.is_safe', 0, 'intval'),
            'is_verified' => I('post.is_verified', 0, 'intval'),
        );
        if(empty($data['user_name'])){
            $this->error = '用户名不能为空';
            return false;            
        }
        
        if(!in_array($data['user_type'],array(0,1))){
            $this->error = '用户类型不存在';
            return false;            
        }        

        if(!in_array($data['work_rank'],array(1,2))){
            $this->error = '工人类型不存在';
            return false;            
        }
        
        if(empty($data['password'])){
            $this->error = '密码不能为空';
            return false;            
        }else{
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 10]);
        }
        
        $is_mobile = \Com\Logdd\Validator::isMobile($data['mobile']);
        if (!$is_mobile) {//手机号码错误
            $this->error = '手机号码格式不正确';
            return false;               
        }        

        if(!in_array($data['gender'],array(0,1,2))){
            $this->error = '性别不存在';
            return false;            
        }
        
        if($data['work_type_id'] == 0){
            //$this->error = '未选择工种';
            //return false;
        }
        if($data['region_id'] == 0){
            $this->error = '未选择地区';
            return false;
        }
        if (!in_array($data['enter_year'], array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11))) {
            $data['enter_year'] = 1;
        }        
        if (!$this->create($data, self::MODEL_INSERT)) {
            admin_log("需求用户失败");
            return false;
        } else {
            $this->startTrans();
            $user_id = $this->add();
            $mobile_data = array(
                'user_id' => $user_id,
                'user_type' => 'mobile',
                'user_name' => $data['mobile'],
                'is_inside' => 1,
                'is_verify' => 1,
                'add_time' => gmtime(),
            );
            $result = M('UserAuths')->add($mobile_data);
            if ($result === false) {
                $this->rollback();
                $this->error = '手机号码重复';
                return false;
            }

            $user_name_data = array(
                'user_id' => $user_id,
                'user_type' => 'username',
                'user_name' => $data['user_name'],
                'is_inside' => 1,
                'is_verify' => 1,
                'add_time' => gmtime(),
            );
            $result = M('UserAuths')->add($user_name_data);
            if ($result === false) {
                $this->rollback();
                $this->error = '用户名重复';
                return false;                
            }
            $this->commit();            
        }
        if ($result === false) {
            return false;
        }
        
        $type_ids = I('post.type_ids', array(), 'intval');
        if (!empty($type_ids)) {
            $data = M('WorkType')->where(array('status' => 1, 'level' => 2, 'type_id' => array('in', $type_ids)))->field($user_id . ' as user_id,parent_id as type_id_1,type_id as type_id_2')->limit('0,3')->select();
            if(!empty($data)){
                M('UserToWorkType')->where(array('user_id' => $user_id))->delete();
                M('UserToWorkType')->addAll($data);                   
            }
        }
    
        
        admin_log("用户添加成功", json_encode($data));
        return $result;        
    }
    
    
    public function editUser(){
        $user_id = I('post.user_id',0,'intval');
        $userInfo = $this->field('user_id,user_name,mobile')->find($user_id);
        if(empty($userInfo)){
            $this->error = '用户名不存在';
            return false;
        }
        $data = array(
            'user_name' => I('post.user_name', '', 'htmlspecialchars'),
            'user_type' => I('post.user_type', 0, 'intval'),
            'work_rank' => I('post.work_rank', 0, 'intval'),
            'password' => I('post.password', '', 'htmlspecialchars'),
            'mobile' => I('post.mobile', '', 'htmlspecialchars'),
            'region_id' => I('post.region_id', 0, 'intval'),
            'real_name' => I('post.real_name', '', 'htmlspecialchars'),
            'avatar' => I('post.avatar', '', 'htmlspecialchars'),
            'gender' => I('post.gender', 0, 'intval'),
            'birthday' => I('post.birthday', '', 'htmlspecialchars'),
            'qq' => I('post.qq', '', 'htmlspecialchars'),
            'money' => I('post.money', 0, 'intval'),
            'enter_year' => I('post.enter_year', 0, 'intval'),
            'day_price' => I('post.day_price', 0, 'intval'),
            'profile' => I('post.profile', '', 'htmlspecialchars'),
            'service_idea' => I('post.service_idea', '', 'htmlspecialchars'),
            'add_time' => gmtime(),
            'status' => I('post.status', 0, 'intval'),
            'is_safe' => I('post.is_safe', 0, 'intval'),
            'is_verified' => I('post.is_verified', 0, 'intval'),
        );
        if(empty($data['user_name'])){
            $this->error = '用户名不能为空';
            return false;            
        }
        if($data['user_name'] == $userInfo['user_name']){
            unset($data['user_name']);
        }
        if(!in_array($data['user_type'],array(0,1))){
            $this->error = '用户类型不存在';
            return false;            
        }        

        if(!in_array($data['work_rank'],array(1,2))){
            $this->error = '工人类型不存在';
            return false;            
        }
        
        if(empty($data['password'])){
            unset($data['password']);   
        }else{
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 10]);
        }
        
        $is_mobile = \Com\Logdd\Validator::isMobile($data['mobile']);
        if (!$is_mobile) {//手机号码错误
            $this->error = '手机号码格式不正确';
            return false;               
        }       
        
        if($data['mobile'] == $userInfo['mobile']){
            unset($data['mobile']);
        }
        
        if(!in_array($data['gender'],array(0,1,2))){
            $this->error = '性别不存在';
            return false;            
        }
        
        if($data['region_id'] == 0){
            $this->error = '未选择地区';
            return false;
        }
        
        if (!in_array($data['enter_year'], array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11))) {
            $data['enter_year'] = 1;
        }        
        
        if (!$this->create($data, self::MODEL_UPDATE)) {
            admin_log("需求用户失败");
            return false;
        } else {
            $this->startTrans();
            $this->where(array('user_id' => $user_id))->save();
            
            if(isset($data['mobile'])){
                $mobile_data = array(
                    'user_name' => $data['mobile'],
                    'is_inside' => 1,
                    'is_verify' => 1,
                );
                $result = M('UserAuths')->where(array('user_id' => $user_id,'user_type' => 'mobile'))->save($mobile_data);
                if ($result === false) {
                    $this->rollback();
                    $this->error = '手机号码重复';
                    return false;
                }                
            }

            if(isset($data['user_name'])){
                $user_name_data = array(
                    'user_name' => $data['user_name'],
                    'is_inside' => 1,
                    'is_verify' => 1,
                );
                $result = M('UserAuths')->where(array('user_id' => $user_id,'user_type' => 'username'))->save($user_name_data);
                if ($result === false) {
                    $this->rollback();
                    $this->error = '用户名重复';
                    return false;
                }                
            }
            $this->commit();            
        }
        if ($result === false) {
            return false;
        }
        
        $type_ids = I('post.type_ids', array(), 'intval');
        if (!empty($type_ids)) {
            $data = M('WorkType')->where(array('status' => 1, 'level' => 2, 'type_id' => array('in', $type_ids)))->field($user_id . ' as user_id,parent_id as type_id_1,type_id as type_id_2')->limit('0,3')->select();
            if(!empty($data)){
                M('UserToWorkType')->where(array('user_id' => $user_id))->delete();
                M('UserToWorkType')->addAll($data);                   
            }
        }
    
        
        admin_log("用户添加成功", json_encode($data));
        return $result;        
    }    
}
