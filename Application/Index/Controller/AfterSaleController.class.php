<?php

namespace Index\Controller;

class AfterSaleController extends CommonController {

    //提交工单
    public function submit() {
        if (!USER_ID) {
            $this->error("请登录后再进行提交", U('User/login'));
            exit();
        }
        if (IS_POST) {
            $act = I('post.act', '');
            if ($act == 'upload_attachment') {
                $upload = new \Think\Upload();
                $upload->maxSize   =     1024*1024 ;// 设置附件上传大小 1M
                $upload->exts = array('jpg', 'bmp', 'png'); // 设置附件上传类型
                $upload->rootPath = 'userfiles/'; // 设置附件上传根目录         
                $upload->savePath = USER_ID.'/aftersale/'; // 设置附件上传根目录         
                $info = $upload->uploadOne($_FILES['Filedata']);
                $path = $upload->rootPath . $info['savepath'] . $info['savename'];
                if (empty($info)) {
                    $this->error($upload->getError(), '', true);
                } else {
                    $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 2);                      
                    if($result !== false){
                        unlink($path);
                        $this->success($result);
                        exit();
                    }else{
                        $this->error('文件上传出现错误'); 
                        exit();
                    }
                }
            
            } else {
                $tender_id = I('post.id',0,'intval');
                $type = I('post.type',1,'intval');
                if(!in_array($type, array(1,99))){
                   $type =  1; 
                }
                $this->type = $type;
                $tenderInfo = M('Tender')->alias('t')->field('t.tender_id,t.status,t.finish_status,t.order_price')
                        ->where(array('t.user_id' => USER_ID,'t.tender_id' => $tender_id))->find();
                if(empty($tenderInfo)){
                    $this->error('需求不存在！');
                    exit();
                }
                if(in_array($tenderInfo['finish_status'],array(FINISH_STEP_FIFTY,FINISH_STEP_SIXTY,FINISH_STEP_SEVENTY,FINISH_PREFECT)) && $tenderInfo['status'] == STATUS_IS_FINISH){
                    $data = array(
                        'tender_id' => $tenderInfo['tender_id'],
                        'user_id' => USER_ID,
                        'type' => $type,
                        'content' => I('post.content','','strip_tags,htmlspecialchars'),
                        'status' => 1,
                        'add_time' => gmtime(),
                    );
                    
                    if($tenderInfo['finish_status'] != FINISH_PREFECT && $type == 1){
                        $data['refund_money'] = I('post.refund_money',0,'intval');
                        if($data['refund_money'] > $tenderInfo['order_price']){
                            $this->error('退款价格不能超过实际价格！');
                            exit();                            
                        }
                        $data['is_confirm'] = 0;
                    }
                    
                    $attachment_ids = I('post.attachment_id',array());
                    foreach($attachment_ids as $key => $url){
                        if((!preg_match('|^'.C('IMAGES_OSS_DOMAIN').'|',$url)) || !preg_match('|^http:\/\/[_a-zA-Z0-9\-\_\@\!]+(.[_a-zA-Z0-9-]+)*$|',$url)){
                            unset($attachment_ids[$key]);
                        }
                    }
                    $attachment_ids = array_slice($attachment_ids,0,3,false);
                    $i = 1;
                    foreach($attachment_ids as $url){
                        $data['attachment_0'.$i] = $url;
                        $i++;
                    }
                    $is_exist = M('TenderAfterSale')->where(array('tender_id' => $tenderInfo['tender_id'],'status' => 1))->count();
                    if(!empty($is_exist)){
                        $this->error('工单已经存在，请结束以后再提交！');
                        exit();
                    }else{
                        $result = M('TenderAfterSale')->add($data);
                        $this->success('工单已提交完成，等待回复处理！',U('AfterSale/info?id='.$tenderInfo['tender_id']));
                        exit();
                    }
                }else{
                    $this->error('不支持本操作！');
                    exit();
                }
            }
        } else {
            $this->pageHeadInfo = array(
                'title' => '提交工单-'.C('web_name'),
                'keywords' => C('web_keywords'),
                'description' => C('web_description'),
            );              
            $tender_id = I('get.id',0,'intval');
            $type = I('get.type',1,'intval');
            if(!in_array($type, array(1,99))){
               $type =  1; 
            }
            $this->type = $type;
            $tenderInfo = M('Tender')->alias('t')->field('t.tender_id,t.add_time,t.status,t.finish_status,t.order_price,t.tender_name,wt.type_name,u.user_name as bid_user_name,u.mobile as bid_mobile,u.real_name as bid_real_name')
                    ->join('left join __WORK_TYPE__ as wt on t.work_type_id = wt.type_id')
                    ->join('left join __USERS__ as u on u.user_id = t.bidder_id')
                    ->where(array('t.user_id' => USER_ID,'t.tender_id' => $tender_id))->find();
            if(empty($tenderInfo)){
                $this->error('需求不存在！');
                exit();                
            }
            $tenderInfo['add_time'] = local_date('Y-m-d H:i:s', $tenderInfo['add_time']);
            $tenderInfo['status_desc'] = L('TENDER_STATUS_'.$tenderInfo['status']).'('.L('TENDER_FINISH_STATUS_'.$tenderInfo['finish_status']).')';
            
            if(in_array($tenderInfo['finish_status'],array(FINISH_STEP_FIFTY,FINISH_STEP_SIXTY,FINISH_STEP_SEVENTY,FINISH_PREFECT)) && $tenderInfo['status'] == STATUS_IS_FINISH){
                if($tenderInfo['finish_status'] == FINISH_PREFECT && $type == 1){
                    $this->error('不支持本操作！');
                    exit();
                }
                
                
                $this->tenderInfo = $tenderInfo;
                $this->display();                
            }else{
                $this->error('不支持本操作！');
                exit();
            }
        }
    }
    
    public function info(){
        if (!USER_ID) {
            $this->error("请登录后再进行查看", U('User/login'));
            exit();
        }        
        $this->pageHeadInfo = array(
            'title' => '查看工单-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );               
        $tender_id = I('get.id',0,'intval');
        $tenderInfo = M('Tender')->alias('t')->field('t.user_id,t.bidder_id,t.tender_id,t.add_time,t.status,t.finish_status,t.order_price,t.tender_name,wt.type_name,u.user_name as bid_user_name,u.mobile as bid_mobile,u.real_name as bid_real_name')
                ->join('left join __WORK_TYPE__ as wt on t.work_type_id = wt.type_id')
                ->join('left join __USERS__ as u on u.user_id = t.bidder_id')
                ->where(array('t.tender_id' => $tender_id))->find();
        if(empty($tenderInfo)){
            $this->error('需求不存在！');
            exit();
        }
 
        if($tenderInfo['user_id'] != USER_ID && $tenderInfo['bidder_id'] != USER_ID ){
            $this->error('无权限！');
            exit();
        }
        $tenderInfo['add_time'] = local_date('Y-m-d H:i:s', $tenderInfo['add_time']);
        $tenderInfo['status_desc'] = L('TENDER_STATUS_'.$tenderInfo['status']).'('.L('TENDER_FINISH_STATUS_'.$tenderInfo['finish_status']).')';               
        $afterSaleInfo = M('TenderAfterSale')->field('s.*,u.user_name,u.avatar')->alias('s')->join('__USERS__ as u on s.user_id = u.user_id')
                ->order('s.id desc')->where(array('s.tender_id' => $tender_id))->find();
        //var_dump($afterSaleInfo );die();
        if(empty($afterSaleInfo)){
            $this->error('售后信息不存在！');
            exit();
        }
        $afterSaleInfo['add_time'] = local_date('Y-m-d H:i', $afterSaleInfo['add_time']);
        
        $afterSaleCommentList = M('TenderAfterSaleComment')->field('c.*,u.user_name,au.user_name as admin_user_name,u.avatar')->alias('c')
        ->join('left join __USERS__ as u on c.user_id = u.user_id')
        ->join('left join __ADMIN_USER__ as au on c.admin_id = au.uid')
        ->where(array('c.id' => $afterSaleInfo['id']))->select();
        //$afterSaleCommentList = M('TenderAfterSaleComment')->field('c.*,u.user_name,u.avatar')->alias('c')->join('__USERS__ as u on c.user_id = u.user_id')->where(array('c.id' => $afterSaleInfo['id']))->order('id desc')->select();
       // var_Dump(   $afterSaleCommentList);die();
        foreach($afterSaleCommentList as &$info){
            $info['add_time'] = local_date('Y-m-d H:i:s', $info['add_time']);
        }
        //var_dump($afterSaleCommentList);die();
        $this->tenderInfo = $tenderInfo;
        $this->afterSaleInfo = $afterSaleInfo;
        $this->afterSaleCommentList = $afterSaleCommentList;
        $this->display();
        
    }
    
    public function requestService(){
        $id = I('post.id',0,'intval');
        $afterSaleInfo = M('TenderAfterSale')->field('tender_id,need_service')->where(array('id'=> $id,'status' => 1))->find();

        if(empty($afterSaleInfo)){
            $this->error("不能进行本操作！");
            exit();
        }

        if($afterSaleInfo['need_service'] == 1){
            $this->error("已进行过本操作！");
            exit();
        }
        
        $tenderInfo = M('Tender')->field('user_id,bidder_id')->where(array('tender_id' => $afterSaleInfo['tender_id']))->find();
        if(USER_ID != $tenderInfo['user_id'] && USER_ID != $tenderInfo['bidder_id']){
            $this->error("不能进行本操作！");
            exit();                    
        }  
        M('TenderAfterSale')->where(array('id'=> $id,'status' => 1))->save(array('need_service' => 1));
        $this->success("生成成功，请等待客服处理！");
        exit();           
        
    }
    
    public function changeRefundPrice(){
        if (!USER_ID) {
            $this->error("请登录后再进行操作", U('User/login'));
            exit();
        }
        $id = I('post.id',0,'intval');
        $refund_money = I('post.refund_money',0,'intval');
        $afterSaleInfo = M('TenderAfterSale')->where(array('user_id' => USER_ID,'id'=> $id,'status' => 1,'is_confirm' => 0,'type' => 1))->find();

        if(empty($afterSaleInfo)){
            $this->error("不能进行本操作！");
            exit();
        }
        
        $tenderInfo = M('Tender')->field('pay_total')->where(array('tender_id' => $afterSaleInfo['tender_id']))->find();
        if($refund_money > $tenderInfo['pay_total']){
            $this->error("退款金额不能大于已付款金额！");
            exit();                    
        }
        $result = M('TenderAfterSale')->where(array('user_id' => USER_ID,'id'=> $id,'status' => 1,'is_confirm' => 0,'type' => 1))->save(array('refund_money' => $refund_money));
        if($result !== false){
            $this->success("价格修改成功！");
            exit();  
        }else{
            $this->error("不能修改价格！");
            exit();            
        }
    } 

    public function closeAfterSale(){
        if (!USER_ID) {
            $this->error("请登录后再进行操作", U('User/login'));
            exit();
        }
        $id = I('post.id',0,'intval');
        $afterSaleInfo = M('TenderAfterSale')->where(array('user_id' => USER_ID,'id'=> $id,'status' => 1))->find();

        if(empty($afterSaleInfo)){
            $this->error("不能进行本操作！");
            exit();
        }
        $result = M('TenderAfterSale')->where(array('user_id' => USER_ID,'id'=> $id,'status' => 1))->save(array('status' => 0));
        if($result !== false){
            $this->success("售后工单关闭成功！");
            exit();  
        }else{
            $this->error("请求失败！");
            exit();            
        }
    }    
    
    public function comment(){
        if (!USER_ID) {
            $this->error("请登录后再进行提交", U('User/login'));
            exit();
        }
        if (IS_POST) {
            $act = I('post.act', '');
            if ($act == 'upload_attachment') {
                $upload = new \Think\Upload();
                $upload->maxSize   =     1024*1024 ;// 设置附件上传大小 1M
                $upload->exts = array('jpg', 'bmp', 'png'); // 设置附件上传类型
                $upload->rootPath = 'userfiles/'; // 设置附件上传根目录         
                $upload->savePath = USER_ID.'/aftersale/'; // 设置附件上传根目录         
                $info = $upload->uploadOne($_FILES['Filedata']);
                $path = $upload->rootPath . $info['savepath'] . $info['savename'];
                if (empty($info)) {
                    $this->error($upload->getError(), '', true);
                } else {
                    $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 2);                      
                    if($result !== false){
                        unlink($path);
                        $this->success($result);
                        exit();
                    }else{
                        $this->error('文件上传出现错误'); 
                        exit();
                    }
                }
            
            } else {       
                $id = I('post.id',0,'intval');
                $afterSaleInfo = M('TenderAfterSale')->where(array('id' => $id))->find();
                if(empty($afterSaleInfo)){
                    $this->error('工单不存在！');
                    exit();
                }
                if($afterSaleInfo['status'] == 0){
                    $this->error('工单已关闭！');
                    exit();
                }                
                $tenderInfo = M('Tender')->alias('t')->field('t.tender_id,t.user_id,t.bidder_id')
                        ->where(array('t.tender_id' => $afterSaleInfo['tender_id']))->find();
                if(empty($tenderInfo)){
                    $this->error('需求不存在！');
                    exit();
                }
                if($tenderInfo['user_id'] != USER_ID && $tenderInfo['bidder_id'] != USER_ID ){
                    $this->error('不支持操作！');
                    exit();
                }
                
                $data = array(
                    'id' => $id,
                    'user_id' => USER_ID,
                    'content' => I('post.content','','strip_tags,htmlspecialchars'),
                    'add_time' => gmtime(),
                );
                
                $is_confirm = I('post.is_confirm',0,'intval');
                if($afterSaleInfo['is_confirm'] == 0 && $is_confirm == 1 && USER_ID == $tenderInfo['bidder_id']){
                    $data['is_confirm'] = 1;
                }else{
                    $data['is_confirm'] = 0;
                }
                if($afterSaleInfo['is_confirm'] == 1){
                    $data['is_confirm'] = 1;
                }
                
                $attachment_ids = I('post.attachment_id',array());
                foreach($attachment_ids as $key => $url){
                    if((!preg_match('|^'.C('IMAGES_OSS_DOMAIN').'|',$url)) || !preg_match('|^http:\/\/[_a-zA-Z0-9\-\_\@\!]+(.[_a-zA-Z0-9-]+)*$|',$url)){
                        unset($attachment_ids[$key]);
                    }
                }
                $attachment_ids = array_slice($attachment_ids,0,3,false);
                $i = 1;
                foreach($attachment_ids as $url){
                    $data['attachment_0'.$i] = $url;
                    $i++;
                }
                 
                $result = M('TenderAfterSaleComment')->add($data);
                
                if($data['is_confirm'] == 1){
                    M('TenderAfterSale')->where(array('id' => $id))->save(array('is_confirm' => 1));
                }
                $this->success('回复成功！',U('AfterSale/info?id='.$tenderInfo['tender_id']));
                exit();
            }
        }
    }     
    
    
}
