<?php
namespace Admin\Controller;
class PaymentController extends CommonController {

    public function index() {
        $payList = M('Payment')->field('pay_id,pay_code,pay_name,enabled,pay_order')->select();
        $payInfoArr = array();
        $needInstallPayList = array();
        foreach($payList as $payInfo){
            $payInfoArr[] = $payInfo['pay_code'];
        }
        $this->payList = $payList;
        $payObj = new \Common\Library\Pay\Pay();
        
        $allPayList = $payObj->getAllPayList();
        foreach($allPayList as $payInfo){
            if(!in_array($payInfo['pay_code'], $payInfoArr)){
                $needInstallPayList[] = $payInfo;
            }
        }
        $this->needInstallPayList = $needInstallPayList;
        $this->display();
    }

    public function edit() {
        if(IS_POST){
            $payInfo = array();
            $payId = I('post.pay_id',0,'intval');
            $where = array(
                'pay_id' => $payId,
            );
            $payInfo = M('Payment')->where($where)->find();
            if(empty($payInfo)){
                $this->error("支付方式数据不存在！", U("Payment/index"));
            }   
            $payObj = new \Common\Library\Pay\Pay();
            $isExist = $payObj->payIsExist($payInfo['pay_code']);
            if(empty($isExist)){
                $this->error("支付方式文件不存在！", U("Payment/index"));
            }
            $pay = $payObj->getDriver($payInfo['pay_code']);
            $defaultConfig = $pay->getDefaultConfig();
            
            $info = array(
                'pay_name' => I('post.pay_name',$payInfo['pay_name'],  'htmlspecialchars'),  
                'pay_desc' => I('post.pay_desc',$payInfo['pay_desc'],  'htmlspecialchars'),  
                'enabled' => I('post.enabled',0,  'intval'), 
                'is_online' => I('post.is_online',0,  'intval'), 
                'pay_order' => I('post.pay_order',0,  'intval'),
            );
            $info['pay_config'] = array('is_online' => I('post.is_online',0,  'intval'), );
            foreach($defaultConfig as $k => $item){
                $info['pay_config'][$k] = I('post.'.$k,$item['value'],'htmlspecialchars');
            }            
            $info['pay_config'] = serialize($info['pay_config']);
            M('Payment')->data($info)->where(array('pay_id'=>$payId))->save();
            $info['pay_id'] = $payId;
            admin_log("修改支付方式",json_encode($info));             
            $this->error("支付方式修改成功！", U("Payment/index"));        
        }else{
            $where = array();
            $payId = I('get.pay_id',0,'intval');
            $where = array(
                'pay_id' => $payId,
            );
            $payInfo = M('Payment')->where($where)->find();
            $payInfo['pay_config'] = unserialize($payInfo['pay_config']);           

            $payObj = new \Common\Library\Pay\Pay();
            $pay = $payObj->getDriver($payInfo['pay_code']);
            $defaultConfig = $pay->getDefaultConfig();

            foreach($defaultConfig as $k => &$item){
                if(isset($payInfo['pay_config'][$k])){
                    $item['value'] = $payInfo['pay_config'][$k];
                }
            }         
            $this->payInfo = $payInfo;
            $this->defaultConfig = $defaultConfig;
            $this->display();
        }
    }

    public function install() {
        if(IS_POST){
            $payInfo = array();
            $payCode = I('post.pay_code','','htmlspecialchars');
            $where = array(
                'pay_code' => $payCode,
            );
            $payInfo = M('Payment')->where($where)->find();
            if(!empty($payInfo)){
                $this->error("支付方式已存在！", U("Payment/index"));
            }   
            $payObj = new \Common\Library\Pay\Pay();
            $isExist = $payObj->payIsExist($payCode);
            if(empty($isExist)){
                $this->error("支付方式文件不存在！", U("Payment/index"));
            }
            $pay = $payObj->getDriver($payCode);
            $defaultConfig = $pay->getDefaultConfig();
            
            $info = array(
                'pay_code' => $payCode,   
                'pay_name' => I('post.pay_name',$payInfo['pay_name'],  'htmlspecialchars'),  
                'pay_desc' => I('post.pay_desc',$payInfo['pay_desc'],  'htmlspecialchars'),  
                'enabled' => I('post.enabled',0,  'intval'), 
                'is_online' => I('post.is_online',0,  'intval'), 
                'pay_order' => I('post.pay_order',0,  'intval'),                
            );
            $info['pay_config'] = array('is_online' => I('post.is_online',0,  'intval'), );
            foreach($defaultConfig as $k => $item){
                $info['pay_config'][$k] = I('post.'.$k,$item['value'],'htmlspecialchars');
            }
            $info['pay_config'] = serialize($info['pay_config']);
            $pay_id = M('Payment')->data($info)->add();
            $info['pay_id'] = $pay_id;
            admin_log("安装支付方式",json_encode($info));                
            $this->success("支付方式安装成功！", U("Payment/index"));        
        }else{
            $payCode = I('get.pay_code','','htmlspecialchars');
            $payObj = new \Common\Library\Pay\Pay($payCode);
            $pay = $payObj->getDriver($payCode);
            $defaultConfig = $pay->getDefaultConfig();
            
            $payInfo = array(
                'pay_id' => 0,
                'pay_code' => $payCode,
                'pay_name' => $pay->payName,
                'pay_desc' => $pay->payDesc,
                'enabled' => '1',
                'is_online' => '1',
                'pay_order' => '1',
            );        
            $this->payInfo = $payInfo;
            $this->defaultConfig = $defaultConfig;
            $this->display();
        }
    }

    public function del() {
        //防止误操作  暂时不提供删除功能
    }

}
