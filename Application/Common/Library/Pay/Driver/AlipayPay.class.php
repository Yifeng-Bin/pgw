<?php

namespace Common\Library\Pay\Driver;

class AlipayPay {

    protected $gateway = 'https://mapi.alipay.com/gateway.do';
    protected $verify_url = 'http://notify.alipay.com/trade/notify_query.do';
    protected $info;
    public $payCode = 'alipay';
    public $payName = '支付宝';
    public $payDesc = '支付宝支付';
    public $payConfig = array(
        'email' => array(
            'type' => 'input',
            'desc' => '邮箱',
            'value' => '',
        ),
        'key' => array(
            'type' => 'input',
            'desc' => 'key',
            'value' => '',
        ),
        'partner' => array(
            'type' => 'input',
            'desc' => 'partner',
            'value' => '',
        ),
    );
    public $config = array(
        'email' => '',
        'key' => '',
        'partner' => ''
    );

    public function getDefaultConfig() {
        return $this->payConfig;
    }

    public function setConfig($config) {
        $this->config = array_merge($this->config, $config);
    }

    public function buildRequestForm($data) {
        $param = array(
            'service' => 'create_direct_pay_by_user',
            'partner' => $this->config['partner'],
            '_input_charset' => 'utf-8',
            'notify_url' => $this->config['notify_url'],
            'return_url' => $this->config['return_url'],
            'out_trade_no' => $data['order_sn'],
            'subject' => $data['subject'],
            'payment_type' => '1',
            'total_fee' => $data['order_amount'],
            'seller_email' => $this->config['email'],
                //'seller_id' => $this->config['partner'],
        );
        if(WEB_TYPE == 'm'){
            $param['service'] = 'alipay.wap.create.direct.pay.by.user';
            unset($param['seller_email']);
            $param['seller_id'] = $this->config['partner'];
        }        
        ksort($param);
        reset($param);
        $sign = '';
        $str = '';
        foreach ($param as $key => $val) {
            $sign.=$key . '=' . $val . '&';
        }
        $param['sign'] = md5(substr($sign, 0, -1) . $this->config['key']);
        $param['sign_type'] = 'MD5';
        $request_str = http_build_query($param);
        $url = $this->gateway . '?' . $request_str;
        if(isset($data['direct_buy']) && $data['direct_buy'] == 1){
            return $url;
        }else{
            $button = '<input type="button" onclick="window.open(\'' . $url . '\')" value="立 即 去 支 付 宝 支 付" />';
            return $button;            
        }
    }
    
    public function notifySuccess() {
        echo "success";
    }
    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
    protected function getSignVeryfy($param, $sign) {
        //除去待签名参数数组中的空值和签名参数
        $param_filter = array();
        while (list ($key, $val) = each($param)) {
            if ($key == "sign" || $key == "sign_type" || $val == "") {
                continue;
            } else {
                $param_filter[$key] = $param[$key];
            }
        }

        ksort($param_filter);
        reset($param_filter);

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = "";
        while (list ($key, $val) = each($param_filter)) {
            $prestr.=$key . "=" . $val . "&";
        }
        //去掉最后一个&字符
        $prestr = substr($prestr, 0, -1);

        $prestr = $prestr . $this->config['key'];
        $mysgin = md5($prestr);

        if ($mysgin == $sign) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
    public function verifyNotify($notify) {

        //生成签名结果
        $isSign = $this->getSignVeryfy($notify, $notify["sign"]);
        //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
        $responseTxt = 'true';
        if (!empty($notify["notify_id"])) {
            $responseTxt = $this->getResponse($notify["notify_id"]);
        }

        if (preg_match("/true$/i", $responseTxt) && $isSign) {
            $this->setInfo($notify);
            return true;
        } else {
            return false;
        }
    }

    protected function setInfo($notify) {
        $info = array();
        //支付状态
        $info['status'] = ($notify['trade_status'] == 'TRADE_FINISHED' || $notify['trade_status'] == 'TRADE_SUCCESS') ? true : false;
        $info['money'] = $notify['total_fee'];
        $info['out_trade_no'] = $notify['out_trade_no'];
        $this->info = $info;
    }

    public function getInfo() {
        return $this->info;
    }

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空 
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
    protected function getResponse($notify_id) {
        $partner = $this->config['partner'];
        $veryfy_url = $this->verify_url . "?partner=" . $partner . "&notify_id=" . $notify_id;
        $responseTxt = $this->fsockOpen($veryfy_url);
        return $responseTxt;
    }

    protected function fsockOpen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE, $encodetype = 'URLENCODE', $allowcurl = TRUE, $position = 0, $files = array()) {
        $return = '';
        $matches = parse_url($url);
        $scheme = $matches['scheme'];
        $host = $matches['host'];
        $path = $matches['path'] ? $matches['path'] . ($matches['query'] ? '?' . $matches['query'] : '') : '/';
        $port = !empty($matches['port']) ? $matches['port'] : ($scheme == 'http' ? '80' : '');
        $boundary = $encodetype == 'URLENCODE' ? '' : random(40);

        if ($post) {
            if (!is_array($post)) {
                parse_str($post, $post);
            }
            $this->formatPostkey($post, $postnew);
            $post = $postnew;
        }
        if (function_exists('curl_init') && function_exists('curl_exec') && $allowcurl) {
            $ch = curl_init();
            $httpheader = array();
            if ($ip) {
                $httpheader[] = "Host: " . $host;
            }
            if ($httpheader) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
            }
            curl_setopt($ch, CURLOPT_URL, $scheme . '://' . ($ip ? $ip : $host) . ($port ? ':' . $port : '') . $path);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            if ($post) {
                curl_setopt($ch, CURLOPT_POST, 1);
                if ($encodetype == 'URLENCODE') {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                } else {
                    foreach ($post as $k => $v) {
                        if (isset($files[$k])) {
                            $post[$k] = '@' . $files[$k];
                        }
                    }
                    foreach ($files as $k => $file) {
                        if (!isset($post[$k]) && file_exists($file)) {
                            $post[$k] = '@' . $file;
                        }
                    }
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                }
            }
            if ($cookie) {
                curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            }
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            $data = curl_exec($ch);
            $status = curl_getinfo($ch);
            $errno = curl_errno($ch);
            curl_close($ch);
            if ($errno || $status['http_code'] != 200) {
                return;
            } else {
                $GLOBALS['filesockheader'] = substr($data, 0, $status['header_size']);
                $data = substr($data, $status['header_size']);
                return !$limit ? $data : substr($data, 0, $limit);
            }
        }

        if ($post) {
            if ($encodetype == 'URLENCODE') {
                $data = http_build_query($post);
            } else {
                $data = '';
                foreach ($post as $k => $v) {
                    $data .= "--$boundary\r\n";
                    $data .= 'Content-Disposition: form-data; name="' . $k . '"' . (isset($files[$k]) ? '; filename="' . basename($files[$k]) . '"; Content-Type: application/octet-stream' : '') . "\r\n\r\n";
                    $data .= $v . "\r\n";
                }
                foreach ($files as $k => $file) {
                    if (!isset($post[$k]) && file_exists($file)) {
                        if ($fp = @fopen($file, 'r')) {
                            $v = fread($fp, filesize($file));
                            fclose($fp);
                            $data .= "--$boundary\r\n";
                            $data .= 'Content-Disposition: form-data; name="' . $k . '"; filename="' . basename($file) . '"; Content-Type: application/octet-stream' . "\r\n\r\n";
                            $data .= $v . "\r\n";
                        }
                    }
                }
                $data .= "--$boundary\r\n";
            }
            $out = "POST $path HTTP/1.0\r\n";
            $header = "Accept: */*\r\n";
            $header .= "Accept-Language: zh-cn\r\n";
            $header .= $encodetype == 'URLENCODE' ? "Content-Type: application/x-www-form-urlencoded\r\n" : "Content-Type: multipart/form-data; boundary=$boundary\r\n";
            $header .= 'Content-Length: ' . strlen($data) . "\r\n";
            $header .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
            $header .= "Host: $host:$port\r\n";
            $header .= "Connection: Close\r\n";
            $header .= "Cache-Control: no-cache\r\n";
            $header .= "Cookie: $cookie\r\n\r\n";
            $out .= $header;
            $out .= $data;
        } else {
            $out = "GET $path HTTP/1.0\r\n";
            $header = "Accept: */*\r\n";
            $header .= "Accept-Language: zh-cn\r\n";
            $header .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
            $header .= "Host: $host:$port\r\n";
            $header .= "Connection: Close\r\n";
            $header .= "Cookie: $cookie\r\n\r\n";
            $out .= $header;
        }

        $fpflag = 0;
        if (!$fp = @fsocketopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout)) {
            $context = array(
                'http' => array(
                    'method' => $post ? 'POST' : 'GET',
                    'header' => $header,
                    'content' => $post,
                    'timeout' => $timeout,
                ),
            );
            $context = stream_context_create($context);
            $fp = @fopen($scheme . '://' . ($ip ? $ip : $host) . ':' . $port . $path, 'b', false, $context);
            $fpflag = 1;
        }

        if (!$fp) {
            return '';
        } else {
            stream_set_blocking($fp, $block);
            stream_set_timeout($fp, $timeout);
            @fwrite($fp, $out);
            $status = stream_get_meta_data($fp);
            if (!$status['timed_out']) {
                while (!feof($fp) && !$fpflag) {
                    $header = @fgets($fp);
                    $headers .= $header;
                    if ($header && ($header == "\r\n" || $header == "\n")) {
                        break;
                    }
                }
                $GLOBALS['filesockheader'] = $headers;

                if ($position) {
                    for ($i = 0; $i < $position; $i++) {
                        $char = fgetc($fp);
                        if ($char == "\n" && $oldchar != "\r") {
                            $i++;
                        }
                        $oldchar = $char;
                    }
                }

                if ($limit) {
                    $return = stream_get_contents($fp, $limit);
                } else {
                    $return = stream_get_contents($fp);
                }
            }
            @fclose($fp);
            return $return;
        }
    }

    protected function formatPostkey($post, &$result, $key = '') {
        foreach ($post as $k => $v) {
            $_k = $key ? $key . '[' . $k . ']' : $k;
            if (is_array($v)) {
                $this->formatPostkey($v, $result, $_k);
            } else {
                $result[$_k] = $v;
            }
        }
    }

}
