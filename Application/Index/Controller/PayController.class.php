<?php
namespace Index\Controller;
class PayController extends CommonController {

    public function submit() {
        if (!USER_ID) {
            $this->error("请登录后再进行查询", U("User/login"));
        }
        
        $type = I('post.type','');
        if(!in_array($type, array('margin','gold','requirement'))){
            $type = 'margin';
        }
        if($type == 'margin'){
            $money_type = I('post.money_type',1,'intval');
            $pay_id = I('post.pay_id',1,'intval');
            if(!in_array($money_type, array(1,2))){
                $money_type = 1;
            }            
            if($money_type == 1){
                $money = 1000;
            }else{
                $money = I('post.money',100,'intval');
                if($money < 1){
                    $money = 1;
                }                 
            }
            $payInfo = M('Payment')->field('pay_code')->where(array('pay_id' => $pay_id,'enabled' => 1,'is_online' => 1))->find();
            if(empty($payInfo)){
                $this->error('支付方式不存在');
                exit();
            }
            $data = array(
                'out_id' => 0,
                'user_id' => USER_ID,
                'log_type' => 1,
                'money' => $money,
                'log_ip' => get_client_ip(),
                'pay_status' => 0,
                'pay_id' => $pay_id,
                'log_time' => gmtime(),
                'log_remark' => '',
            );
            $id = M('PayLog')->add($data);
            $payObj = new \Common\Library\Pay\Pay();
            $pay_code = $payInfo['pay_code'];
            $pay = $payObj->getDriver($pay_code);
            if ($pay->config['is_online']) {
                $url = $pay->buildRequestForm(array('order_amount' => $money,'subject' => '支付保证金' , 'order_sn' => $id,'direct_buy' => 1));
                \redirect($url);
            } else {
                $orderInfo['pay_button'] = '';
                \redirect("User/order");
            }            
        }
        
        elseif($type == 'gold'){
            $god_num = I('post.god_num',10,'intval');
            $pay_id = I('post.pay_id',1,'intval');
            $money =  intval(floor($god_num/10));
            if($money < 1){
                $money = 1;
            }
            $payInfo = M('Payment')->field('pay_code')->where(array('pay_id' => $pay_id,'enabled' => 1,'is_online' => 1))->find();
            if(empty($payInfo)){
                $this->error('支付方式不存在');
                exit();
            }
            $data = array(
                'out_id' => 0,
                'user_id' => USER_ID,
                'log_type' => 2,
                'money' => $money,
                'log_ip' => get_client_ip(),
                'pay_status' => 0,
                'pay_id' => $pay_id,
                'log_time' => gmtime(),
                'log_remark' => '',
            );
            $id = M('PayLog')->add($data);
            $payObj = new \Common\Library\Pay\Pay();
            $pay_code = $payInfo['pay_code'];
            $pay = $payObj->getDriver($pay_code);
            if ($pay->config['is_online']) {
                $url = $pay->buildRequestForm(array('order_amount' => $money,'subject' => '购买金币' , 'order_sn' => $id,'direct_buy' => 1));
                \redirect($url);
            } else {
                $orderInfo['pay_button'] = '';
            }
        }
        
        elseif($type == 'requirement'){
            
            $tender_id = I('post.tender_id',0,'intval');
            $pay_id = I('post.pay_id',1,'intval');
            $tenderInfo = M('Tender')->field('tender_name,tender_id,pay_part1,pay_part2,pay_part3,pay_part4,pay_total,order_price')->where(array('tender_id' => $tender_id, 'user_id' => USER_ID))->find();
            $payInfo = M('Payment')->field('pay_code')->where(array('pay_id' => $pay_id,'enabled' => 1))->find();
            if(empty($payInfo)){
                $this->error('支付方式不存在');
                exit();
            }
            $tenderInfo['pay_part1'] = intval($tenderInfo['pay_part1']);
            $tenderInfo['pay_part2'] = intval($tenderInfo['pay_part2']);
            $tenderInfo['pay_part3'] = intval($tenderInfo['pay_part3']);
            $tenderInfo['pay_part4'] = intval($tenderInfo['pay_part4']);
            $tenderInfo['pay_total'] = intval($tenderInfo['pay_total']);
            $tenderInfo['order_price'] = intval($tenderInfo['order_price']);
            
            if($tenderInfo['order_price'] == $tenderInfo['pay_total']){
                $this->error('已完成所有支付无需进行进行本操作');
                exit();                
            }            
            if($tenderInfo['pay_part1'] == 0){
                $this->error('未设置金额');
                exit();                
            }
            M('Tender')->where(array('tender_id' => $tender_id, 'user_id' => USER_ID,'status' => STATUS_IS_CONFIRM))->save(array('status' => STATUS_WAIT_PAY));
            if($tenderInfo['pay_total'] == 0){
                $id = M('PayLog')->where(array('user_id' => USER_ID,'log_type' => 3,'out_id' => $tender_id ,'stage' => 1))->getField("log_id");
                if(empty($id)){
                    $data = array(
                        'out_id' => $tender_id,
                        'stage' => 1,
                        'money' => $tenderInfo['pay_part1'],
                        'user_id' => USER_ID,
                        'log_type' => 3,
                        'log_ip' => get_client_ip(),
                        'pay_status' => 0,
                        'pay_id' => $pay_id,
                        'log_time' => gmtime(),
                        'log_remark' => '',
                    );
                    $id = M('PayLog')->add($data);                    
                }
                $money = $tenderInfo['pay_part1'];
            }elseif($tenderInfo['pay_total'] < ($tenderInfo['pay_part1'] + $tenderInfo['pay_part2'])){
                $id = M('PayLog')->where(array('user_id' => USER_ID,'log_type' => 3,'out_id' => $tender_id ,'stage' => 2))->getField("log_id");
                if(empty($id)){
                    $data = array(
                        'out_id' => $tender_id,
                        'stage' => 2,
                        'money' => $tenderInfo['pay_part2'],
                        'user_id' => USER_ID,
                        'log_type' => 3,
                        'log_ip' => get_client_ip(),
                        'pay_status' => 0,
                        'pay_id' => $pay_id,
                        'log_time' => gmtime(),
                        'log_remark' => '',
                    );
                    $id = M('PayLog')->add($data);                    
                }
                $money = $tenderInfo['pay_part2'];                
            }elseif($tenderInfo['pay_total'] < ($tenderInfo['pay_part1'] + $tenderInfo['pay_part2'] + $tenderInfo['pay_part3'])){
                $id = M('PayLog')->where(array('user_id' => USER_ID,'log_type' => 3,'out_id' => $tender_id ,'stage' => 3))->getField("log_id");
                if(empty($id)){
                    $data = array(
                        'out_id' => $tender_id,
                        'stage' => 3,
                        'money' => $tenderInfo['pay_part3'],
                        'user_id' => USER_ID,
                        'log_type' => 3,
                        'log_ip' => get_client_ip(),
                        'pay_status' => 0,
                        'pay_id' => $pay_id,
                        'log_time' => gmtime(),
                        'log_remark' => '',
                    );
                    $id = M('PayLog')->add($data);                    
                }  
                $money = $tenderInfo['pay_part3'];
            }elseif($tenderInfo['pay_total'] < ($tenderInfo['pay_part1'] + $tenderInfo['pay_part2'] + $tenderInfo['pay_part3'] + $tenderInfo['pay_part4'])){
                $id = M('PayLog')->where(array('user_id' => USER_ID,'log_type' => 3,'out_id' => $tender_id ,'stage' => 4))->getField("log_id");
                if(empty($id)){
                    $data = array(
                        'out_id' => $tender_id,
                        'stage' => 4,
                        'money' => $tenderInfo['pay_part4'],
                        'user_id' => USER_ID,
                        'log_type' => 3,
                        'log_ip' => get_client_ip(),
                        'pay_status' => 0,
                        'pay_id' => $pay_id,
                        'log_time' => gmtime(),
                        'log_remark' => '',
                    );
                    $id = M('PayLog')->add($data);                    
                }
                $money = $tenderInfo['pay_part4'];                  
            }else{
                $this->error("支付错误");
                exit();
            }    

            $payObj = new \Common\Library\Pay\Pay();
            $pay_code = $payInfo['pay_code'];
            $pay = $payObj->getDriver($pay_code);
            if ($pay->config['is_online']) {
                $url = $pay->buildRequestForm(array('order_amount' => $money,'subject' => '支付需求:'.$tenderInfo['tender_name'] , 'order_sn' => $id,'direct_buy' => 1));
                \redirect($url);
            } else {
                $orderInfo['pay_button'] = '';
            }            
        }        
        
        $this->display();
    }
}
