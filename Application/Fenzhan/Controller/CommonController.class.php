<?php

namespace Fenzhan\Controller;

use Think\Controller;
use Com\Logdd\Auth;

class CommonController extends Controller {

    protected function _initialize() {
        define('ADMIN_USER_ID', session('fz_user_info.user_id'));        define('ADMIN_CITY_ID', session('fz_user_info.region_id'));       
        if (!ADMIN_USER_ID) {
            $this->redirect('Public/login');
        }
        $name = CONTROLLER_NAME . '/' . ACTION_NAME;
        
        if ((!in_array(ADMIN_USER_ID, C('ADMINISTRATOR'))) && (strtolower($name) != 'index/index') && (strtolower($name) != 'index/info')) {
            $auth = new Auth();
            if (!$auth->check($name, ADMIN_USER_ID)) {
                $this->error('无权限，跳转到首页...', U('Index/info'));
            }
        }
        if (session('fz_user_info.is_admin') == 1) {
            define('IS_ADMIN', 1);
        } else {
            define('IS_ADMIN', 0);
        }
        $this->userInfo = D('AdminUser')->getUserInfo(ADMIN_USER_ID);        
        if (!session('xss_code')) {
            session('xss_code', get_xss_code());
        }        
        C(M('Config')->getField('name,value'));
        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on" || $_SERVER['HTTPS'] == "1")) {
            $this->baseurl = 'https://';
        } else {
            $this->baseurl = 'http://';
        }
        $this->baseurl .= $_SERVER['HTTP_HOST'] . __ROOT__ . '/';
        $this->baseurl = htmlspecialchars($this->baseurl);
    }
}
