<?php
namespace Common\Library\Pay;
class Pay {

    /**
     * 支付驱动实例
     * @var Object
     */
    public $driver;
    /**
     * 构造方法，用于构造上传实例
     * @param string $driver 要使用的支付驱动
     * @param array  $config 配置
     */
    
    public function getDriver($driver){
        if(!isset($this->driver[$driver])){
            if($this->payIsExist($driver)){
                $class = 'Common\\Library\\Pay\\Driver\\'.ucfirst($driver).'Pay';
                $this->driver[$driver] =  new $class();
                $config = M('Payment')->where(array('pay_code'=>$driver))->getField('pay_config');
                $config = unserialize($config);       
                $config['notify_url'] = U("Payment/Notify", array('type' => $driver, 'method' => 'notify'),true,true);
                $config['return_url'] = U("Payment/Notify", array('type' => $driver, 'method' => 'return'),true,true);  
                $this->driver[$driver]->setConfig($config);
            }else{
                return false;
            }
        } 
        return $this->driver[$driver];
    }       
    
    public function getAllPayList(){
        $payList = array();
        $dir = dirname(__FILE__). DIRECTORY_SEPARATOR . "Driver";
        $dirHandle = opendir($dir);
        $i = 0;
        $tmpPayObj = '';
        while (false !== ($fileName = readdir($dirHandle))) {
            $subFile = $dir . DIRECTORY_SEPARATOR . $fileName;
            if (is_file($subFile)) {
                $class = 'Common\\Library\\Pay\\Driver\\'.ucfirst(strstr($fileName,'.',true));
                $tmpPayObj =  new $class();  
                $payList[$i]['pay_code'] = $tmpPayObj->payCode;
                $payList[$i]['pay_name'] = $tmpPayObj->payName;
                $payList[$i]['pay_desc'] = $tmpPayObj->payDesc;
            }
            $i++ ;
        }
        unset($tmpPayObj);
        closedir($dirHandle);        
        return $payList;
    }    

    public function payIsExist($pay_code){
        $subFile = dirname(__FILE__). DIRECTORY_SEPARATOR . "Driver".DIRECTORY_SEPARATOR. ucfirst($pay_code).'Pay.class.php';
        if (is_file($subFile)) {
            return true;
        }
        return false;
    }
    
    public function __call($method, $arguments) {
        if (method_exists($this, $method)) {
            return call_user_func_array(array(&$this, $method), $arguments);
        } elseif (!empty($this->driver) && method_exists($this->driver, $method)) {
            return call_user_func_array(array(&$this->driver, $method), $arguments);
        }
    }      
}
