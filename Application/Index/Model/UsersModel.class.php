<?php

namespace Index\Model;

use Think\Model;

class UsersModel extends CommonModel {

    public function getUserInfo() {
        if (USER_ID) {
            $userInfo = $this->field('user_id,user_name,user_type,real_name,region_id')->find(USER_ID);
            return $userInfo;
        } else {
            return array(
                'user_id' => 0,
                'user_name' => '游客',
                'user_type' => 0,
                'real_name' => '游客',
                'region_id' => 0,
            );
        }
    }

    public function login($user_name, $password) {
        $where = array(
            'user_name' => $user_name,
        );
        $UserAuthInfo = M('UserAuths')->where($where)->find();
        if (empty($UserAuthInfo)) {
            $this->error = '帐号不存在...';
            return false;
        }
        $where = array(
            'user_id' => $UserAuthInfo['user_id'],
        );
        $userInfo = $this->field('user_id,status,password')->where($where)->find();

        if (empty($userInfo) || $userInfo['status'] == 0 || $userInfo['password'] == '') {
            $this->error = '帐号不存在...';
            return false;
        }
        if (password_verify($password, $userInfo['password'])) {
            $this->setSession($UserAuthInfo['user_id']);
            return true;
        } else {
            $this->error = '登录失败...';
            return false;
        }
    }

    public function setOauthUser($openid, $type, $openUserInfo) {

        $user_id = M('UserAuths')->where(array('user_name' => $type . "_" . $openid, 'user_type' => $type))->getField('user_id');
        if (empty($user_id)) {//用户不存在则新增
            
            if(empty($openUserInfo['nick'])){
                 $openUserInfo['nick'] = 'pcyg_'.\Org\Util\String::randString(6, 1);
            }
            if(empty($openUserInfo['name'])){
                 $openUserInfo['name'] = $openUserInfo['nick'];
            }            
            
            $data = array(
                'nick_name' => $openUserInfo['nick'],
                'password' => '',
                'status' => 1,
                'create_time' => gmtime(),
            );
            $this->startTrans();
            if (!$this->create($data, \Think\Model::MODEL_INSERT)) {
                $this->rollback();
                $this->error = '服务器小憩中，稍后再试！';
                return false;
            } else {
                $user_id = $this->add($data);
            }
            if ($user_id === false) {
                $this->rollback();
                $this->error = '注册未知错误！';
                return false;
            }

            $data = array(
                'user_id' => $user_id,
                'user_type' => $type,
                'auth_nick_name' => $openUserInfo['name'],
                'user_name' => $type . "_" . $openid,
                'is_inside' => 2,
                'is_verify' => 1,
            );
            if (!M('UserAuths')->create($data, \Think\Model::MODEL_INSERT)) {
                $this->rollback();
                $this->error = L('USER_TYPE_'.$type) . '错误';
                return false;
            } else {
                $result = M('UserAuths')->add($data);
                if ($result === false) {
                    $this->rollback();
                    $this->error = L('USER_TYPE_'.$type) . '重复';
                    return false;
                }
            }
            $this->commit();
        }else{
            M('UserAuths')->where(array('user_id' => $user_id))->save(array('auth_nick_name' => $openUserInfo['name']));
        }
        $this->setSession($user_id);

        return $user_id;
    }

    public function logout() {
        session(null);
        cookie('remember_token',null);
        //cookie('s_id',null);
        session('[regenerate]');
    }

    public function setSession($user_id) {
        if (empty($user_id)) {
            session('user_info.user_id', 0);
            session('user_info.user_ip', get_client_ip());
        } else {
            session('user_info.user_id', $user_id);
            session('user_info.user_ip', get_client_ip());
        }
        session('[regenerate]');
    }

}
