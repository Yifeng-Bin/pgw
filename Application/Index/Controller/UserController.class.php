<?php
namespace Index\Controller;
class UserController extends CommonController {
    //用户登录
    public function login() {
        if (USER_ID) {
            $this->success("已登录用户，请退出后再进行注册", $this->baseurl);
            exit();
        }
        if (IS_POST) {
            $user_name = I('post.user_name', '');
            $password = I('post.password', '');           
            //$verify = new \Think\Verify();
            //$checked = $verify->check(I('post.captcha', '', ''), 'login');
            //if (empty($checked)) {
            //    $this->error('验证码输入错误');
            //}
            if (empty($user_name) || empty($password)) {
                $this->error('帐号或密码不能为空');
            }            $i=D("Users")->login($user_name, $password);            
            if (D("Users")->login($user_name, $password)) {
                $remember = I('post.remember', '');                
                if (!empty($remember)) {
                    $randStr = \Org\Util\String::randString(20);
                    $user_id = session('user_info.user_id');
                    $time_out = gmtime() + 30 * 24 * 3600;                   
                    $remember_token = md5($user_id . $randStr);
                    M('Users')->where(array('user_id' => $user_id))->save(array('remember_token' => $remember_token . "|" . $time_out));                    
                    cookie('remember_token', $user_id . "|" . $remember_token, 30 * 24 * 3600);
                }
                $back_url = I('post.back_url', '', "htmlentities");                
                if (empty($back_url)) {                   
                    $this->success("登录失败！", $this->baseurl);
                } else {                     $login_time = time();                     M('Users')->where(array('user_name' => $user_name))->save(array('login_time' => $login_time));             
                                        $this->success("登录成功！", $back_url);
                }
            } else {              
                $this->error("登录失败！");
            }
        } else {
            $this->pageHeadInfo = array(
                'title' => '用户登录-' . C('web_name'),
                'keywords' => C('web_keywords'),
                'description' => C('web_description'),
            );
            $back_url = $_SERVER['HTTP_REFERER'];
            if (empty($back_url)) {
                $back_url = U("Index/index");
            }else{
                if(preg_match('/user\/[findpass|findpass]/i',$back_url)){
                    $back_url = U("User/index");
                }
            }
            $this->back_url = htmlentities($back_url);
            $this->display();
        }
    }

    //用户登录  
    public function logout() {
        if (!USER_ID) {
            $this->success("未登录无需退出操作", $this->baseurl);
        }
        if (session('xss_code') != I('get.code', '')) {
            $this->success("错误操作", $this->baseurl);
        }
        D('Users')->logout();
        \redirect($this->baseurl, 0, '');
    }

    //用户注册
    public function register() {
        if (USER_ID) {
            $this->redirect($this->baseurl);
            //$this->success("已登录用户，请退出后再进行注册", $this->baseurl);
            exit();
        }
        if (IS_POST) {
            $act = I('post.act', '');
            if ($act == 'getcode') {
                $mobile = I('post.mobile', '');               
                $is_mobile = \Com\Logdd\Validator::isMobile($mobile);
                if (!$is_mobile) {//手机号码错误
                    $this->error('手机号码格式不正确');
                }
                $mobile_is_exist = M('UserAuths')->where(array('user_name' => $mobile))->count();
                if (!empty($mobile_is_exist)) {
                    $this->error('手机号码已存在');
                }
                //验证号码正常
                $info = send_sms($mobile, 1);
                if ($info !== true) {
                    $this->error('验证码获取失败-' . $info);
                    exit();
                } else {
                    $this->success($info);
                    exit();
                }
            } else {
                $user_name = I('post.user_name', '', 'trim');
                $mobile = I('post.mobile', '');
                $mobilecode = I('post.mobilecode', '');
                $password = I('post.password', '');
                $region_id = I('post.region_id', 0, 'intval');
                $repassword = I('post.repassword', '');
                $region_id = I('post.region_id', 0, 'intval');
                $agree = I('post.agree', '');
                $user_type = I('post.user_type', 0, 'intval');
                if (!in_array($user_type, array(0, 1))) {
                    $user_type = 0;
                }
                if (empty($agree)) {
                    $this->error('同意注册协议后才能进行注册！');
                    exit();
                }
                if (empty($user_name)) {
                    $this->error('用户名不能为空');
                    exit();
                }
                $user_name_len = mb_strlen($user_name,'UTF-8');
                if($user_name_len < 2 || $user_name_len > 20){
                    $this->error('用户名长度为2-20个字符，请重新输入');
                    exit();                    
                }
                
                if (empty($password) || $password != $repassword) {
                    $this->error('密码不能为空或两次密码输入不同');
                    exit();
                }
                $regionInfo = M('Region')->where(array('region_id' => $region_id, 'level' => C('REGION_LEVEL')))->find();
                if (empty($regionInfo)) {
                    $this->error('请选择所在区域！');
                    exit();
                }
                $is_mobile = \Com\Logdd\Validator::isMobile($mobile);
                if (!$is_mobile) {//手机号码错误
                    $this->error('手机号码格式不正确！');
                    exit();
                }
                $codeInfo = get_last_sms($mobile, 1);
                if (empty($codeInfo)) {
                    $this->error('验证码不存在');
                    exit();
                }
                if ($codeInfo['code'] != $mobilecode) {
                    $this->error('验证码错误');
                    exit();
                }

                $data = array(
                    'user_name' => $user_name,
                    'add_time' => gmtime(),
                    'user_type' => $user_type,
                    'mobile' => $mobile,
                    'real_name' => $user_name,
                    'region_id' => $region_id,
                    'phone_is_validated' => 1,
                );
                $data['password'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

                $mobile_is_exist = M('UserAuths')->where(array('user_name' => $data['mobile']))->count();
                if (!empty($mobile_is_exist)) {
                    $this->error('手机号码已存在');
                    exit();
                }
                $mobile_is_exist = M('UserAuths')->where(array('user_name' => $data['user_name']))->count();
                if (!empty($mobile_is_exist)) {
                    $this->error('用户名已存在');
                    exit();
                }

                $userModel = M('Users');
                $userModel->startTrans();
   
                $user_id = $userModel->add($data);                
                if ($user_id === false) {
                    $userModel->rollback();
                    $this->error('注册未知错误！');
                    exit();
                }

                $data = array(
                    'user_id' => $user_id,
                    'user_type' => 'mobile',
                    'user_name' => $mobile,
                    'is_inside' => 1,
                    'is_verify' => 1,
                    'add_time' => gmtime(),
                );
                $result = M('UserAuths')->add($data);
                if ($result === false) {
                    $userModel->rollback();
                    $this->error('手机号码重复');
                    exit();
                }

                $data = array(
                    'user_id' => $user_id,
                    'user_type' => 'username',
                    'user_name' => $user_name,
                    'is_inside' => 1,
                    'is_verify' => 1,
                    'add_time' => gmtime(),
                );
                $result = M('UserAuths')->add($data);
                if ($result === false) {
                    $userModel->rollback();
                    $this->error('用户名重复');
                    exit();
                }
                $userModel->commit();

                M('SmsVerify')->where(array('id' => array('elt', $codeInfo['id'])))->save(array('status' => 0)); //使用过的验证码失效       
                D('Users')->setSession($user_id);
                \redirect($this->baseurl);
            }
        } else {
            $this->pageHeadInfo = array(
                'title' => '用户注册-' . C('web_name'),
                'keywords' => C('web_keywords'),
                'description' => C('web_description'),
            );
            $this->city_id = M('Region')->where(array('region_id' => REGION_ID))->getField('parent_id');
            $this->province_id = M('Region')->where(array('region_id' => $this->city_id))->getField('parent_id');
            $this->display();
        }
    }

    //我的问答
    public function ask() {
        if (!USER_ID) {
            $this->error("请登录后再进行查询", U("User/login"));
        }
        if (IS_POST) {
            $act = I('post.act', '');
            if ($act == 'del_comment') {
                $id = I('post.id', 0, 'intval');
                $where = array(
                    'c.comment_id' => $id,
                    'c.is_delete' => 0,
                    'c.user_id' => USER_ID,
                    'a.is_delete' => 0,
                );
                $is_belong = M('ArticleComment')->alias('c')->join('left join __ARTICLE__ as a on c.article_id =a.article_id')->where($where)->find();
                if (empty($is_belong)) {
                    $this->error('不能删除评论');
                } else {
                    M('ArticleComment')->where(array('comment_id' => $id))->save(array('is_delete' => 1));
                    $this->success('评论删除成功');
                }
            } else {
                $this->error('act不存在');
            }
        }
        $this->pageHeadInfo = array(
            'title' => "我的问答-".C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $type = I('get.type', 'question');
        if (!in_array($type, array('question', 'answer'))) {
            $type = 'question';
        }
        $this->type = $type;

        if ($type == 'question') {
            $where = array(
                'a.is_delete' => 0,
                'a.cat_id_1' => 51,
                'a.user_id' => USER_ID,
            );
            $count = M('Article')->alias('a')->where($where)->count();
            $Page = new \Think\Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            //$Page->lastSuffix = false;
            $Page->setConfig('first', '首页');
            $Page->setConfig('prev', '上一页');
            $Page->setConfig('next', '下一页');
            $Page->setConfig('last', '…%TOTAL_PAGE%');
            $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
            $Page->rollPage = 11;
            $this->pageShow = $Page->show();
            $list = M('Article')->alias('a')->field('a.article_id,a.article_name,a.comment_num,a.is_resolved,a.is_checked,a.add_time')->where($where)
                            ->limit($Page->firstRow . ',' . $Page->listRows)->order('add_time desc')->select();
            foreach ($list as &$info) {
                $info['add_time'] = local_date('Y-m-d H:i:s', $info['add_time']);
                $info['url'] = U('Ask/detail?id=' . $info['article_id']);
            }
            $this->list = $list;
        } else {
            $where = array(
                'a.is_delete' => 0,
                'a.cat_id_1' => 51,
                'c.user_id' => USER_ID,
                'c.is_delete' => 0,
            );
            $count = M('ArticleComment')->alias('c')->join('left join __ARTICLE__ as a on c.article_id = a.article_id')->where($where)->count();
            $Page = new \Think\Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            //$Page->lastSuffix = false;
            $Page->setConfig('first', '首页');
            $Page->setConfig('prev', '上一页');
            $Page->setConfig('next', '下一页');
            $Page->setConfig('last', '…%TOTAL_PAGE%');
            $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
            $Page->rollPage = 11;
            $this->pageShow = $Page->show();
            $list = M('ArticleComment')->alias('c')->join('left join __ARTICLE__ as a on c.article_id = a.article_id')
                            ->field('a.article_id,a.article_name,a.comment_num,a.is_resolved,a.best_comment_id,c.add_time,c.comment_id')->where($where)
                            ->limit($Page->firstRow . ',' . $Page->listRows)->order('add_time desc')->select();
            foreach ($list as &$info) {
                $info['add_time'] = local_date('Y-m-d H:i:s', $info['add_time']);
                $info['url'] = U('Ask/detail?id=' . $info['article_id']);
            }
            $this->list = $list;
        }

        $this->display();
    }    
    
    //案例管理
    public function cases() {
        if (!USER_ID) {
            $this->error("请登录后再进行查询", U("User/login"));
        }
        $this->pageHeadInfo = array(
            'title' => "案例管理-".C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $where = array(
            'a.user_id' => USER_ID,
            'a.cat_id_1' => 49,
            'a.is_delete' => 0,
        );
        $count = M('Article')->alias('a')->where($where)->count();
        $Page = new \Think\Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        //$Page->lastSuffix = false;
        $Page->setConfig('first', '首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '…%TOTAL_PAGE%');
        $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
        $Page->rollPage = 11;
        $this->pageShow = $Page->show();
        $list = M('Article')->alias('a')->join('__ARTICLE_CATEGORY__ as c on a.cat_id = c.cat_id')
                        ->field('a.article_id,a.article_name,a.cat_id,a.main_image,a.add_time,a.follow_num,a.region_id')->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($list as &$info) {
            $info['add_time'] = local_date('Y-m-d H:i', $info['add_time']);
            $info['url'] = U('Case/detail?id=' . $info['article_id']);
            $info['main_image'] = str_replace('style_org_img', 'style_thumb_img', $info['main_image']);
        }
        $this->list = $list;//var_dump($list);die();
        $this->display();
    }
    
    //装修日记
    public function diary() {
        if (!USER_ID) {
            $this->error("请登录后再进行查询", U("User/login"));
            exit();
        }
        if (IS_POST) {
            $act = I('post.act', '');
            if ($act == 'del') {
                $id = I('post.id', 0, 'intval');
                $diaryInfo = M('Article')->field('article_id,cat_id')->where(array('article_id' => $id, 'user_id' => USER_ID))->find();
                if (!empty($diaryInfo)) {
                    $pCatId = M('ArticleCategory')->where(array('cat_id' => $diaryInfo['cat_id']))->getField('parent_id');
                    if ($pCatId != 52) {
                        $id = 0;
                    }
                } else {
                    $id = 0;
                }
                if ($id != 0) {
                    M('Article')->where(array('article_id' => $id, 'user_id' => USER_ID))->save(array('is_delete' => 1));
                    $this->success('日志删除成功！');
                    exit();
                } else {
                    $this->error('日志不存在！');
                    exit();
                }
            } else {
                $this->error('操作不存在！');
                exit();
            }
        }

        $this->pageHeadInfo = array(
            'title' => "装修日记-".C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $type = I('get.type', '');
        if (!in_array($type, array('', 'comment'))) {
            $type = '';
        }
        $this->type = $type;
        if ($type == '') {
            $where = array(
                'a.user_id' => USER_ID,
                'a.cat_id_1' => 52,
                'a.is_delete' => 0,
            );
            $count = M('Article')->alias('a')->where($where)->count();
            $Page = new \Think\Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            //$Page->lastSuffix = false;
            $Page->setConfig('first', '首页');
            $Page->setConfig('prev', '上一页');
            $Page->setConfig('next', '下一页');
            $Page->setConfig('last', '…%TOTAL_PAGE%');
            $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
            $Page->rollPage = 11;
            $this->pageShow = $Page->show();
            $list = M('Article')->alias('a')->join('__ARTICLE_CATEGORY__ as c on a.cat_id = c.cat_id')
                            ->field('a.article_id,a.article_name,a.cat_id,a.is_checked,a.add_time,c.cat_name')->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order('add_time desc')->select();
            foreach ($list as &$info) {
                $info['add_time'] = local_date('Y-m-d H:i', $info['add_time']);
                $info['url'] = U('Diary/detail?id=' . $info['article_id']);
            }
            $this->list = $list;
        } else {
            $where = array(
                'a.user_id' => USER_ID,
                'a.cat_id_1' => 52,
                'a.is_delete' => 0,
                'c.is_checked' => 1,
                'c.is_delete' => 0,
            );
            $count = M('Article')->alias('a')->join('__ARTICLE_COMMENT__ as c on c.article_id =a.article_id')->where($where)->count();
            $Page = new \Think\Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            //$Page->lastSuffix = false;
            $Page->setConfig('first', '首页');
            $Page->setConfig('prev', '上一页');
            $Page->setConfig('next', '下一页');
            $Page->setConfig('last', '…%TOTAL_PAGE%');
            $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
            $Page->rollPage = 11;
            $this->pageShow = $Page->show();
            $list = M('Article')->alias('a')->join('__ARTICLE_COMMENT__ as c on c.article_id =a.article_id')
                    ->field('a.article_id,a.article_name,c.comment_id,c.add_time,c.content')
                    ->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            foreach ($list as &$info) {
                $info['add_time'] = local_date('Y-m-d H:i', $info['add_time']);
                $info['url'] = U('Diary/detail?id=' . $info['article_id']);
            }
            $this->list = $list;
        }
        if(WEB_TYPE == 'm' && $type == 'comment'){
            $this->display('User:diary_comment');
            exit();
        }
        $this->display();
    }    

    //我的收藏
    public function follow() {
        if (!USER_ID) {
            $this->error("请登录后再进行查询", U("User/login"));
        }
        $this->pageHeadInfo = array(
            'title' => '我的关注-' . C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $type = I('get.type', 'worker');
        if (!in_array($type, array('worker', 'diary', 'photo', 'teach'))) {
            $type = 'worker';
        }
        $this->type = $type;

        if ($type == 'worker') {
            $where = array(
                'f.user_id' => USER_ID,
                'u.is_delete' => 0,
                'u.status' => 1,
            );
            $count = M('Follow')->alias('f')->join('left join __USERS__ as u on f.follow_user_id = u.user_id')->where($where)->count();
            $Page = new \Think\Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            //$Page->lastSuffix = false;
            $Page->setConfig('first', '首页');
            $Page->setConfig('prev', '上一页');
            $Page->setConfig('next', '下一页');
            $Page->setConfig('last', '…%TOTAL_PAGE%');
            $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
            $Page->rollPage = 11;
            $this->pageShow = $Page->show();

            $list = M('Follow')->alias('f')->join('left join __USERS__ as u on f.follow_user_id = u.user_id')->where($where)
                            ->field('u.user_id,u.avatar,u.user_name,u.service_idea,u.enter_year,u.case_num,u.day_price,u.money,u.tenders_take_num,u.tenders_finish_num,u.tenders_dispute_num,u.tenders_doing_num,u.score1,u.score2,u.score3,u.is_safe,u.is_verified')
                            ->order('f.add_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->list = $list;
        } 
        elseif ($type == 'diary') {
            $where = array(
                'f.user_id' => USER_ID,
                'f.cat_id' => 52,
                'a.is_delete' => 0,
            );
            $count = M('ArticleFollow')->alias('f')->join('left join __ARTICLE__ as a on f.article_id = a.article_id')->where($where)->count();
            $Page = new \Think\Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            //$Page->lastSuffix = false;
            $Page->setConfig('first', '首页');
            $Page->setConfig('prev', '上一页');
            $Page->setConfig('next', '下一页');
            $Page->setConfig('last', '…%TOTAL_PAGE%');
            $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
            $Page->rollPage = 11;
            $this->pageShow = $Page->show();

            $list = M('ArticleFollow')->alias('f')->join('left join __ARTICLE__ as a on f.article_id = a.article_id')
                    ->join('left join __ARTICLE_CATEGORY__ as c on c.cat_id = a.cat_id')
                    ->where($where)->field('a.article_id,a.article_name,a.article_brief,a.comment_num,a.follow_num,c.cat_name,f.add_time,a.is_checked')
                            ->order('f.add_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            foreach($list as &$info){
                $info['url']  = U('Diary/detail?id='.$info['article_id']);
            }
            $this->list = $list;            
        } 
        elseif ($type == 'photo') {
             $where = array(
                'f.user_id' => USER_ID,
                'f.cat_id' => 2,
                'a.is_delete' => 0,
            );
            $count = M('ArticleFollow')->alias('f')->join('left join __ARTICLE__ as a on f.article_id = a.article_id')->where($where)->count();
            $Page = new \Think\Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            //$Page->lastSuffix = false;
            $Page->setConfig('first', '首页');
            $Page->setConfig('prev', '上一页');
            $Page->setConfig('next', '下一页');
            $Page->setConfig('last', '…%TOTAL_PAGE%');
            $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
            $Page->rollPage = 11;
            $this->pageShow = $Page->show();

            $list = M('ArticleFollow')->alias('f')->join('left join __ARTICLE__ as a on f.article_id = a.article_id')
                    ->where($where)->field('a.article_id,a.article_name,a.main_image,a.follow_num')
                            ->order('f.add_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            foreach($list as &$info){
                $info['url']  = U('Photo/detail?id='.$info['article_id']);
            }
            $this->list = $list;                
        } 
        elseif ($type == 'teach') {
            $where = array(
                'f.user_id' => USER_ID,
                'f.cat_id' => 4,
                'a.is_delete' => 0,
            );
            $count = M('ArticleFollow')->alias('f')->join('left join __ARTICLE__ as a on f.article_id = a.article_id')->where($where)->count();
            $Page = new \Think\Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            //$Page->lastSuffix = false;
            $Page->setConfig('first', '首页');
            $Page->setConfig('prev', '上一页');
            $Page->setConfig('next', '下一页');
            $Page->setConfig('last', '…%TOTAL_PAGE%');
            $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
            $Page->rollPage = 11;
            $this->pageShow = $Page->show();

            $list = M('ArticleFollow')->alias('f')->join('left join __ARTICLE__ as a on f.article_id = a.article_id')
                    ->join('left join __ARTICLE_CATEGORY__ as c on c.cat_id = a.cat_id')
                    ->where($where)->field('a.article_id,a.article_name,a.article_brief,c.cat_name,f.add_time,a.is_checked')
                            ->order('f.add_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            foreach($list as &$info){
                $info['url']  = U('Teach/detail?id='.$info['article_id']);
                $info['add_time']  = local_date('Y-m-d H:i:s', $info['add_time']);
            }
            $this->list = $list;                 
        }
        $this->display();
    }

    //我的金币
    public function gold() {
        if (!USER_ID) {
            $this->error("请登录后再进行查询", U("User/login"));
        }
        $this->pageHeadInfo = array(
            'title' => "我的金币-".C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        
        $type = I('get.type', 'all');
        if (!in_array($type, array('all', 'in','out','pay_log'))) {
            $type = 'all';
        }
        $this->type = $type;
        
        $where = array(
            'user_id' => USER_ID,
        );
        if($type == 'in'){
            $where['in_out'] = 0;
        }elseif($type == 'out'){
            $where['in_out'] = 1;
        }
        
        if($type == 'pay_log'){
            $where = array(
                'user_id' => USER_ID,
                'log_type' => 2,
            );
            $count = M('PayLog')->where($where)->count();
            $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            //$Page->lastSuffix = false;
            $Page->setConfig('first', '首页');
            $Page->setConfig('prev', '上一页');
            $Page->setConfig('next', '下一页');
            $Page->setConfig('last', '…%TOTAL_PAGE%');
            $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
            $Page->rollPage = 11;
            $this->pageShow = $Page->show();

            $list = M('PayLog')->alias('l')->join('left join __PAYMENT__ as p on p.pay_id = l.pay_id')->where($where)
                            ->field('l.log_id,l.pay_id,l.money,l.pay_status,l.log_time,p.pay_name')
                            ->order('l.log_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            foreach($list as &$info){
                $info['log_time'] = local_date('Y-m-d H:i:s', $info['log_time']);
                if($info['pay_status'] == 0){
                    $payObj = new \Common\Library\Pay\Pay();
                    $pay_code = M('Payment')->where(array('pay_id' => $info['pay_id'],'enabled' => 1,'is_online' => 1))->getField('pay_code');
                    $pay = $payObj->getDriver($pay_code);
                    if(empty($pay)){
                        $info['pay_button'] =  '';
                    }else{
                        $info['pay_button'] = $pay->buildRequestForm(array('order_amount' => $info['money'],'subject' => '购买金币' , 'order_sn' => $info['log_id']));
                    }              
                }
            }
            $this->total = $count;
            $this->list = $list;       
        }else{
            $count = M('IntegralLog')->where($where)->count();
            $Page = new \Think\Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            //$Page->lastSuffix = false;
            $Page->setConfig('first', '首页');
            $Page->setConfig('prev', '上一页');
            $Page->setConfig('next', '下一页');
            $Page->setConfig('last', '…%TOTAL_PAGE%');
            $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
            $Page->rollPage = 11;
            $this->pageShow = $Page->show();
            $list = M('IntegralLog')->field('change_time,change_type,change_desc,in_out,integral')->where($where)
                            ->limit($Page->firstRow . ',' . $Page->listRows)->order('change_time desc')->select();
            foreach ($list as &$info) {
                $info['change_time'] = local_date('Y-m-d H:i:s', $info['change_time']);
                if($info['change_type'] == 1){
                   $info['change_type'] = '上传头像'; 
                }elseif($info['change_type'] == 2){
                   $info['change_type'] = '回答问题'; 
                }elseif($info['change_type'] == 3){
                   $info['change_type'] = '写案例'; 
                }elseif($info['change_type'] == 4){
                   $info['change_type'] = '写日志'; 
                }elseif($info['change_type'] == 99){
                   $info['change_type'] = '购买金币'; 
                }else{
                    $info['change_type'] = '其他'; 
                }
            }
            $this->list = $list;            
        }
        

        $this->display();
    }
    
    //用户中心
    public function index() {
        if (!USER_ID) {
            $this->error("请登录后再进行查询", U("User/login"));
        }
        $this->pageHeadInfo = array(
            'title' => "用户中心-".C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $userBaseInfo = M('Users')->field('user_id,user_name,avatar,user_type,work_rank,mobile,money,gold,is_verified,is_safe')->where(array('user_id' => USER_ID))->find();
        $this->userBaseInfo = $userBaseInfo;
        $payList = M('Payment')->field('pay_id,pay_name,pay_code')->where(array('enabled' => 1,'is_online' => 1))->select();
        $this->payList = $payList;        
        
        $where = array(
            'a.user_id' => USER_ID,
            'a.cat_id_1' => 52,
            'a.is_delete' => 0,
        );
        $diaryList = M('Article')->alias('a')->join('__ARTICLE_CATEGORY__ as c on a.cat_id = c.cat_id')
                        ->field('a.article_id,a.article_name,a.cat_id,a.is_checked,a.add_time,c.cat_name')->where($where)->limit('0,10')->select();
        foreach ($diaryList as &$info) {
            $info['add_time'] = local_date('Y-m-d H:i', $info['add_time']);
            $info['url'] = U('Diary/detail?id=' . $info['article_id']);
        }
        $this->diaryList = $diaryList;     
        
        $projectInfo = array();
        $projectInfo['bid_num'] = M('Tender')->where(array('user_id' => USER_ID,'tender_type' => 1))->count();
        $projectInfo['appointment_num'] = M('Tender')->where(array('user_id' => USER_ID,'tender_type' => 2))->count();
        $projectInfo['wait_pay_num'] = M('Tender')->where(array('user_id' => USER_ID,'status' => 5))->count();
        $projectInfo['wait_check_num'] = M('Tender')->where(array('user_id' => USER_ID,'status' => 8))->count();
        $projectInfo['wait_comment_num'] = M('Tender')->where(array('user_id' => USER_ID,'status' => 9,'comment_score1' => 0))->count();
        $this->projectInfo = $projectInfo;  
        
        $this->display();
    }

    //修改资料
    public function profile() {
        if (!USER_ID) {
            $this->error("请登录后再进行查询", U("User/login"));
        }
        if (IS_POST) {
            $act = I('post.act', '');
            if ($act == 'modify_baseinfo') {
                $data = array();
                $data['real_name'] = I('post.real_name', '');
                $data['gender'] = I('post.gender', 0, 'intval');
                $data['birthday'] = I('post.birthday', '');
                $data['region_id'] = I('post.region_id', 0, "intval");

                if (empty($data['real_name'])) {
                    $this->error('姓名不能为空');
                    exit();
                }
        
                if (!in_array($data['gender'], array(0, 1, 2))) {
                    $data['gender'] = 0;
                }
                $birth_time = gmstr2time($data['birthday']);
                if ($birth_time == false) {
                    $this->error('生日格式不正确');
                    exit();
                } else {
                    $data['birthday'] = local_date("Y-m-d", $birth_time);
                }
                $regionInfo = M('Region')->where(array('region_id' => $data['region_id'], 'level' => C('REGION_LEVEL')))->find();
                if (empty($regionInfo)) {
                    $this->error('请选择所在区域！');
                    exit();
                }
                $data['update_time'] = gmtime();
                M('Users')->where(array('user_id' => USER_ID))->save($data);
                $this->redirect('User/profile');
                exit();
            } elseif ($act == 'modify_pass') {
                $data['org_password'] = I('post.org_password', '');
                $data['new_password'] = I('post.new_password', '');
                $data['re_password'] = I('post.re_password', '');
                if (empty($data['org_password']) || empty($data['new_password']) || empty($data['re_password'])) {
                    $this->error('输入密码不能为空！');
                    exit();
                }
                if ($data['new_password'] != $data['re_password']) {
                    $this->error('确认密码不正确！');
                    exit();
                }
                if ($data['org_password'] == $data['new_password']) {
                    $this->error('新密码和原始密码不能相同！');
                    exit();
                }
                $userInfo = M('Users')->field('password')->where(array('user_id' => USER_ID))->find();
                $is_true = password_verify($data['org_password'], $userInfo['password']);
                if (!is_true) {
                    $this->error('原密码不正确！');
                    exit();
                }
                $data['password'] = password_hash($data['new_password'], PASSWORD_BCRYPT, ['cost' => 10]);
                M('Users')->where(array('user_id' => USER_ID))->save(array('password' => $data['password'], 'update_time' => gmtime()));
                $this->success('密码修改成功，请退出后重新登录！');
                exit();
            } elseif ($act == 'modify_mobile') {
                $act1 = I('post.act1', '');
                if ($act1 == 'getcode') {
                    $mobile = I('post.mobile', '');
                    $is_mobile = \Com\Logdd\Validator::isMobile($mobile);
                    if (!$is_mobile) {//手机号码错误
                        $this->error('手机号码格式不正确');
                        exit();
                    }
                    $mobile_is_exist = M('UserAuths')->where(array('user_name' => $mobile))->count();
                    if (!empty($mobile_is_exist)) {
                        $this->error('手机号码已存在');
                    }
                    //验证号码正常
                    $info = send_sms($mobile, 3);
                    if ($info !== true) {
                        $this->error('验证码获取失败-' . $info);
                        exit();
                    } else {
                        $this->success($info);
                        exit();
                    }
                } else {
                    $mobile = I('post.mobile', '');
                    $mobilecode = I('post.mobilecode', '');
                    $is_mobile = \Com\Logdd\Validator::isMobile($mobile);
                    if (!$is_mobile) {//手机号码错误
                        $this->error('手机号码格式不正确！');
                        exit();
                    }
                    $codeInfo = get_last_sms($mobile, 3);
                    if (empty($codeInfo)) {
                        $this->error('验证码不存在');
                        exit();
                    }
                    if ($codeInfo['code'] != $mobilecode) {
                        $this->error('验证码错误');
                        exit();
                    }

                    $mobile_is_exist = M('UserAuths')->where(array('user_name' => $mobile))->count();
                    if (!empty($mobile_is_exist)) {
                        $this->error('手机号码已存在');
                        exit();
                    }
                    $had_mobile = M('UserAuths')->where(array('user_id' => USER_ID, 'user_type' => 'mobile'))->count();
                    if ($had_mobile) {
                        M('UserAuths')->where(array('user_id' => USER_ID, 'user_type' => 'mobile'))->save(array('user_name' => $mobile));
                    } else {
                        $data = array(
                            'user_id' => USER_ID,
                            'user_type' => 'mobile',
                            'user_name' => $mobile,
                            'is_inside' => 1,
                            'is_verify' => 1,
                            'add_time' => gmtime(),
                        );
                        $result = M('UserAuths')->add($data);
                        if ($result === false) {
                            $this->error('手机号码重复');
                            exit();
                        }
                    }
                    $result = M('Users')->where(array('user_id' => USER_ID))->save(array('mobile' => $mobile, 'update_time' => gmtime()));
                    if ($result === false) {
                        $this->error('服务器错误，请重试！');
                        exit();
                    }
                    M('SmsVerify')->where(array('type' => 3, 'id' => array('elt', $codeInfo['id'])))->save(array('status' => 0)); //使用过的验证码失效       
                    $this->success('手机号码修改成功！');
                    exit();
                }
            } elseif ($act == 'modify_avatar') { //修改用户头像
                $upload = new \Think\Upload();
                $upload->maxSize = 1024 * 1024; // 设置附件上传大小 1M
                $upload->exts = array('jpg', 'bmp', 'png'); // 设置附件上传类型
                $upload->rootPath = 'userfiles/'; // 设置附件上传根目录         
                $upload->savePath = USER_ID . '/avatar/'; // 设置附件上传根目录         
                $info = $upload->uploadOne($_FILES['Filedata']);
                $path = $upload->rootPath . $info['savepath'] . $info['savename'];
                if (empty($info)) {
                    $this->error($upload->getError(), '', true);
                    exit();
                } else {
                    $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 1);
                    if ($result !== false) {
                        //奖励积分 第一次上传头像
                        $status = appLock('', 0, "updateAvatar");
                        if ($status == 0) {
                            $this->error("服务器繁忙，请稍后再试！");
                        }
                        $avatar = M('Users')->where(array('user_id' => USER_ID))->getField('avatar');
                        if(empty($avatar)){
                            M('Users')->startTrans();
                            $data = array(
                                'user_id' => USER_ID,
                                'change_type' => 1,
                                'change_desc' => '上传头像',
                                'change_time' => gmtime(),
                                'in_out' => 0,
                                'integral' => C('send_integral_by_add_avatar'),
                            );
                            $log_id = M('IntegralLog')->add($data);
                            M('Users')->where(array('user_id' => USER_ID))->save(array('gold' => array('exp','gold+'.C('send_integral_by_add_avatar'))));
                        }
                        //奖励积分
                        $url = $result['url'];
                        M('Users')->where(array('user_id' => USER_ID))->save(array('avatar' => $url, 'update_time' => gmtime()));
                        M('Users')->commit();
                        appUnlock('', "updateAvatar");
                        unlink($path);
                        $this->success($url);
                    } else {
                        $this->error('文件上传出现错误');
                        exit();
                    }
                }
            } else {
                $this->error('act不存在');
                exit();
            }
        } else {
            $this->pageHeadInfo = array(
                'title' => "个人资料-".C('web_name'),
                'keywords' => C('web_keywords'),
                'description' => C('web_description'),
            );
            $userInfo = M('Users')->field('user_id,user_name,real_name,avatar,user_type,work_rank,mobile,money,is_verified,gender,birthday,region_id')->where(array('user_id' => USER_ID))->find();
            $this->userInfo = $userInfo;

            $regionInfo = M('Region')->field('region_id,region_name,parent_id')->where(array('region_id' => $userInfo['region_id']))->find();
            $this->regionInfo = $regionInfo;
            $this->cityInfo = M('Region')->field('region_id,region_name,parent_id')->where(array('region_id' => $regionInfo['parent_id']))->find();
            $this->provinceInfo = M('Region')->field('region_id,region_name,parent_id')->where(array('region_id' => $this->cityInfo['parent_id']))->find();
            $this->display();
        }
    }

    //提交日志
    public function write_diary() {
        if (!USER_ID) {
            $this->error("请登录后再进行查询", U("User/login"));
            exit();
        }
        if (IS_POST) {
            $id = I('post.id', 0, 'intval');
            $diaryInfo = M('Article')->field('article_id,cat_id')->where(array('article_id' => $id, 'user_id' => USER_ID))->find();
            if (!empty($diaryInfo)) {
                $pCatId = M('ArticleCategory')->where(array('cat_id' => $diaryInfo['cat_id']))->getField('parent_id');
                if ($pCatId != 52) {
                    $id = 0;
                }
            } else {
                $id = 0;
            }

            $cat_id = I('post.cat_id', 0, 'intval');
            $cat_id = M('ArticleCategory')->where(array('parent_id' => 52, 'status' => 1, 'cat_id' => $cat_id))->getField('cat_id');
            if (empty($cat_id)) {
                $this->error("请选择日志分类！");
                exit();
            }
            $data = array(
                'article_name' => I('post.article_name', '', 'strip_tags,htmlspecialchars'),
                'article_desc' => I('post.article_desc', '', 'clean_xss'),
                'cat_id' => $cat_id,
                'cat_id_1' => 52,
                'cat_id_2' => $cat_id,
            );
            if (empty($data['article_name'])) {
                $this->error("日志标题不能为空");
                exit();
            }
            if (empty($data['article_desc'])) {
                $this->error("日志内容不能为空");
                exit();
            }

            preg_match("/<[img|IMG].*?src=[\'|\"](.*?[\.bmp|\.jpg|\.png].*?)[\'|\"].*?[\/]?>/", $data['article_desc'], $match);
            if (isset($match[1]) && !empty($match[1])) {
                $data['main_image'] = strip_tags($match[1]);
            }

            $data['article_brief'] = mb_substr(strip_tags($data['article_desc']), 0, 255, 'UTF-8');
            $data['is_checked'] = 0;
            $data['add_time'] = gmtime();
            $data['user_id'] = USER_ID;
            if ($id != 0) {
                M('Article')->where(array('article_id' => $id))->save($data);
            } else {
                $id = M('Article')->add($data);
                //奖励积分 预赠送积分写入，后台审核后赠送
                $start_time = gmstr2time('today');
                $end_time = $start_time + 24 * 3600;
                $where =array(
                    'user_id' => USER_ID,
                    'add_time' => array(array('gt' => $start_time),array('lt' => $end_time),'and'),
                    'cat_id_1' => 52,
                    'article_id' => array('ELT' => $id),
                );
                $times = M('Article')->where($where)->count();
                if($times <= C('send_integral_times_by_add_diary')){
                    M('Article')->where(array('article_id' => $id))->save(array('pre_integral' => C('send_integral_by_add_diary')));
                }
                //奖励积分
                
            }

            $this->success('日志提交成功，等待管理员审核！', U('User/diary'));
            exit();
        }
        $this->pageHeadInfo = array(
            'title' => "写日志-".C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $this->diaryCat = M('ArticleCategory')->field('cat_id,cat_name')->where(array('parent_id' => 52, 'status' => 1))->select();
        $id = I('get.id', 0, 'intval');
        $diaryInfo = M('Article')->field('article_id,article_name,cat_id,article_desc')->where(array('article_id' => $id, 'user_id' => USER_ID))->find();
        if (!empty($diaryInfo)) {
            $pCatId = M('ArticleCategory')->where(array('cat_id' => $diaryInfo['cat_id']))->getField('parent_id');
            if ($pCatId != 52) {
                $diaryInfo = array(
                    'article_id' => 0,
                    'article_name' => '',
                    'cat_id' => 0,
                    'article_desc' => '',
                );
            }
        } else {
            $diaryInfo = array(
                'article_id' => 0,
                'article_name' => '',
                'cat_id' => 0,
                'article_desc' => '',
            );
        }
        $this->diaryInfo = $diaryInfo;
        $this->display();
    }
    //提交案例
    public function write_case() {
        if (!USER_ID) {
            $this->error("请登录后再进行查询", U("User/login"));
            exit();
        }		
        if (IS_POST) {
            $act = I('post.act', '');
            if ($act == 'upload_attachment') {
                $upload = new \Think\Upload();
                $upload->maxSize = 1024 * 1024; // 设置附件上传大小 1M
                $upload->exts = array('jpg', 'bmp', 'png'); // 设置附件上传类型
                $upload->rootPath = 'userfiles/'; // 设置附件上传根目录         
                $upload->savePath = USER_ID . '/case/'; // 设置附件上传根目录         
                $info = $upload->uploadOne($_FILES['Filedata']);
                $path = $upload->rootPath . $info['savepath'] . $info['savename'];
                if (empty($info)) {
                    $this->error($upload->getError(), '', true);
                } else {
                    $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 2);                
                    if ($result !== false) {
                        $url = $result['url'];
                        $arr = array(
                            'url' => $url,
                            'path' => $path,
                            'user_id' => USER_ID,
                            'add_time' => gmtime(),
                        );                      
                        $img_id = M('ArticlePhoto')->add($arr);
                        if ($img_id) {
                            $result['img_id'] = $img_id;
                            $result['url'] = str_replace('style_org_img', 'style_thumb_img', $url);
                            unlink($path);
                            $this->success($result);
                        } else {
                            $this->error('服务器处理错误，请重试');
                            exit();
                        }
                    } else {
                        $this->error('文件上传出现错误');
                        exit();
                    }
                }
            } else {
                $data = array(
                    'article_name' => I('post.case_name', '', 'trim,strip_tags,htmlspecialchars'),
                    'article_desc' => I('post.case_add', '', 'trim,strip_tags,htmlspecialchars'),
                    'cat_id' => 49,
                    'cat_id_1' => 49,
                    'is_checked' => 0,
                    'add_time' => gmtime(),
                    'user_id' => USER_ID,                    
                );                 $data['loupan_id']=I('post.loupan_id',0,'intval');                  $data['case_type']=I('post.case_type',0,'intval');                 $data['region_id']=I('post.region_id',0,'intval');                 $data['area']=I('post.area','');                 $type_desc=array(                      'house_type'=>I('post.house_type',''),                      'style_type'=>I('post.style_type',''),                      'price'     =>I('post.price',''),
                      'type'     =>I('post.type','')
                 );                 $data['type_desc']=serialize($type_desc);              
                if (empty($data['article_name'])) {
                    $this->error('不能为空');
                    exit();
                }                if (empty($data['area'])) {                                    $this->error('面积不能为空');                                    exit();                                }                if ($data['case_type']==0) {                                    $this->error('请选择案例类型');                                    exit();                                }                if ($data['loupan_id']==0) {                                    $this->error('请选择小区');                                    exit();                                }
                $img_ids = I('post.img_id', array(), 'intval');
                $img_descs = I('post.img_desc', array(), 'trim,strip_tags');//////添加数据                          //var_dump($data);die();
                if (!empty($img_ids)) {
                    $img_ids = M('ArticlePhoto')
                            ->where(array('img_id' => array('in', $img_ids), 'user_id' => USER_ID, 'article_id' => 0))
                            ->getField('img_id', true);
                }else{
                   $this->error('案例必须上传图片！');
                   exit();
                }       
                $result = M("Article")->add($data);								M('Users')->where('user_id='.USER_ID)->setInc('case_num');					
                if ($result !== false) {
                    if (!empty($img_ids)) {
                        foreach ($img_ids as $key => $img_id) {
                            M('ArticlePhoto')
                                    ->where(array('img_id' => $img_id, 'user_id' => USER_ID, 'article_id' => 0))
                                    ->save(array('article_id' => $result, 'img_desc' => $img_descs[$key]));
                        }
                    }
                    $photo_num = M('ArticlePhoto')->where(array('article_id' => $result))->count();
                    M("Article")->where(array('article_id' => $result))->save(array('photo_num' => $photo_num));
                    if ($photo_num > 0) {
                        $main_image = M('ArticlePhoto')->where(array('article_id' => $result))->order('sort desc')->getField('url');
                        M("Article")->where(array('article_id' => $result))->save(array('main_image' => $main_image));
                    }
                    //奖励积分 预赠送积分写入，后台审核后赠送
                    $start_time = gmstr2time('today');
                    $end_time = $start_time + 24 * 3600;
                    $where =array(
                        'user_id' => USER_ID,
                        'add_time' => array(array('gt' => $start_time),array('lt' => $end_time),'and'),
                        'cat_id_1' => 49,
                        'article_id' => array('ELT' => $result),
                    );
                    $times = M('Article')->where($where)->count();
                    if($times <= C('send_integral_times_by_add_case')){
                        M('Article')->where(array('article_id' => $result))->save(array('pre_integral' => C('send_integral_by_add_case')));
                    }
                    //奖励积分
                    
                    $this->success("案例提交成功，请等待管理员审核", U("User/cases"));
                    exit();
                } else {
                    $this->error("案例提交失败，请重新提交，如提交继续出现问题，请联系客服人员进行处理！");
                    exit();
                }
            }
        } else {
            $this->pageHeadInfo = array(
                'title' => '增加案例-' . C('web_name'),
                'keywords' => C('web_keywords'),
                'description' => C('web_description'),
            ); //数据显示                   $this->house_type=M('ArticleAttrValue')->where(array('attr_id' =>3))->select();            $this->style_type=M('ArticleAttrValue')->where(array('attr_id' =>2))->select();            $this->type=M('LoupanAttrValue')->where(array('attr_id' =>0))->select();            //$this->price=M('LoupanAttrValue')->where(array('attr_id' =>1))->select();                       
            $this->display();
        }
    }
//选择小区public function select_loupan(){    $region_id=I('get.region_id',0,'intval');     $get_page=I('get.page',0,'intval');    $loupan_name=I('get.loupan_name','');    if($region_id!=0){          $where['region_id']=$region_id;    }    if(!empty($loupan_name)){          $where['loupan_name']=array('like','%'.$loupan_name.'%');    }    $count = M('Loupan')->where($where)->count();    if($get_page!=0){            $page=$get_page;    }else{        $page=1;  //第一页    }    $pagesize=20;   //每页条数    $pages=ceil($count/$pagesize);//总页数    $offset=$pagesize*($page-1);//偏移量    $list=M("Loupan")->field('loupan_id,loupan_name')->where($where)->limit($offset, $pagesize)->select();     $list1['list']=$list;    $list1['page']=$page;    echo json_encode($list1);}//删除案例   public function del_case(){       $article_id=I('get.article_id',0,'intval');       $res=M('Article')->where(array('article_id'=>$article_id))->save(array('is_delete'=>1));	   	   M('Users')->where('user_id='.USER_ID)->setDec('case_num');      //var_dump($res);die();       if(!$res){                      $this->error('删除失败');       }else{                     $this->success('删除成功',U('User/cases'));       }   } //编辑案例   public function edit_case(){     if($_POST){           //var_Dump($_POST);				            $data['article_id']=I('post.article_id',0,'intval');           $data['loupan_id']=I('post.loupan_id',0,'intval');           $data['region_id']=I('post.region_id',0,'intval');           $data['article_name']=I('post.case_name', '', 'trim,strip_tags,htmlspecialchars');           $data['article_desc']=I('post.case_add', '', 'trim,strip_tags,htmlspecialchars');           $data['add_time']=time();           $data['case_type']=I('post.case_type',0,'intval');		   $data['area']=I('post.area',0,'intval');           $type_desc=array(               'house_type'=>I('post.house_type',''),               'style_type'=>I('post.style_type',''),               'price'     =>I('post.price',''),               'type'     =>I('post.type','')           );           $data['type_desc']=serialize($type_desc);                    $res=M('Article')->where(array('article_id'=>$data['article_id']))->save($data);      // var_dump($res);die();           if(!$res){                        $this->error('编辑失败');            }else{                       $this->success('编辑成功',U('User/cases'));            }   }else{       $article_id=I('get.article_id',0,'intval');            $this->region_id=$region_id=I('get.region_id','intval');       $this->city_id = M('Region')->where(array(           'region_id' => $region_id       ))->getField('parent_id');       $this->province_id = M('Region')->where(array(           'region_id' => $this->city_id       ))->getField('parent_id');       $this->house_type=M('ArticleAttrValue')->where(array('attr_id' =>3))->select();       $this->style_type=M('ArticleAttrValue')->where(array('attr_id' =>2))->select();       $this->type=M('LoupanAttrValue')->where(array('attr_id' =>0))->select();       //$this->price=M('LoupanAttrValue')->where(array('attr_id' =>1))->select();       $this->list=$list=M('Article')->alias('a')->field("a.article_id,a.article_name,a.article_desc,a.type_desc,a.case_type,a.loupan_id,a.area,l.loupan_name")       ->join('left join __LOUPAN__ as l on a.loupan_id =l.loupan_id')       //->join('left join __ARTICLE_PHOTO__ as p on a.article_id =p.article_id')       ->where(array('article_id'=>$article_id))       ->find();       $this->house_type1=isset(unserialize($list['type_desc'])['house_type'])?unserialize($list['type_desc'])['house_type']:'';       $this->style_type1=isset(unserialize($list['type_desc'])['style_type'])?unserialize($list['type_desc'])['style_type']:'';       $this->type1= isset(unserialize($list['type_desc'])['type'])?unserialize($list['type_desc'])['type']:'';       $this->price1=isset(unserialize($list['type_desc'])['price'])?unserialize($list['type_desc'])['price']:'';      // var_dump( isset(unserialize($list['type_desc'])['price'])?unserialize($list['type_desc'])['price']:'');die();       $this->display();     }   }
    //我的需求
    public function requirement() {
        if (!USER_ID) {
            $this->error("请登录后再进行查询", U("User/login"));
        }
        $this->pageHeadInfo = array(
            'title' => "我的需求-".C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        
        $where = array(
            't.is_checked' => 1,
            't.is_delete' => 0,
            't.status' => array('neq',0),
            't.user_id' => USER_ID,
        );
        
        $type = I('get.type', '');
        if (!in_array($type, array('','1', '2','3','4'))) {
            $type = '';
        }
        $this->type = $type;
        
        if($type == 1){ //待雇佣
            $where['t.status'] = STATUS_WAIT_EMPLOY;
        }elseif($type == 2){  //待付款
            $where['t.status'] = STATUS_WAIT_PAY;
        }elseif($type == 3){  //待验收
            $where['t.status'] = STATUS_WAIT_CHECK;
        }elseif($type == 4){  //待评价
            $where['t.status'] = STATUS_IS_FINISH;
            $where['t.comment_time'] = 0;
        }
        
        $count  = M('Tender')->alias('t')->where($where)->count();
        $Page  = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        //$Page->lastSuffix = false;
        $Page->setConfig('first', '首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '…%TOTAL_PAGE%');
        $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
        $Page->rollPage = 11;
        $this->pageShow = $Page->show();
        
        $requirementList =M('Tender')->alias('t')->field('t.tender_id,t.contacts,t.tender_name,wt.type_name,t.add_time,t.status,t.order_price,t.pay_total,t.finish_status,t.bidder_num,t.budget,t.closing_time,t.order_price,u.user_name as bid_user_name,t.comment_score1')
                ->join('left join __WORK_TYPE__ as wt on t.work_type_id = wt.type_id')
                ->join('left join __USERS__ as u on u.user_id = t.bidder_id')
                ->where($where)->order('add_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $tender_ids = array();
        foreach($requirementList as &$info){
            //$list['contacts'] = substr_cut($list['contacts']);
            $info['add_time'] = local_date('Y-m-d H:i:s', $info['add_time']);
            $info['status_desc'] = L('TENDER_STATUS_'.$info['status']);
            if($info['status'] == STATUS_IS_FINISH){
                $tender_ids = $info['tender_id'];
                $info['status_desc'] = $info['status_desc'].'('.L('TENDER_FINISH_STATUS_'.$info['finish_status']).')';
            }
            if($info['status'] == STATUS_WAIT_PAY){
                $info['pay_list'] = M('PayLog')->field('log_id,money')->where(array('log_type' => 3,'out_id' => $info['tender_id']));
            }
        }
        if(!empty($tender_ids)){
            $after_sale_arr = array();
            $after_sale_refund_arr = array();
            $after_sale_refund_arr = M('TenderAfterSale')->where(array('type' => 1,'tender_id' => array('in',$tender_ids)))->group('tender_id')->getField('tender_id,count(id) as after_sale_num');
            $after_sale_arr = M('TenderAfterSale')->where(array('status' => 1,'type' => 99,'tender_id' => array('in',$tender_ids)))->group('tender_id')->getField('tender_id,count(id) as after_sale_num');
            foreach($requirementList as &$info){
                if(isset($after_sale_arr[$info['tender_id']])){
                    $info['after_sale_num'] = $info['tender_id'];
                }
                if(isset($after_sale_refund_arr[$info['tender_id']])){
                    $info['after_sale_refund_num'] = $info['tender_id'];
                }                
            }
        }

        $this->requirementList = $requirementList;    
        
        $payList = M('Payment')->field('pay_id,pay_name,pay_code')->where(array('enabled' => 1))->select();
        $this->payList = $payList;           
        
        $this->display();
    }

    //我的需求
    public function modify_requirement() {
        if (!USER_ID) {
            $this->error("请登录后再进行查询", U("User/login"));
        }
        $this->pageHeadInfo = array(
            'title' => "修改需求-".C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        
        $where = array(
            't.is_delete' => 0,
            't.status' => STATUS_WAIT_BID,
            't.user_id' => USER_ID,
            't.tender_id' => I('get.id',0,'intval'),
        );
        $info  = M('Tender')->field('tender_id,budget,tender_name,visit_time,add_time,region_id,work_type_id,closing_time,address,remark,days,work_rank')->alias('t')->where($where)->find();
        if(empty($info)){
            $this->error('需求不存在');
            exit();
        }
        $info['add_time'] = local_date('Y-m-d H:i:s', $info['add_time']);
        $info['attachmentList'] = M('TenderAttachment')->where(array('tender_id' => $info['tender_id']))->select();
        $info['city_id'] = M('Region')->where(array('region_id' => $info['region_id']))->getField('parent_id');
        $info['province_id'] = M('Region')->where(array('region_id' => $info['city_id']))->getField('parent_id');     
        $info['top_work_type_id'] = M('WorkType')->where(array('type_id' => $info['work_type_id']))->getField('parent_id');  
        $info['closing_time'] = local_date('Y-m-d H:i:s', $info['closing_time']);        
        $this->info = $info;
        $this->display();
    }    
    
    //我的工人信息
    public function workinfo() {
        if (!USER_ID) {
            $this->error("请登录后再进行查询", U("User/login"));
        }
        if (IS_POST) {
            $act = I('post.act', '');
            if ($act == 'modify_baseinfo') {
                $data = array();
                $data['qq'] = I('post.qq', '', 'intval');
                $data['enter_year'] = I('post.enter_year', 1, 'intval');
                if (!in_array($data['enter_year'], array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11))) {
                    $data['enter_year'] = 1;
                }
                $data['service_idea'] = I('post.service_idea', '', 'strip_tags,htmlspecialchars');
                $data['profile'] = I('post.profile', '', 'strip_tags,htmlspecialchars');
                $data['update_time'] = gmtime();
                $data['day_price'] = I('post.day_price', 0, 'intval');
                M('Users')->where(array('user_id' => USER_ID))->save($data);
                $this->redirect('User/workinfo');
                exit();
            } elseif ($act == 'modify_idinfo') {
                $workInfo = M('Users')->field('is_verified')->where(array('user_id' => USER_ID))->find();
                if($workInfo['is_verified'] == 1){
                    $this->error('已验证过身份证信息');
                    exit();
                }                
                $data = array();
                $data['id_card_number'] = I('post.id_card_number', '');
                $isIdCard = \Com\Logdd\Validator::isIdCard($data['id_card_number']);
                if ($isIdCard == false) {
                    $this->error('身份证号码有误', U('User/workinfo'));
                    exit();
                }
                $data['update_time'] = gmtime();
                $data['is_verified'] = 2;
                M('Users')->where(array('user_id' => USER_ID))->save($data);
                $this->redirect('User/workinfo');
                exit();
            } elseif ($act == 'modify_workinfo') {
                $type_ids = I('post.type_ids', array(), 'intval');
                if (empty($type_ids)) {
                    $this->error('请至少选择一个服务项目后再进行提交', U('User/workinfo'));
                    exit();
                }
                $data = M('WorkType')->where(array('status' => 1, 'level' => 2, 'type_id' => array('in', $type_ids)))->field(USER_ID . ' as user_id,parent_id as type_id_1,type_id as type_id_2')->limit('0,3')->select();
                M('UserToWorkType')->where(array('user_id' => USER_ID))->delete();
                M('UserToWorkType')->addAll($data);
                $this->redirect('User/workinfo');
                exit();
            } else {
                $this->error('act不存在');
                exit();
            }
        } else {
            $this->pageHeadInfo = array(
                'title' => "工人信息-".C('web_name'),
                'keywords' => C('web_keywords'),
                'description' => C('web_description'),
            );
            $workInfo = M('Users')->field('enter_year,qq,profile,service_idea,is_verified,id_card_number,id_card_image_01,id_card_image_02,day_price')->where(array('user_id' => USER_ID))->find();
            if (!empty($workInfo['id_card_image_01'])) {
                $workInfo['id_card_image_01'] = \Com\Logdd\AliyunOssManager::getSignedUrl($workInfo['id_card_image_01']);
            }
            if (!empty($workInfo['id_card_image_02'])) {
                $workInfo['id_card_image_02'] = \Com\Logdd\AliyunOssManager::getSignedUrl($workInfo['id_card_image_02']);
            }
            $workInfo['workTypeList'] = M('UserToWorkType')->where(array('user_id' => USER_ID))->getField('type_id_2', TRUE);
            $this->workInfo = $workInfo;
            $this->display();
        }
    }

    public function upload_id_card_image() {
        if (!USER_ID) {
            $this->error("请登录后再进行查询", U("User/login"));
        }
        if (IS_POST) {
            $workInfo = M('Users')->field('is_verified')->where(array('user_id' => USER_ID))->find();
            if($workInfo['is_verified'] == 1){
                $this->error('已验证过身份证信息');
                exit();
            }
            $type = I('post.type', '');
            if (!in_array($type, array('front', 'back'))) {
                $type = 'front';
            }
            $upload = new \Think\Upload();
            $upload->maxSize = 1024 * 1024; // 设置附件上传大小 1M
            $upload->exts = array('jpg', 'bmp', 'png'); // 设置附件上传类型
            $upload->rootPath = 'userfiles/'; // 设置附件上传根目录         
            $upload->savePath = USER_ID . '/id_card_images/'; // 设置附件上传根目录         
            $info = $upload->uploadOne($_FILES['Filedata']);
            $path = $upload->rootPath . $info['savepath'] . $info['savename'];
            if (empty($info)) {
                $this->error($upload->getError(), '', true);
            } else {
                $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 3);
                if ($result !== false) {
                    $data = array();
                    $type == 'front' ? $data['id_card_image_01'] = $path : $data['id_card_image_02'] = $path;
                    M('Users')->where(array('user_id' => USER_ID))->save($data);
                    $result['url'] = \Com\Logdd\AliyunOssManager::getSignedUrl($path);
                    $this->success($result['url']);
                    exit();
                } else {
                    $this->error('文件上传出现错误');
                    exit();
                }
            }
        } else {
            $this->error('无数据');
            exit();
        }
    }

    //我的订单
    public function service() {
        if (!USER_ID) {
            $this->error("请登录后再进行查询", U("User/login"));
        }
        $this->money = M('Users')->where(array('user_id' => USER_ID))->getField('money');
        $this->pageHeadInfo = array(
            'title' => "我的订单-".C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        
        $where = array(
            'user_id' => USER_ID,
            'log_type' => 1,
        );
        $count = M('PayLog')->where($where)->count();
        $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        //$Page->lastSuffix = false;
        $Page->setConfig('first', '首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '…%TOTAL_PAGE%');
        $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
        $Page->rollPage = 11;
        $this->pageShow = $Page->show();

        $payLogList = M('PayLog')->alias('l')->join('left join __PAYMENT__ as p on p.pay_id = l.pay_id')->where($where)
                        ->field('l.log_id,l.pay_id,l.money,l.pay_status,l.log_time,p.pay_name')
                        ->order('l.log_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach($payLogList as &$info){
            $info['log_time'] = local_date('Y-m-d H:i:s', $info['log_time']);
            if($info['pay_status'] == 0){
                $payObj = new \Common\Library\Pay\Pay();
                $pay_code = M('Payment')->where(array('pay_id' => $info['pay_id'],'enabled' => 1,'is_online' => 1))->getField('pay_code');
                $pay = $payObj->getDriver($pay_code);
                if(empty($pay)){
                    $info['pay_button'] =  '';
                }else{
                    $info['pay_button'] = $pay->buildRequestForm(array('order_amount' => $info['money'],'subject' => '支付保证金' , 'order_sn' => $info['log_id']));
                }              
            }
        }
        $this->total = $count;
        $this->payLogList = $payLogList;

        $payList = M('Payment')->field('pay_id,pay_name,pay_code')->where(array('enabled' => 1,'is_online' => 1))->select();
        $this->payList = $payList;
        $this->display();
    }

    //我的订单
    public function order() {
        if (!USER_ID) {
            $this->error("请登录后再进行查询", U("User/login"));
        }
        $this->pageHeadInfo = array(
            'title' => "我的订单-".C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        
        $where = array(
            't.is_checked' => 1,
            't.is_delete' => 0,
            't.status' => array('neq',0),
            'tu.user_id' => USER_ID,
        );
        $count  = M('TenderUser')->alias('tu')->join('left join __TENDER__ as t on t.tender_id = tu.tender_id')->where($where)->count();
        $Page  = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        //$Page->lastSuffix = false;
        $Page->setConfig('first', '首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '…%TOTAL_PAGE%');
        $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
        $Page->rollPage = 11;
        $this->pageShow = $Page->show();
        
        $orderList =M('TenderUser')->alias('tu')->join('left join __TENDER__ as t on t.tender_id = tu.tender_id')
                ->field('t.tender_id,t.bidder_id,t.contacts,t.tender_name,t.order_price,wt.type_name,t.add_time,t.status,t.finish_status,t.bidder_num,t.budget,t.closing_time,t.order_price,t.pay_total,t.pay_part1,t.pay_part2,t.pay_part3,t.pay_part4,t.guarantee_money,t.guarantee_date,tu.price')
                ->join('left join __WORK_TYPE__ as wt on t.work_type_id = wt.type_id')
                ->where($where)->order('add_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $tender_ids = array();
        foreach($orderList as &$info){
            //$list['contacts'] = substr_cut($list['contacts']);
            $info['add_time'] = local_date('Y-m-d H:i:s', $info['add_time']);
            $info['status_desc'] = L('TENDER_STATUS_'.$info['status']);
            if($info['status'] != STATUS_WAIT_EMPLOY && $info['bidder_id'] != USER_ID){
                $info['status_desc'] = '未中标';
            }else{
                if($info['status'] == STATUS_IS_FINISH){
                    $info['status_desc'] = $info['status_desc'].'('.L('TENDER_FINISH_STATUS_'.$info['finish_status']).')';
                    $tender_ids[] = $info['tender_id'];
                }
                if($info['status'] == STATUS_WAIT_PAY){
                    $info['pay_list'] = M('PayLog')->field('log_id,money')->where(array('log_type' => 3,'out_id' => $info['tender_id']));
                }
            }

            
        }
        if(!empty($tender_ids)){
            $after_sale_arr = array();
            $after_sale_refund_arr = array();
            $after_sale_refund_arr = M('TenderAfterSale')->where(array('type' => 1,'tender_id' => array('in',$tender_ids)))->group('tender_id')->getField('tender_id,count(id) as after_sale_num');
            $after_sale_arr = M('TenderAfterSale')->where(array('status' => 1,'type' => 99,'tender_id' => array('in',$tender_ids)))->group('tender_id')->getField('tender_id,count(id) as after_sale_num');
            foreach($orderList as &$info){
                if(isset($after_sale_arr[$info['tender_id']])){
                    $info['after_sale_num'] = $info['tender_id'];
                }
                if(isset($after_sale_refund_arr[$info['tender_id']])){
                    $info['after_sale_refund_num'] = $info['tender_id'];
                }                
            }            
        }        
        $this->orderList = $orderList;      
        
        $this->display();
    }

    //找回密码
    public function findpass() {
        if (USER_ID) {
            $this->error("您已经登录，无需使用找回密码功能",U('User/index'));
        }
        if (IS_POST) {
            $act = I('post.act', '');
            import('Common/Validator', COMMON_PATH, '.php');
            if ($act == 'getcode') {
                $mobile = I('post.mobile', '');
                $is_mobile = \Com\Logdd\Validator::isMobile($mobile);
                if (!$is_mobile) {//手机号码错误
                    $this->error('手机号码格式不正确');
                }
                $mobile_is_exist = M('Users')->where(array('mobile_phone' => $mobile))->count();
                if (empty($mobile_is_exist)) {
                    $this->error('手机号码不存在');
                }
                //验证号码正常
                $info = send_sms($mobile, 2);
                if ($info !== true) {
                    $this->error('验证码发送失败-' . $info);
                } else {
                    $this->success('验证码已发送，请查收');
                }
            } else {
                $mobile = I('post.mobile', '');
                $is_mobile = \Com\Logdd\Validator::isMobile($mobile);
                if (!$is_mobile) {//手机号码错误
                    $this->error('手机号码格式不正确');
                }
                $user_id = M('UserAuths')->where(array('user_name' => $mobile))->getField('user_id');
                if (empty($user_id)) {
                    $this->error('手机号码不存在');
                }
                $mobilecode = I('post.mobilecode', '');
                $password = I('post.password', '');
                $repassword = I('post.repassword', '');

                if (empty($password) || $password != $repassword) {
                    $this->error('密码不能为空或两次密码输入不同');
                    exit();
                }

                $codeInfo = get_last_sms($mobile, 2);
                if (empty($codeInfo)) {
                    $this->error('验证码不存在');
                    exit();
                }
                if ($codeInfo['code'] != $mobilecode) {
                    $this->error('验证码错误');
                    exit();
                }

                $data = array(
                    'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]),
                );
                M('Users')->where(array('user_id' => $user_id))->save($data);
                $this->success('密码修改成功',U('User/login'));
            }
        } else {
            $this->pageHeadInfo = array(
                'title' => '找回密码-' . C('web_name'),
                'keywords' => C('web_keywords'),
                'description' => C('web_description'),
            );            
            $this->display();
        }
    }                        
//在建工地public function  build(){    if (!USER_ID) {            $this->error("请登录后再进行查询", U("User/login"));            exit();        }    $count=M('LoupanBuild')->count();        $Page  = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)        //$Page->lastSuffix = false;        $Page->setConfig('first', '首页');        $Page->setConfig('prev', '上一页');        $Page->setConfig('next', '下一页');        $Page->setConfig('last', '…%TOTAL_PAGE%');        $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');        $Page->rollPage = 11;        $this->pageShow = $Page->show();    $this->list=M('LoupanBuild')->alias('a')->field('a.*,wt.loupan_name,w.attr_value')    ->join('left join __LOUPAN__ as wt on a.loupan_id = wt.loupan_id')    ->join('left join __ARTICLE_ATTR_VALUE__ as w on a.attr_id = w.attr_value_id')    ->where(array('user_id'=>USER_ID))    ->limit($Page->firstRow.','.$Page->listRows)->order('a.add_time desc')->select();   //var_Dump($this->list);die();    $this->display();}public function build_add(){    if (!USER_ID) {            $this->error("请登录后再进行查询", U("User/login"));            exit();        }    $this->res=M('ArticleAttrValue')->field('attr_value_id,attr_value')->where(array('attr_id'=>3))->select();    if($_POST){                $data=array();         $data['title']=I('post.title','');         $data['region_id']=I('post.region_id','');         $data['area']=I('post.area','');         $data['attr_id']=I('post.attr_id','');         $data['price']=I('post.price','');         $data['loupan_id']=I('post.loupan_id','');         $data['desc']=I('post.desc','');         $data['add_time']=time();         $data['status']=0;         $data['user_id']=USER_ID;		 $data['unit']=I('post.unit','');		 		$upload = new \Think\Upload();// 实例化上传类				$upload->maxSize   =     333145728 ;// 设置附件上传大小		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型		$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录		$upload->savePath  =     '/'; // 设置附件上传（子）目录		// 上传文件 		$info   =   $upload->upload(); 		$data['img'] = '/Uploads'.$info['img']['savepath'].$info['img']['savename'];		//print_r($data); exit;		          if(empty($data['title'])){             $this->error('标题不能为空');         }         if(empty($data['region_id'])){             $this->error('请选择区域');             die();         }         if(empty($data['area'])){             $this->error('请填写面积');             die();         }         if(empty($data['attr_id'])){             $this->error('请选择户型');             die();         }         if(empty($data['price'])){             $this->error('请填写价格');             die();         }         if(empty($data['loupan_id'])){             $this->error('请填写小区');             die();         }         if(empty($data['desc'])){             $this->error('描述不能为空');             die();         }        if(M('Loupan_build')->add($data)){            $go_url=U('User/build');            $this->success('添加成功', $go_url);            die();        } else{             $this->error('添加失败');            die();        }              }       $this->display();}//在建工地  下拉框楼盘public function build_name(){    if (!USER_ID) {            $this->error("请登录后再进行查询", U("User/login"));            exit();        }    $region_id=  I('get.region_id',0,'intval');    $res1=M('loupan')->field('loupan_id,loupan_name')->where(array('region_id'=>$region_id))->select();    echo json_encode($res1);}//public function build_edit(){    if (!USER_ID) {            $this->error("请登录后再进行查询", U("User/login"));            exit();        }    $id=I('get.id','');    $region_id=I('get.region_id','');        $this->region_id =$region_id=I('get.region_id','');    $this->city_id = M('Region')->where(array(        'region_id' => $region_id    ))->getField('parent_id');    $this->province_id = M('Region')->where(array(        'region_id' => $this->city_id    ))->getField('parent_id');    $this->res=M('ArticleAttrValue')->field('attr_value_id,attr_value')->where(array('attr_id'=>3))->select();    $this->reslist=M('LoupanBuild')->where(array('id'=>$id))->find();    $this->res1=M('Loupan')->field('loupan_name,loupan_id')->where(array('region_id'=>$region_id))->select();        $this->display();}  public function build_editok(){    if (!USER_ID) {            $this->error("请登录后再进行查询", U("User/login"));            exit();        }    $data=array();    $data['id']=I('post.id','');    $data['title']=I('post.title','');    $data['region_id']=I('post.region_id','');    $data['area']=I('post.area','');    $data['attr_id']=I('post.attr_id','');    $data['price']=I('post.price','');    $data['loupan_id']=I('post.loupan_id','');    $data['desc']=I('post.desc','');    $data['add_time']=time();  	$data['unit']=I('post.unit','');    	$upload = new \Think\Upload();// 实例化上传类				$upload->maxSize   =     333145728 ;// 设置附件上传大小		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型		$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录		$upload->savePath  =     '/'; // 设置附件上传（子）目录		// 上传文件 		$info   =   $upload->upload();				$data['img'] = '/Uploads'.$info['img']['savepath'].$info['img']['savename'];				$tupian = M('Loupan_build')->field('img')->where('id='.$data['id'])->find();		if(!empty($tupian['img'])){			unlink($tupian['img']);		}		    if(empty($data['title'])){        $this->error('标题不能为空');    }    if(empty($data['region_id'])){        $this->error('请选择区域');        die();    }    if(empty($data['area'])){        $this->error('请填写面积');        die();    }    if(empty($data['attr_id'])){        $this->error('请选择户型');        die();    }    if(empty($data['price'])){        $this->error('请填写价格');        die();    }    if(empty($data['loupan_id'])){        $this->error('请填写小区');        die();    }    if(empty($data['desc'])){        $this->error('描述不能为空');        die();    }  //  var_dump( $data);die();    $res=M('Loupan_build')->where(array('id'=>$data['id']))->save($data);    if($res){       $go_url=U('User/build');       $this->success('编辑成功', $go_url);            }else{                $this->error('编辑失败');            }      }public function build_del(){        $id = I('post.id', 0, 'intval');        $res=D('LoupanBuild')->where(array('id' => $id))->delete();         if($res){               echo json_encode('ok');     }else{                echo json_encode('no');    }        } public function build_made(){     if (!USER_ID) {              $this->error("请登录后再进行查询", U("User/login"));              exit();          }     $this->id=$id = I('get.id','');         $this->region_id =$region_id=I('get.region_id','');          $this->region = M('Region')->field('parent_id,region_name')->where(array(         'region_id' => $region_id     ))->find();          $this->city = M('Region')->field('parent_id,region_name')->where(array(         'region_id' => $this->region['parent_id']     ))->find();          $this->province = M('Region')->field('region_name')->where(array(         'region_id' => $this->city['parent_id']     ))->find();          $this->list=M('LoupanBuild')->alias('a')->field('a.id,a.title,a.price,a.desc,a.area,a.img,wt.loupan_name,w.attr_value')     ->join('left join __LOUPAN__ as wt on a.loupan_id = wt.loupan_id')     ->join('left join __ARTICLE_ATTR_VALUE__ as w on a.attr_id = w.attr_value_id')     ->where(array('a.id'=>$id))->find();     $this->madelist=M('LoupanBuildMade')->field('id,bid,made_name,made_time')->where(array('bid'=>$id))->select();    // var_Dump($this->province);die();     $this->display(); }
public function build_made_add(){    if (!USER_ID) {            $this->error("请登录后再进行查询", U("User/login"));            exit();        }    $data=array();    $data['made_desc']=I('post.made_desc','');       $this->id=I('get.id','');    //var_Dump($data['bid']);die();        if(!empty($data['made_desc'])){        $data['made_name']=I('post.made_name','');        $data['made_time']=time();        $data['bid']=I('post.bid','');				$upload = new \Think\Upload();// 实例化上传类				$upload->maxSize   =     333145728 ;// 设置附件上传大小		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型		$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录		$upload->savePath  =     '/'; // 设置附件上传（子）目录		// 上传文件 		$info   =   $upload->upload();				$data['img'] = '/Uploads'.$info['img']['savepath'].$info['img']['savename'];				//print_r($data);exit;		       if(M('Loupan_build_made')->add($data)){          // $go_url=U('User/build_made',array('id'=>$data['bid']));           $this->success("更新成功",U('User/build_made',array('id'=>$data['bid'])));           die();       }else{           $this->error('更新失败');           die();                  }               }       $this->display();}  public function build_made_edit(){      if (!USER_ID) {                $this->error("请登录后再进行查询", U("User/login"));                exit();            }      $this->id=$id=I('get.id','');      $this->madelist=M('LoupanBuildMade')->where(array('id'=>$id))->find();     //var_Dump($this->madelist);die();      $this->display();  }    public function build_made_editok(){      if (!USER_ID) {                $this->error("请登录后再进行查询", U("User/login"));                exit();            }      $data['made_name']=I('post.made_name','');      $data['made_time']=time();      $id=I('post.id','');      $bid=I('post.bid','');      $data['made_desc']=I('post.made_desc','');      	   	    $upload = new \Think\Upload();// 实例化上传类				$upload->maxSize   =     333145728 ;// 设置附件上传大小		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型		$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录		$upload->savePath  =     '/'; // 设置附件上传（子）目录		// 上传文件 		$info   =   $upload->upload(); 				$data['img'] = '/Uploads'.$info['img']['savepath'].$info['img']['savename'];				$tupian = M('Loupan_build_made')->field('img')->where('id='.$data['id'])->find();		if(!empty($tupian['img'])){			unlink($tupian['img']);		}		      if(empty($data['made_desc'])){          $this->error('描述不能为空');      }      if(M('Loupan_build_made')->where(array('id'=>$id))->save($data)){                    $this->success("编辑成功",U('User/build_made',array('id'=>$bid)));                }else{          $this->error('编辑失败');               }      //var_dump($data);die();  }    public function build_made_del(){      if (!USER_ID) {                $this->error("请登录后再进行查询", U("User/login"));                exit();            }      $id=I('get.id','');      $bid=I('get.bid','');      $res=M('LoupanBuildMade')->where(array('id'=>$id))->delete();      if($res){                    $this->success("删除成功",U('User/build_made',array('id'=>$bid)));                }else{          $this->error('删除失败');                }       }       }
