<?phpnamespace Fenzhan\Controller;
use Think\Controller;
class PublicController extends Controller {
    public function login() {
        if (session('fz_user_info.user_id')) {
            $this->error('已登录，请先退出...', U('Index/info'));
        }
        if (!IS_POST) {
            C(M('Config')->getField('name,value'));
            if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on" || $_SERVER['HTTPS'] == "1")) {
                $this->baseurl = 'https://';
            } else {
                $this->baseurl = 'http://';
            }
            $this->baseurl .= $_SERVER['HTTP_HOST'] . __ROOT__ . '/';
            $this->baseurl = htmlspecialchars($this->baseurl);            
            $this->display();
        } else {                        $verify = new \Think\Verify();            $checked = $verify->check(I('post.verify_code', '', ''), 'login');            if (empty($checked)) {                $this->error('验证码输入错误',U('login'));                exit();            }                                    $loginInfo = D('AdminUser')->login();            
            if ($loginInfo['error'] != 0) {                               
                $this->error($loginInfo['message'], U('login'));
                exit();
            }                              
            $userInfo = D('AdminUser')->getUserInfo($loginInfo['uid']);			print_r($userInfo); exit;            //读取配置信息                        C(M('Config')->getField('name,value'));                        //判断用户城市是否对应            $domain = $_SERVER['HTTP_HOST'];                         $domain_arr = explode('.', $domain);                        if ($domain_arr[1] != C('city_domain_prefix')) {                            $this->error("请选择分站城市！", U('login'));                                exit();                        }else{                                $regionInfo = M('Region')->field('region_id,parent_id,region_name,domain')->where(array('domain' => $domain_arr[0], 'status' => 1, 'level' => C('REGION_LEVEL')))->find();                                if (empty($regionInfo) || $regionInfo['region_id']!=$userInfo['region_id']) {                                    $this->error("分站城市错误！", U('login'));                                    exit();                                }                                if (empty($userInfo) || $userInfo['status'] == 0) {                                    $this->error('用户已禁用', U('login'));                                    exit();                                }                                session('fz_user_info.user_id', $userInfo['uid']);                session('fz_user_info.region_id', $userInfo['region_id']);                                if (in_array($userInfo['uid'], C('ADMINISTRATOR'))) {                                    session('fz_user_info.is_admin', 1);                                } else {                                    session('fz_user_info.is_admin', 0);                                }                                //跳转到首页                                $this->success('登录成功，正在跳转...', U('Index/index'));                                                            }           
        }
    }

    public function logout() {
        $uid = session('fz_user_info.user_id');
        if (!$uid) {
            $this->error('已退出...', U('Public/login'));
        } else {
            admin_log("用户退出后台成功", '', $uid);
            //session(null);            session('fz_user_info.user_id', null);
            $this->success('已退出...', U('Public/login'));
        }
    }

    public function upload() {
        $uid = session('fz_user_info.user_id');
        if (!$uid) {
            $this->error('已退出...', U('Public/login'));
        }
        $type = I('get.type', 0, 'intval');
        $upload = new \Think\Upload();
        //$upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $upload->rootPath = 'files/'; // 设置附件上传根目录

        if ($type == 1) {//工种图片
            $upload->savePath = 'work_type/'; // 设置附件上传（子）目录    
            $info = $upload->uploadOne($_FILES['Filedata']);
            if (empty($info)) {
                admin_log("用户上传工种图片出现错误:" . $upload->getError());
                $this->error($upload->getError(), '', true);
            } else {
                $info['rootpath'] = $upload->rootPath;
                $path = $info['rootpath'] . $info['savepath'] . $info['savename'];
                $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 1);
                if($result !== false){
                    admin_log("用户上传工种图片成功:" . $path, '', $uid);
                    $this->success($result, '', true);                    
                } else {
                    $this->error('上传到oss出现错误', '', true);   
                }
            }
        }elseif ($type == 2) {//文章图片图库
            $upload->savePath = 'photo/'; // 设置附件上传（子）目录    
            $info = $upload->uploadOne($_FILES['Filedata']);            //var_dump($info);die();
            if (empty($info)) {
                admin_log("用户上传工种图片出现错误:" . $upload->getError());
                $this->error($upload->getError(), '', true);
            } else {
                $info['rootpath'] = $upload->rootPath;
                $path = $info['rootpath'] . $info['savepath'] . $info['savename'];
                $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 2);
                if($result !== false){
                    $data = array(
                        'url' => $result['url'],
                        'path' => $path,
                        'add_time' => gmtime(),
                        'admin_id' => $uid,
                    );
                    $img_id = M('ArticlePhoto')->add($data);                        
                    $result['img_id'] = $img_id;
                    admin_log("用户上传图库图片成功:" . $path, '', $uid);
                    $this->success($result, '', true);             
                }
                else{
                    $this->error('上传到oss出现错误', '', true);   
                }
            }
        }elseif ($type == 3) {//广告图片
            $upload->savePath = 'affiche/'; // 设置附件上传（子）目录    
            $info = $upload->uploadOne($_FILES['Filedata']);
            if (empty($info)) {
                admin_log("广告图片出现错误:" . $upload->getError());
                $this->error($upload->getError(), '', true);
            } else {
                $info['rootpath'] = $upload->rootPath;
                $path = $info['rootpath'] . $info['savepath'] . $info['savename'];
                $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 1);                
                if($result !== false){
                    admin_log("用户广告图片成功:" . $path, '', $uid);
                    $this->success($result, '', true);         
                }
                else{
                    $this->error('上传到oss出现错误', '', true);   
                }                    

            }
        }elseif ($type == 4) {//模版数据图片
            $upload->savePath = 'block_info/';
            $info = $upload->uploadOne($_FILES['Filedata']);
            if (empty($info)) {
                admin_log("模版数据图片出现错误:" . $upload->getError());
                $this->error($upload->getError(), '', true);
            } else {
                $info['rootpath'] = $upload->rootPath;
                $path = $info['rootpath'] . $info['savepath'] . $info['savename'];
                $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 1);    
                if($result !== false){
                    admin_log("用户模版数据图片成功:" . $path, '', $uid);
                    $this->success($result, '', true);         
                }
                else{
                    $this->error('上传到oss出现错误', '', true);   
                }                    

            }
        }elseif ($type == 5) {//文章封面图片
            $upload->savePath = 'article_cover/';
            $info = $upload->uploadOne($_FILES['Filedata']);
            if (empty($info)) {
                admin_log("封面图片出现错误:" . $upload->getError());
                $this->error($upload->getError(), '', true);
            } else {
                $info['rootpath'] = $upload->rootPath;
                $path = $info['rootpath'] . $info['savepath'] . $info['savename'];
                $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 2);                   
                if($result !== false){
                    admin_log("文章封面图片成功:" . $path, '', $uid);
                    $this->success($result, '', true);         
                }
                else{
                    $this->error('上传到oss出现错误', '', true);   
                }
            }
        }elseif ($type == 6) {//楼盘图片图库            $upload->savePath = 'photo/'; // 设置附件上传（子）目录                $info = $upload->uploadOne($_FILES['Filedata']);                        if (empty($info)) {                admin_log("用户上传工种图片出现错误:" . $upload->getError());                $this->error($upload->getError(), '', true);            } else {                $info['rootpath'] = $upload->rootPath;                $path = $info['rootpath'] . $info['savepath'] . $info['savename'];                $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 2);                if($result !== false){                    $data = array(                        'url' => $result['url'],                        'path' => $path,                        'add_time' => gmtime(),                        'admin_id' => $uid,                    );                    $img_id = M('LoupanPhoto')->add($data);                                                               $result['img_id'] = $img_id;                    admin_log("用户上传图库图片成功:" . $path, '', $uid);                    $this->success($result, '', true);                             }                else{                    $this->error('上传到oss出现错误', '', true);                   }            }        } else {
            admin_log("用户上传图片出现类型错误");
            $this->error('未知类型', '', true);
        }
    }

    public function changepass() {
        $uid = session('fz_user_info.user_id');
        if (!$uid) {
            $this->error('已退出...', U('Public/login'));
        }
        import('Common/PasswordHash', COMMON_PATH, '.php');
        $pHash = new \PasswordHash(8, false);
        $password = M('FenzhanUser')->where(array('uid' => $uid))->getField('password');
        if (!$pHash->CheckPassword(I('post.password'), $password)) {
            admin_log("用户修改密码错误，原始密码不对", '', $uid);
            $this->error('密码错误', U('Index/index'));
        } else {
            $newpassword = $pHash->HashPassword(I('post.newpassword'));
            $result = M('FenzhanUser')->where(array('uid' => $uid))->save(array('password' => $newpassword));
            if ($result === false) {
                $this->error('更新错误', U('Index/index'));
            } else {
                admin_log("用户修改密码成功", '', $uid);
                $this->success('修改成功', U('Index/index'));
            }
        }
    }

    public function searchGoods() {
        $uid = session('fz_user_info.user_id');
        if (!$uid) {
            $this->error('非后台管理员');
        }
        $cat_id = I('post.cat_id', 0, 'intval');
        $goods_name = I('post.goods_name', '');
        $where = array();
        if (!empty($cat_id)) {
            $where['cat_ids'] = array('like', '%,' . $cat_id . ',%');
        }
        if (!empty($goods_name)) {
            $where['goods_name'] = array('like', '%' . $goods_name . '%');
        }
        $goodList = M('MainGoods')->where($where)->limit('0,20')->getField('goods_id,goods_name');
        $this->success($goodList);
    }                               public function verify() {            $key = I('get.key','');            if(in_array($key,array('login'))){                $Verify = new \Think\Verify(array('length' => 4,'useCurve'=>false,'fontSize' => 40,'imageH' => 80));                $Verify->entry($key);            }            exit();        }
}
