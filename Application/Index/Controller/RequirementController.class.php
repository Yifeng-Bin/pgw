<?php

namespace Index\Controller;

class RequirementController extends CommonController {

    //需求大厅
    public function index() {
        $this->pageHeadInfo = array(
            'title' => '需求大厅-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );        
        
        $where = array(
            't.is_checked' => 1,
            't.is_delete' => 0,
            't.status' => array('neq',0),
            't.region_id' => REGION_ID,
            't.tender_type' => 1,
			't.is_activity' => 0,
        );
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
        
        $requirementList =M('Tender')->alias('t')->field('t.tender_id,t.contacts,t.tender_name,wt.type_name,t.add_time,t.closing_time,t.bidder_num')
                ->join('left join __WORK_TYPE__ as wt on t.work_type_id = wt.type_id')
                ->where($where)->order('add_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($requirementList as &$list){
            $list['contacts'] = substr_cut($list['contacts']);
        }
        $this->requirementList = $requirementList;              
        $this->display();
    }

    //提交需求
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
                $upload->savePath = USER_ID.'/requirement/'; // 设置附件上传根目录         
                $info = $upload->uploadOne($_FILES['Filedata']);
                $path = $upload->rootPath . $info['savepath'] . $info['savename'];
                if (empty($info)) {
                    $this->error($upload->getError(), '', true);
                } else {
                    $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 2);                      
                    if($result !== false){
                        $url = $result['url'];
                        $arr = array(
                            'user_id' => USER_ID,
                            'url' => $url,
                            'path' => $path,
                        );
                        $attachment_id = M('TenderAttachment')->add($arr);
                        if($attachment_id){
                            $result['attachment_id'] = $attachment_id;
                            unlink($path);
                            $this->success($result); 
                        }else{
                            $this->error('服务器处理错误，请重试'); 
                            exit();                        
                        }
                    }else{
                        $this->error('文件上传出现错误'); 
                        exit();
                    }
                }
            
            } else {
                $data = array(
                    'tender_name' => I('post.tender_name', '', 'trim,strip_tags'),
                    'work_type_id' => I('post.work_type_id', 0, 'intval'),
                    'region_id' => I('post.region_id', 0, 'intval'),
                    'address' => I('post.address', '', 'trim,strip_tags'),
                    'user_id' => USER_ID,
                    'bidder_id' => 0,
                    'contacts' => I('post.contacts', '', 'trim,strip_tags'),
                    'mobile' => I('post.mobile', '', 'trim,strip_tags'),
                    'budget' => I('post.budget', 0, 'intval'),
                    'days' => I('post.days', 0, 'intval'),
                    'is_checked' => 0,
                    'status' => STATUS_WAIT_BID,
                    'finish_status' => FINISH_IS_INIT,
                    'remark' => I('post.remark', '', 'trim,strip_tags'),
                    'work_rank' => I('post.work_rank', 0, 'intval'),
                    'closing_time' => I('post.closing_time', 0, ''),
                    'add_time' => gmtime(),
                    'installment_num' => I('post.installment_num', 1, 'intval'),
                    'guarantee_date' => I('post.guarantee_date', 0, 'intval'),
                );
                if (empty($data['tender_name'])) {
                    $this->error('项目名称不能为空');
                    exit();
                }
                if (!in_array($data['installment_num'],array(1,2,3,4))) {
                    $this->error('未选择是否分期');
                    exit();
                }
                if (!in_array($data['guarantee_date'],array(0,1,2,3,6))) {
                    $this->error('未选择质保期');
                    exit();
                }
                $bidder_id = I('post.user_id', 0, 'intval');
                if($bidder_id != 0){
                    if($bidder_id == USER_ID){
                        $this->error("您不能对自己的进行预约！");
                        exit();  
                    }
                    $bidderUserInfo = M('Users')->field('user_id')->where(array('user_id' => $bidder_id,'user_type' => 1,'status' => 1,'is_delete' => 0))->find();
                    if(empty($bidderUserInfo)){
                        $this->error("工人不存在！");
                        exit();                          
                    }else{
                        $appointmentCount = M('Tender')->where(array('user_id' => USER_ID,'bidder_id' => $bidder_id,'tender_type' => 2,'add_time' => array('gt',  gmtime() - 3600)))->count();
                        if(!empty($appointmentCount)){
                            $this->error("对同一个工人一个小时只能进行一次预约，请耐心等待工人确认！");
                            exit();                                 
                        }
                        $data['status'] = STATUS_WAIT_CONFIRM;
                        $data['bidder_id'] = $bidder_id;
                        $data['tender_type'] = 2;
                        $data['work_rank'] = 0;  //直接预约不需要选择技工类型
                        $data['closing_time'] = gmtime() - 1 ;  //直接预约的到期时间为当前时间减1s
                    }
                }
                $work_type_is_exist = M('WorkType')->where(array('type_id' => $data['work_type_id'], 'level' => 2))->count();
                if (empty($work_type_is_exist)) {
                    $this->error('请选择服务类型');
                    exit();
                }
                $region_is_exist = M('Region')->where(array('region_id' => $data['region_id'], 'level' => 3))->count();
                if (empty($region_is_exist)) {
                    $this->error('请选择服务地区');
                    exit();
                }
                if (empty($data['mobile']) || !(\Com\Logdd\Validator::isMobile($data['mobile']))) {
                    $this->error('联系电话不能为空或手机号码不正确');
                    exit();
                }
                if (empty($data['address'])) {
                    $this->error('详细地址不能为空！');
                    exit();
                }                
             
                if (!in_array($data['work_rank'], array(0, 1, 2))) {
                    $this->error('请选择接单技工类型！');
                    exit();
                }
               // if (!in_array($data['closing_time'], array(1, 2)) && $data['bidder_id'] == 0) {
             //       $this->error('请选择接需求截至时间 ！');
               //     exit();
              //  }
                if ($data['closing_time'] == 1) {
                    $data['closing_time'] = gmtime() + 3 * 24 * 3600;
                } elseif ($data['closing_time'] == 2) {
                    $data['closing_time'] = gmtime() + 7 * 24 * 3600;
                }
                
                $result = M("Tender")->add($data);
                if ($result !== false) {
                    
                    //如果是直接预约，增加用户
                    if($data['bidder_id'] != 0){ 
                        $tenderUserData = array(
                            'tender_id' => $result,
                            'user_id' => $data['bidder_id'],
                            'add_time' => gmtime(),
                            'price' => $data['budget'],
                        );
                        M('TenderUser')->add($tenderUserData);
                    }
                    
                    $attachment_ids = I('post.attachment_id',array(),'intval');
                    if(!empty($attachment_ids)){
                        $attachment_ids = M('TenderAttachment')
                                ->where(array('attachment_id' => array('in',$attachment_ids),'user_id' => USER_ID ,'tender_id' => 0))
                                ->getField('attachment_id',true);
                    }
                    if(!empty($attachment_ids)){
                        M('TenderAttachment')
                        ->where(array('attachment_id' => array('in',$attachment_ids),'user_id' => USER_ID ,'tender_id' => 0))
                        ->save(array('tender_id' => $result));
                    }
                    
                    $this->success("需求提交成功，请等待管理员审核", U("Requirement/index"));
                    exit();
                } else {
                    $this->error("需求提交失败，请重新提交，如提交继续出现问题，请联系客服人员进行处理！");
                    exit();
                }
            }
        } else {
            $this->pageHeadInfo = array(
                'title' => '发布需求-'.C('web_name'),
                'keywords' => C('web_keywords'),
                'description' => C('web_description'),
            );              
            $this->city_id = M('Region')->where(array('region_id' => REGION_ID))->getField('parent_id');
            $this->province_id = M('Region')->where(array('region_id' => $this->city_id))->getField('parent_id');
            $where = array(
                't.is_checked' => 1,
                't.is_delete' => 0,
                't.status' => array('neq',0),
                't.region_id' => REGION_ID,
				't.is_activity' => 0,
            );
            $requirementList =M('Tender')->alias('t')->field('t.tender_id,t.contacts,t.tender_name,wt.type_name,t.add_time,t.closing_time')
                    ->join('left join __WORK_TYPE__ as wt on t.work_type_id = wt.type_id')
                    ->where($where)->order('add_time desc')->limit("0,20")->select();
            foreach($requirementList as &$list){
                $list['contacts'] = substr_cut($list['contacts']);
            }
            $this->requirementList = $requirementList;
            $this->display();
        }
    }


    //提交需求
    public function modify() {
        if (!USER_ID) {
            $this->error("请登录后再进行提交", U('User/login'));
            exit();
        }
        if (IS_POST) {
            $act = I('post.act', '');
            $tender_id = I('post.id', 0, 'intval');
            $tenderInfo = M('Tender')->field('status')->where(array('tender_id' => $tender_id,'user_id' => USER_ID))->find();
            if(empty($tenderInfo)){
                $this->error('需求不存在'); 
                exit();                
            }
            if($tenderInfo['status'] != STATUS_WAIT_BID){
                $this->error('不能修改需求信息'); 
                exit();
            }            
            if ($act == 'upload_attachment') {
                $upload = new \Think\Upload();
                $upload->maxSize   =     1024*1024 ;// 设置附件上传大小 1M
                $upload->exts = array('jpg', 'bmp', 'png'); // 设置附件上传类型
                $upload->rootPath = 'userfiles/'; // 设置附件上传根目录         
                $upload->savePath = USER_ID.'/requirement/'; // 设置附件上传根目录         
                $info = $upload->uploadOne($_FILES['Filedata']);
                $path = $upload->rootPath . $info['savepath'] . $info['savename'];
                if (empty($info)) {
                    $this->error($upload->getError(), '', true);
                } else {
                    $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 2);                      
                    if($result !== false){
                        $url = $result['url'];
                        $arr = array(
                            'user_id' => USER_ID,
                            'url' => $url,
                            'path' => $path,
                        );
                        $attachment_id = M('TenderAttachment')->add($arr);
                        if($attachment_id){
                            $result['attachment_id'] = $attachment_id;
                            unlink($path);
                            $this->success($result); 
                        }else{
                            $this->error('服务器处理错误，请重试'); 
                            exit();                        
                        }
                    }else{
                        $this->error('文件上传出现错误'); 
                        exit();
                    }
                }
            
            } 
            
            else {
                $data = array(
                    'budget' => I('post.budget', 0, 'intval'),
                    'days' => I('post.days', 0, 'intval'),
                    'region_id' => I('post.region_id', 0, 'intval'),
                    'address' => I('post.address', '', 'trim,strip_tags'),
                    'remark' => I('post.remark', '', 'trim,strip_tags'),
                    'work_rank' => I('post.work_rank', 0, 'intval'),
                    'closing_time' => I('post.closing_time', 0, ''),
                    'user_id' => USER_ID,
                );

                $region_is_exist = M('Region')->where(array('region_id' => $data['region_id'], 'level' => 3))->count();
                if (empty($region_is_exist)) {
                    $this->error('请选择服务地区');
                    exit();
                }       
             
                if (!in_array($data['work_rank'], array(0, 1, 2))) {
                    $this->error('请选择接单技工类型！');
                    exit();
                }
                if (!in_array($data['closing_time'], array(1, 2)) && $data['bidder_id'] == 0) {
                    $this->error('请选择接需求截至时间 ！');
                    exit();
                }
                if ($data['closing_time'] == 1) {
                    $data['closing_time'] = gmtime() + 3 * 24 * 3600;
                } elseif ($data['closing_time'] == 2) {
                    $data['closing_time'] = gmtime() + 7 * 24 * 3600;
                }
                
                $result = M("Tender")->where(array('tender_id' => $tender_id,'user_id' => USER_ID,'status' => STATUS_WAIT_BID))->save($data);
                if ($result !== false) {
                    M('TenderAttachment')
                        ->where(array('tender_id' => $tender_id))
                        ->save(array('tender_id' => 0));        
                    $attachment_ids = I('post.attachment_id', array(), 'intval');
                    if(!empty($attachment_ids)){
                        M('TenderAttachment')->where(array('attachment_id' => array('in',$attachment_ids),'user_id' => USER_ID ,'tender_id' => 0))
                        ->save(array('tender_id' => $tender_id));                
                    }
                    
                    $this->success("需求修改成功!", U("User/requirement"));
                    exit();
                } else {
                    $this->error("需求修改失败，请重新提交，如提交继续出现问题，请联系客服人员进行处理！");
                    exit();
                }
            }
        }
    }
    
    //提交需求
    public function detail() {
        $tender_id = I('get.id',0,'intval');
        $requirementInfo = M('Tender')->alias('t')->where(array('tender_id' => $tender_id,'is_delete' => 0))->find();
        if(empty($requirementInfo)){
            $this->error('需求不存在');
        }
        if($requirementInfo['status'] == 0 || $requirementInfo['is_checked'] == 0){
            $this->error('需求状态无效或正在审核中');
        }    
        
        $this->pageHeadInfo = array(
            'title' => '需求详情-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        
        $requirementInfo['contacts'] = substr_cut($requirementInfo['contacts']);
        $requirementInfo['add_time'] = local_date('Y-m-d H:i:s',$requirementInfo['add_time']);
        $requirementInfo['type_name'] = M('WorkType')->where(array('type_id' => $requirementInfo['work_type_id']))->getField('type_name');
        
        $this->regionInfo = M('Region')->field('region_id,region_name,parent_id')->where(array('region_id' => $requirementInfo['region_id']))->find();
        $this->cityInfo = M('Region')->field('region_id,region_name,parent_id')->where(array('region_id' => $this->regionInfo['parent_id']))->find();
        $this->provinceInfo = M('Region')->field('region_id,region_name,parent_id')->where(array('region_id' => $this->cityInfo['parent_id']))->find();
        $requirementInfo['files'] = M('TenderAttachment')->field('attachment_id,url')->where(array('tender_id' => $requirementInfo['tender_id']))->select();
        $this->requirementInfo = $requirementInfo;
        M('Tender')->where(array('tender_id' => $tender_id))->setInc('visit_time');
        
        $where = array(
            'is_checked' => 1,
            'is_delete' => 0,
            'status' => array('neq',0),
            'region_id' => REGION_ID,
            'tender_type' => 1,
            'tender_id' => array('gt',$tender_id),
        );        
        $nextTenderInfo = M('Tender')->field('tender_id')->where($where)->find();
        if(!empty($nextTenderInfo)){
            $this->nextTenderUrl = U('Requirement/detail?id='.$nextTenderInfo['tender_id']);
        }else{
            $this->nextTenderUrl = '';
        }
        $bidderList = M('TenderUser')->alias('t')->field('t.user_id,t.price,u.avatar,u.user_name,u.service_idea,u.money,u.tenders_take_num,u.tenders_finish_num,u.tenders_dispute_num,u.tenders_doing_num,u.score1,u.score2,u.score3,u.is_safe,u.is_verified')
                ->join('left join __USERS__ as u on t.user_id = u.user_id')
                ->where(array('t.tender_id' => $tender_id))->order('t.add_time desc')->limit('0,30')->select();
        $this->bidderList = $bidderList;
        $this->display();
    }

    //提交需求
    public function bidderList() {
        $tender_id = I('get.id',0,'intval');
        $requirementInfo = M('Tender')->alias('t')->where(array('tender_id' => $tender_id,'is_delete' => 0))->find();
        if(empty($requirementInfo)){
            $this->error('需求不存在');
        }
        if($requirementInfo['status'] == 0 || $requirementInfo['is_checked'] == 0){
            $this->error('需求状态无效或正在审核中');
        }    
        
        $this->pageHeadInfo = array(
            'title' => '需求详情-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $this->requirementInfo = $requirementInfo;
        $bidderList = M('TenderUser')->alias('t')->field('t.user_id,t.price,u.avatar,u.user_name,u.service_idea,u.money,u.tenders_take_num,u.tenders_finish_num,u.tenders_dispute_num,u.tenders_doing_num,u.score1,u.score2,u.score3,u.is_safe,u.is_verified')
                ->join('left join __USERS__ as u on t.user_id = u.user_id')
                ->where(array('t.tender_id' => $tender_id))->order('t.add_time desc')->limit('0,30')->select();
        $this->bidderList = $bidderList;
        $this->display();
    }
    
    public function bid(){
        if (!USER_ID) {
            $this->error("请登录后再进行竞标", U('User/login'));
            exit();
        }        
        $price = I('post.price',0,'intval');
        $tender_id = I('post.tender_id',0,'intval');
        $tenderInfo = M('Tender')->where(array('tender_id' => $tender_id,'is_delete' => 0 ,"is_checked" => 1))->find();
        if(empty($tenderInfo)){
            $this->error("招标需求不存在！");
            exit();
        }
        if($tenderInfo['status'] != STATUS_WAIT_BID && $tenderInfo['status'] != STATUS_WAIT_EMPLOY){
            $this->error("已完成选标,不能进行投标！");
            exit();
        }
        if($tenderInfo['user_id'] == USER_ID){
            $this->error("不能对自己的需求进行投标！");
            exit();
        }        
        if($tenderInfo['closing_time'] < gmtime()){
            $this->error("投标截止日期已过！");
            exit();
        }        
        $bid_work_rank = M('Users')->field('work_rank,user_type')->where(array('user_id' => USER_ID))->find();
        if($tenderInfo['work_rank'] == 2 && $bid_work_rank['work_rank'] != 2){
            $this->error("非VIP用户不能对本需求进行投标！");
            exit();
        } 
        
        if($bid_work_rank['user_type'] == 0){
            $this->error("工人类型才能进行投标！");
            exit();
        }  
        
        $is_bided = M('TenderUser')->where(array('user_id' => USER_ID,'tender_id' => $tender_id))->count();
        if($is_bided){
            $this->error("不能重新进行投标！");
            exit();
        }else{
            M('TenderUser')->add(array('user_id' => USER_ID,'tender_id' => $tender_id,'price' => $price,'add_time' => gmtime()));
            M('Tender')->where(array('tender_id' => $tender_id))->save(array('bidder_num' => array('exp','bidder_num+1'),'status' => STATUS_WAIT_EMPLOY));
            $this->success("投标成功，请等待业主选标！");
            exit();
        }
    }
    
    public function changeBidPrice(){
        if (!USER_ID) {
            $this->error("请登录后再进行操作", U('User/login'));
            exit();
        }
        $price = I('post.price',0,'intval');
        $tender_id = I('post.id',0,'intval');
        $tenderInfo = M('Tender')->field('status,tender_type,user_id,closing_time')->where(array('tender_id' => $tender_id,'is_delete' => 0 ,"is_checked" => 1))->find();
        if(empty($tenderInfo)){
            $this->error("招标需求不存在！");
            exit();
        }
        if($tenderInfo['closing_time'] < gmtime()){
            $this->error("投标截止日期已过！");
            exit();
        }
        if(($tenderInfo['status'] == STATUS_WAIT_EMPLOY)){
            $bidInfo = M('TenderUser')->field('price')->where(array('user_id' => USER_ID,'tender_id' => $tender_id))->find();
            if(empty($bidInfo)){
                $this->error("报价不存在！");
                exit();
            }else{
                M('TenderUser')->where(array('user_id' => USER_ID,'tender_id' => $tender_id))->save(array('price' => $price));
                $this->success("报价修改成功！");
                exit();                 
            }
        }else{
            $this->error("不能修改价格！");
            exit();            
        }
    } 
    
    
    public function comment(){
        if (!USER_ID) {
            $this->error("请登录后再进行操作", U('User/login'));
            exit();
        }

        $tender_id = I('post.tender_id',0,'intval');
        $comment_score1 = I('post.comment_score1',0,'intval');
        $comment_score2 = I('post.comment_score2',0,'intval');
        $comment_score3 = I('post.comment_score3',0,'intval');
        $comment_content = I('post.comment_content',0,'strip_tags,htmlspecialchars');
        if((!in_array($comment_score1, array(1,2,3,4,5))) || (!in_array($comment_score2, array(1,2,3,4,5))) || (!in_array($comment_score3, array(1,2,3,4,5)))){
            $this->error('评分不正确');
            exit();
        }
        $comment_score1 = $comment_score1 * 20;
        $comment_score2 = $comment_score2 * 20;
        $comment_score3 = $comment_score3 * 20;
        
        $tenderInfo = M('Tender')->field('bidder_id,comment_score1')->where(array('user_id' => USER_ID,'tender_id' => $tender_id,'is_delete' => 0 ,"is_checked" => 1))->find();
        if(empty($tenderInfo)){
            $this->error("需求不存在！");
            exit();
        }
        if($tenderInfo['comment_score1'] > 0){
            $this->error("需求已经进行过评价！");
            exit();            
        }
        $data = array(
            'comment_score1' => $comment_score1,
            'comment_score2' => $comment_score2,
            'comment_score3' => $comment_score3,
            'comment_content' => $comment_content,
            'comment_time' => gmtime(),
        );
        $result = M('Tender')->where(array('tender_id' => $tender_id))->save($data);
        if($result !== false){
            $commentAvg = M("Tender")->field("AVG(comment_score1) as score1,AVG(comment_score2) as score2,AVG(comment_score3) as score3")
                    ->where(array('bidder_id' => $tenderInfo['bidder_id'],'comment_time' => array('gt', 0)))->group('bidder_id')->find();
            M('Users')->where(array('user_id' => $tenderInfo['bidder_id']))->save($commentAvg);
            $this->success("评价完成！");
        }
    }     
    
    public function cancel(){
        if (!USER_ID) {
            $this->error("请登录后再进行操作", U('User/login'));
            exit();
        }
        $tender_id = I('post.id',0,'intval');
        $tenderInfo = M('Tender')->field('status,user_id,bidder_id')->where(array('tender_id' => $tender_id,'is_delete' => 0 ,"is_checked" => 1))->find();
        if(empty($tenderInfo)){
            $this->error("招标需求不存在！");
            exit();
        }        
        
        if($tenderInfo['status'] == STATUS_WAIT_BID){  //待承接直接取消
            if($tenderInfo['user_id'] == USER_ID){ 
                M('Tender')->where(array('tender_id' => $tender_id,'status' => $tenderInfo['status']))
                        ->save(array('status' => STATUS_IS_FINISH ,'finish_status' => FINISH_STEP_TEN,'finish_time' => gmtime()));
            }else{
                $this->error("无操作权限！");
                exit();                
            }
        }elseif($tenderInfo['status'] == STATUS_WAIT_EMPLOY){  //待承接直接取消
            if($tenderInfo['user_id'] == USER_ID){ 
                M('Tender')->where(array('tender_id' => $tender_id,'status' => $tenderInfo['status']))
                        ->save(array('status' => STATUS_IS_FINISH ,'finish_status' => FINISH_STEP_TWENTY,'finish_time' => gmtime()));
            }else{
                $this->error("无操作权限！");
                exit();                
            }
        }elseif($tenderInfo['status'] == STATUS_WAIT_CONFIRM){  //待确认直接取消
            if($tenderInfo['user_id'] == USER_ID || $tenderInfo['bidder_id'] == USER_ID){ 
                M('Tender')->where(array('tender_id' => $tender_id,'status' => $tenderInfo['status']))
                        ->save(array('status' => STATUS_IS_FINISH ,'finish_status' => FINISH_STEP_THIRTY,'finish_time' => gmtime()));
            }else{
                $this->error("无操作权限！");
                exit();
            }
        }elseif($tenderInfo['status'] == STATUS_IS_CONFIRM || $tenderInfo['status'] == STATUS_WAIT_PAY){  //已确认或待付款直接取消
            if($tenderInfo['user_id'] == USER_ID || $tenderInfo['bidder_id'] == USER_ID){ 
                M('Tender')->where(array('tender_id' => $tender_id,'status' => $tenderInfo['status']))
                        ->save(array('status' => STATUS_IS_FINISH ,'finish_status' => FINISH_STEP_FORTY,'finish_time' => gmtime()));
            }else{
                $this->error("无操作权限！");
                exit();
            }
        }elseif($tenderInfo['status'] == STATUS_WAIT_EXECUTE){  //未进场施工直接取消
            if($tenderInfo['user_id'] == USER_ID){ 
                M('Tender')->where(array('tender_id' => $tender_id,'status' => $tenderInfo['status']))
                        ->save(array('status' => STATUS_IS_FINISH ,'finish_status' => FINISH_STEP_FIFTY,'finish_time' => gmtime()));
            }else{
                $this->error("无操作权限！");
                exit();
            }
        }elseif($tenderInfo['status'] == STATUS_IS_EXECUTE){  //施工中直接取消
            if($tenderInfo['user_id'] == USER_ID || $tenderInfo['bidder_id'] == USER_ID){ 
                M('Tender')->where(array('tender_id' => $tender_id,'status' => $tenderInfo['status']))
                    ->save(array('status' => STATUS_IS_FINISH ,'finish_status' => FINISH_STEP_SIXTY,'finish_time' => gmtime()));
            }else{
                $this->error("无操作权限！");
                exit();
            }
        }elseif($tenderInfo['status'] == STATUS_WAIT_CHECK){  //待验收直接取消
            if($tenderInfo['user_id'] == USER_ID){ 
                M('Tender')->where(array('tender_id' => $tender_id,'status' => $tenderInfo['status']))
                        ->save(array('status' => STATUS_IS_FINISH ,'finish_status' => FINISH_STEP_SEVENTY,'finish_time' => gmtime()));
            }else{
                $this->error("无操作权限！");
                exit();
            }
        }
    }
    
    public function changeStatus(){
        if (!USER_ID) {
            $this->error("请登录后再进行操作", U('User/login'));
            exit();
        }
        $tender_id = I('post.tender_id',0,'intval');
        $status = I('post.status',0,'intval');
        $tenderInfo = M('Tender')->field('status,user_id,bidder_id')->where(array('tender_id' => $tender_id,'is_delete' => 0 ,"is_checked" => 1))->find();
        if(empty($tenderInfo)){
            $this->error("招标需求不存在！");
            exit();
        }
        
        if($status == STATUS_IS_EXECUTE){ //待施工改为施工中   业主操作
            if($tenderInfo['user_id'] == USER_ID && $tenderInfo['status'] == STATUS_WAIT_EXECUTE){
                M('Tender')->where(array('tender_id' => $tender_id))->save(array('status' => STATUS_IS_EXECUTE));
                $this->success('状态修改成功');
                exit();
            }else{
                $this->error("状态信息修改错误！");
                exit();                
            }
        }
        
        if($status == STATUS_WAIT_CHECK){ //施工中改为待验收  工人操作
            if($tenderInfo['bidder_id'] == USER_ID && $tenderInfo['status'] == STATUS_IS_EXECUTE){
                M('Tender')->where(array('tender_id' => $tender_id))->save(array('status' => STATUS_WAIT_CHECK));
                $this->success('状态修改成功');
                exit();
            }else{
                $this->error("状态信息修改错误！");
                exit();
            }
        }        

        if($status == STATUS_IS_FINISH){ //待验收改为验收完成  业主操作
            if($tenderInfo['user_id'] == USER_ID && $tenderInfo['status'] == STATUS_WAIT_CHECK){
                M('Tender')->where(array('tender_id' => $tender_id))->save(array('status' => STATUS_IS_FINISH,'finish_status' => FINISH_PREFECT,'finish_time' => gmtime()));
                M("Users")->where(array('user_id' => $tenderInfo['bidder_id']))->setInc('tenders_finish_num');
                $this->success('状态修改成功');
                exit();
            }else{
                $this->error("状态信息修改错误！");
                exit();
            }
        }
        
    }    
    
    
    public function confirmBidder(){
        if (!USER_ID) {
            $this->error("请登录后再进行操作", U('User/login'));
            exit();
        }
        $tender_id = I('post.tender_id',0,'intval');
        $bidder_id = I('post.bidder_id',0,'intval');
        $tenderInfo = M('Tender')->field('status,tender_type,user_id,closing_time')->where(array('user_id' => USER_ID,'tender_id' => $tender_id,'is_delete' => 0 ,"is_checked" => 1))->find();
        if(empty($tenderInfo)){
            $this->error("招标需求不存在！");
            exit();
        }
        if($tenderInfo['closing_time'] < gmtime()){
            $this->error("投标截止日期已过！");
            exit();
        }
        if($tenderInfo['status'] == STATUS_WAIT_EMPLOY){
            $bidInfo = M('TenderUser')->field('price')->where(array('user_id' => $bidder_id,'tender_id' => $tender_id))->find();
            if(empty($bidInfo)){
                $this->error("雇佣信息不存在！");
                exit();
            }else{
                $result = M('Tender')->where(array('bidder_id' => 0,'tender_id' => $tender_id))->save(array('bidder_id' => $bidder_id,'status' => STATUS_WAIT_CONFIRM));
                if ($result !== false && $result > 0) {
                    
                }
                $this->success("雇佣成功,等待付款！");
                exit();                 
            }
        }else{
            $this->error("招标不能进行重复雇佣！");
            exit();            
        }
    }         
    
    
    public function modifyPayInfo(){
        if (!USER_ID) {
            $this->error("请登录后再进行操作", U('User/login'));
            exit();
        }     
        $tender_id = I('post.tender_id',0,'intval');
        $tenderInfo = M('Tender')->field('status')->where(array('bidder_id' => USER_ID,'tender_id' => $tender_id,'is_delete' => 0 ,"is_checked" => 1))->find();
        if(empty($tenderInfo)){
            $this->error("需求不存在！");
            exit();
        }
        if($tenderInfo['status'] != STATUS_WAIT_CONFIRM && $tenderInfo['status'] != STATUS_IS_CONFIRM){
            $this->error("不能修改价格！");
            exit();
        }
        
        $data = array(
            'pay_part1' => I('post.pay_part1',0,'intval'),
            'pay_part2' => I('post.pay_part2',0,'intval'),
            'pay_part3' => I('post.pay_part3',0,'intval'),
            'pay_part4' => I('post.pay_part4',0,'intval'),
            'guarantee_money' => I('post.guarantee_money',0,'intval'),
            'guarantee_date' => I('post.guarantee_date',0,'intval'),
        );

        if ($data['pay_part1'] <= 0) {
            $this->error('首次付款价格必须大于0');
            exit();
        } 
        
        if($data['pay_part2'] == 0){
           $data['pay_part3'] = 0;
        }

        if($data['pay_part3'] == 0){
           $data['pay_part4'] = 0;
        }
        
        if (!in_array($data['guarantee_date'],array(0,1,2,3,6))) {
            $this->error('未选择质保期');
            exit();
        }    
        $data['order_price'] = $data['pay_part1'] + $data['pay_part2'] + $data['pay_part3'] + $data['pay_part4'];
        if($data['order_price'] <= $data['guarantee_money']){
            $this->error('质保金不能大于总金额');
            exit();           
        }
        
        $data['status'] = STATUS_IS_CONFIRM;
        $result = M('Tender')->where(array('tender_id' => $tender_id,'status' => array('in',array(STATUS_WAIT_CONFIRM,STATUS_IS_CONFIRM))))->save($data);
        if($result !== false){
            $this->success("价格信息提交成功！");
            exit();
        }else{
            $this->error("价格信息提交失败！");
            exit();            
        }
    }
    
    
    public function pay(){
        if (!USER_ID) {
            $this->error("请登录后再进行竞标", U('User/login'));
            exit();
        }        
        $price = I('post.price',0,'intval');
        $tender_id = I('post.tender_id',0,'intval');
        $tenderInfo = M('Tender')->where(array('tender_id' => $tender_id,'is_delete' => 0 ,"is_checked" => 1))->find();
        if(empty($tenderInfo)){
            $this->error("招标需求不存在！");
            exit();
        }
        if($tenderInfo['status'] != STATUS_WAIT_BID && $tenderInfo['status'] != STATUS_WAIT_EMPLOY){
            $this->error("已完成选标,不能进行投标！");
            exit();
        }
        if($tenderInfo['user_id'] == USER_ID){
            $this->error("不能对自己的需求进行投标！");
            exit();
        }        
        if($tenderInfo['closing_time'] < gmtime()){
            $this->error("投标截止日期已过！");
            exit();
        }        
        $bid_work_rank = M('Users')->where(array('user_id' => USER_ID))->getField('work_rank');
        if($tenderInfo['work_rank'] == 2 && $work_rank != 1){
            $this->error("非VIP用户不能对本需求进行投标！");
            exit();
        } 
        $is_bided = M('TenderUser')->where(array('user_id' => USER_ID,'tender_id' => $tender_id))->count();
        if($is_bided){
            $this->error("不能重新进行投标！");
            exit();            
        }else{
            M('TenderUser')->add(array('user_id' => USER_ID,'tender_id' => $tender_id,'price' => $price,'add_time' => gmtime()));
            M('Tender')->where(array('tender_id' => $tender_id))->save(array('bidder_num' => array('exp','bidder_num+1'),'status' => STATUS_WAIT_EMPLOY));
            $this->success("投标成功，请等待业主选标！");
            exit();
        }
    }    
}
