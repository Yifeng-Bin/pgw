<?php
namespace Index\Controller;
use Think\Controller;
class CommonController extends Controller {
  /*  protected function _initialize() {         
//        $stats_time = gmtime();
//        if (empty($_COOKIE['stats_uid'])) {
//            $stats_uid = md5(uniqid(mt_rand(), true));
//        } else {
//            $stats_uid = $_COOKIE['stats_uid'];
//        }
//        setcookie('stats_uid', $stats_uid, $stats_time + 86400 * 365, '/');
        $user_id = session('user_info.user_id') ? intval(session('user_info.user_id')) : 0;
        if ($user_id) {
            $userInfo = D('Users')->field('user_id,status')->find($user_id);
            if (empty($userInfo) || $userInfo['status'] == 0) { //判断用户可能呗删除或禁用
                $user_id = 0;
            }
            if ($user_id == 0) {
                $cookies_remember_token = cookie('remember_token');
                if (!empty($cookies_remember_token)) {
                    $cookies_remember_arr = explode('|', $cookies_remember_token);
                    if (!empty($cookies_remember_arr) || count($cookies_remember_arr) >= 2) {
                        $cookies_remember_arr[0] = intval($cookies_remember_arr[0]);
                        $remember_token = D('Users')->where(array('user_id' => $cookies_remember_arr[0]))->getField('remember_token');
                        if (!empty($remember_token)) {
                            $remember_token_arr = explode('|', $remember_token);
                            if ($remember_token_arr > 2 && $remember_token_arr[0] == $cookies_remember_arr[1] && $remember_token_arr[1] > gmtime()) {
                                $user_id = $cookies_remember_arr[0];
                            }
                        }
                    }
                }
                if ($user_id == 0) {
                    D("Users")->logout();
                    D("Users")->setSession($user_id);
                } else {
                    D("Users")->setSession($user_id);
                }
            }
        } else {
            $user_id = 0;
            $cookies_remember_token = cookie('remember_token');
            if (!empty($cookies_remember_token)) {
                $cookies_remember_arr = explode('|', $cookies_remember_token);
                if (!empty($cookies_remember_arr) || count($cookies_remember_arr) >= 2) {
                    $cookies_remember_arr[0] = intval($cookies_remember_arr[0]);
                    $remember_token = D('Users')->where(array('user_id' => $cookies_remember_arr[0]))->getField('remember_token');
                    if (!empty($remember_token)) {
                        $remember_token_arr = explode('|', $remember_token);
                        if ($remember_token_arr > 2 && $remember_token_arr[0] == $cookies_remember_arr[1] && $remember_token_arr[1] > gmtime()) {
                            $user_id = $cookies_remember_arr[0];
                        }
                    }
                }
            }

            if ($user_id != 0) {
                D("Users")->setSession($user_id);
            }
        }

        define('USER_ID', $user_id);
        define('SESS_ID', session_id());
        $this->userInfo = D('Users')->getUserInfo();
        if (!session('xss_code')) {
            session('xss_code', get_xss_code());
        }        
        //读取配置信息
        C(M('Config')->getField('name,value'));
        //读取配置信息

        $domain = $_SERVER['HTTP_HOST'];         
        $domain_arr = explode('.', $domain);
        $region_id = 0;
        if ($domain_arr[1] == C('city_domain_prefix')) {
            $regionInfo = M('Region')->field('region_id,parent_id,region_name,domain')->where(array('domain' => $domain_arr[0], 'status' => 1, 'level' => C('REGION_LEVEL')))->find();
            if (!empty($regionInfo)) {
                $region_id = $regionInfo['region_id'];
                cookie('region_id', $region_id, 365 * 24 * 60 * 60);
            }
        }

        if ($region_id == 0) {
            $region_id = intval(cookie('region_id'));
            $regionInfo = M('Region')->field('region_id,parent_id,region_name,domain')->where(array('region_id' => $region_id, 'status' => 1, 'level' => C('REGION_LEVEL')))->find();           //var_dump($regionInfo);die();
            if (empty($regionInfo)){                
                if (CONTROLLER_NAME != 'City') {
                    $this->redirect('./pgw/?m=index&c=city&a=change');
                }
            } else {              //echo 'http://' . $regionInfo['domain'] . '.' . C('city_domain_prefix') . '.' . C('root_domain');die();
                cookie('region_id', $region_id, 365 * 24 * 60 * 60);
                \redirect('http://' . $regionInfo['domain'] . '.' . C('city_domain_prefix') . '.' . C('root_domain'), 0, '');              
            }
        }

        $this->regionInfo = $regionInfo;
        define('REGION_ID', $region_id);

        //获取网站基础url
        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on" || $_SERVER['HTTPS'] == "1")) {
            $this->baseurl = 'https://';
        } else {
            $this->baseurl = 'http://';
        }
        $this->baseurl .= $_SERVER['HTTP_HOST'] . __ROOT__ . '/';
        $this->baseurl = htmlspecialchars($this->baseurl);
        //获取网站基础url
        if (USER_ID != 0) {
        }                      //获取服务热线        $this->tel=M('TemplateData')->field('data_id,data_text,data_link')->where(array('position_id'=>14,'region_id'=>REGION_ID))->find();      
    }
    public function _empty() {
        $this->error("页面不存在", $this->baseurl);
    }	*/
}
