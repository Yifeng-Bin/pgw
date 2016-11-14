<?php

//一些时间函数
//当前服务器gmt时间戳
function gmtime() {
    return time() - date('Z');
}

//根据gmt时间获取格式化的当前时间
function local_date($format, $time = NULL, $timezone = NULL) {
    if ($timezone === NULL) {
        $timezone = date('Z') / 3600;
    }
    if ($time === NULL) {
        $time = gmtime();
    } elseif ($time <= 0) {
        return '';
    }
    $time += ($timezone * 3600);
    return date($format, $time);
}

function gmstr2time($str) {
    $time = strtotime($str);

    if ($time > 0) {
        $time -= date('Z');
    }

    return $time;
}

//用户输入过滤
function clean_xss($val) {
    Vendor('htmlpurifier-standalone/HTMLPurifier#standalone');
    $config = \HTMLPurifier_Config::createDefault();
    $config->set('Core.LexerImpl', 'DirectLex');
    $config->set('HTML.Allowed', 'table[border|width|style],tbody,tr,td[style|bgcolor|align|height],th,img[style|src|alt],span[style],p[style],ul,ol,li,strong,em,sup,sub,br');
    $purifier = new \HTMLPurifier($config);
    $goods_desc = $purifier->purify($val);
    //$goods_desc = htmlspecialchars_decode($goods_desc, ENT_NOQUOTES);
    return $goods_desc;
}

//明文加密
function encode_password($word) {
    import('Common/PasswordHash', COMMON_PATH, '.php');
    $pHash = new \PasswordHash(8, false);
    return $pHash->HashPassword($word);
}

//核对密码
function check_password($password, $encode_password) {
    import('Common/PasswordHash', COMMON_PATH, '.php');
    $pHash = new \PasswordHash(8, false);
    return $pHash->CheckPassword($password, $encode_password);
}

function send_sms($mobile, $type) { //$type (1注册)
    $error_code = array(
        'isv.OUT_OF_SERVICE' => '业务停机',
        'isv.PRODUCT_UNSUBSCRIBE' => '产品服务未开通',
        'isv.ACCOUNT_NOT_EXISTS' => '账户信息不存在',
        'isv.ACCOUNT_ABNORMAL' => '账户信息异常',
        'isv.SMS_TEMPLATE_ILLEGAL' => '模板不合法',
        'isv.SMS_SIGNATURE_ILLEGAL' => '签名不合法',
        'isv.MOBILE_NUMBER_ILLEGAL' => '手机号码格式错误',
        'isv.MOBILE_COUNT_OVER_LIMIT' => '手机号码数量超过限制',
        'isv.TEMPLATE_MISSING_PARAMETERS' => '短信模板变量缺少参数',
        'isv.INVALID_PARAMETERS' => '参数异常',
        'isv.BUSINESS_LIMIT_CONTROL' => '触发业务流控限制',
        'isv.INVALID_JSON_PARAM' => 'JSON参数不合法',
    );

    $is_mobile = \Com\Logdd\Validator::isMobile($mobile);
    if (!$is_mobile) {//手机号码错误
        return '手机号码不可用';
    }
    //手机验证次数 每个ip每天的限制
    $gmtime = gmtime();

    $ip = get_client_ip(0, true);
    $count = M('SmsVerify')->where(
                    array('client_ip' => $ip, 'time' => array('egt', $gmtime - 86400))//每个ip每天的限制
            )->count();
    if ($count >= 50) {
        return '客户端当前ip限制，请联系管理员';
    }

    //阿里大鱼接口
    if ($type == 1) {  //用户注册
        $count = M('SmsVerify')->where(
                        array('status' => 1, 'type' => 1, 'mobile' => $mobile, 'time' => array('gt', gmtime() - 60))//60秒内只能获取一次
                )->count();
        if ($count) {
            return "60s内只能获取一次验证码";
        }
        $templete_code = "SMS_9560093";
        $sign = "派工网";
    } elseif ($type == 2) { //找回密码
        $count = M('SmsVerify')->where(
                        array('status' => 1, 'type' => 2, 'mobile' => $mobile, 'time' => array('gt', gmtime() - 60))//60秒内只能获取一次
                )->count();
        if ($count) {
            return "60s内只能获取一次验证码";
        }
        $templete_code = "SMS_9560091";
        $sign = "派工网";
    } elseif ($type == 3) { //修改手机号码
        $count = M('SmsVerify')->where(
                        array('status' => 1, 'type' => 3, 'mobile' => $mobile, 'time' => array('gt', gmtime() - 60))//60秒内只能获取一次
                )->count();
        if ($count) {
            return "60s内只能获取一次验证码";
        }
        $templete_code = "SMS_9560090";
        $sign = "派工网";
    } else {
        return "未知类型错误"; //未知类型
    }

    $client = new Com\Logdd\Alidayu\AlidayuClient;
    $request = new Com\Logdd\Alidayu\Request\SmsNumSend;
    // 短信内容参数
    $code = \Org\Util\String::randString(6, 1);
    $smsParams = array(
        'code' => $code,
        'product' => '派工网'
    );
    // 设置请求参数
    $req = $request->setSmsTemplateCode($templete_code)
            ->setRecNum($mobile)
            ->setSmsParam(json_encode($smsParams))
            ->setSmsFreeSignName($sign)
            ->setSmsType('normal');
    $result = $client->execute($req);
    //阿里大鱼接口
    if (isset($result['error_response'])) {
        return $error_code[$result['error_response']['sub_code']];
    } else {
        M('SmsVerify')->data(
                array('mobile' => $mobile, 'client_ip' => get_client_ip(), 'code' => $code, 'time' => gmtime(), 'type' => $type, 'status' => 1)
        )->add();
        return true;
    }
}

function get_last_sms($mobile, $type) {
    $where = array(
        'type' => $type,
        'mobile' => $mobile,
        'type' => $type,
        'status' => 1,
        'time' => array('gt', gmtime() - 86400),
    );
    $smsInfo = M('SmsVerify')->where($where)->order('id desc')->find();
    return $smsInfo;
}

function get_xss_code($length = 10) {
    return substr(md5('asdfasdfasdfaqwerf' . time()), 0, $length);
}

function price_format($price, $change_price = true) {
    if ($price === '') {
        $price = 0;
    }
    $price = number_format($price, 2, '.', '');
    if ($change_price) {
        return sprintf('￥%s', $price);
    } else {
        return $price;
    }
}

function get_page_url() {
    $url = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
    $url .= $_SERVER['HTTP_HOST'];
    $url .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : urlencode($_SERVER['PHP_SELF']) . '?' . urlencode($_SERVER['QUERY_STRING']);
    return $url;
}

//将用户名进行处理，中间用星号表示  
function substr_cut($user_name) {
    //获取字符串长度  
    $strlen = mb_strlen($user_name, 'utf-8');
    //如果字符创长度小于2，不做任何处理  
    if ($strlen < 2) {
        return $user_name;
    } else {
        if ($strlen > 6) {
            $retain_num = 2;
        } else {
            $retain_num = 1;
        }
        //mb_substr — 获取字符串的部分  
        $firstStr = mb_substr($user_name, 0, $retain_num, 'utf-8');
        $lastStr = mb_substr($user_name, -1 * $retain_num, $retain_num, 'utf-8');
        //str_repeat — 重复一个字符串  
        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - $retain_num * 2) . $lastStr;
    }
}

//onethink 里面拿来用的
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/*字符串截断函数+省略号*/
function subtext($text, $length)
{
    if(mb_strlen($text, 'utf8') > $length)
        return mb_substr($text, 0, $length, 'utf8').'.....';
        return $text;
}
