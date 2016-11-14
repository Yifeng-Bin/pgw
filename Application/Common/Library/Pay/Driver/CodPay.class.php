<?php

namespace Common\Library\Pay\Driver;

class CodPay {

    protected $gateway = '';
    protected $verify_url = '';
    
    public $payCode = 'cod';
    public $payName = '线下转账';
    public $payDesc = '线下转账';
    
    public $payConfig = array(
    );
    
    public $config = array(
    );
    
    public function getDefaultConfig(){
        return $this->payConfig;
    }     
    public function setConfig($config){
        return $this->config = array_merge($this->config,$config);
    }     
    
    public function buildRequestForm($data) {
        return "";
    }


    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
    public function verifyNotify($notify) {
            return true;
    }

    protected function setInfo($notify) {
        
    }
}
