<?php

namespace Admin\Controller;

class AfterSaleController extends CommonController {

    public function index() {
        $where = array(
            
            't.is_delete' => 0,
            
            //'t.region_id' => ADMIN_CITY_ID,
            
        );
        //筛选
        $filter = array(
            'fields_name' => array(
                'type' => array(
                    'desc'=>'工单类型',
                    'type' => "select",
                    'values' => array('-1'=>"选择工单类型",'1'=>"退款",'99'=>"其他"),
                    'match_type' => 'eq',
                    'exclude' => array(-1),                    
                ),
                'id' => array(
                    'desc'=>'工单id',
                    'type' => "input",
                    'match_type' => 'eq',
                ),     
                'tender_id' => array(
                    'desc'=>'需求id',
                    'type' => "input",
                    'match_type' => 'eq',
                ),                
                'status' => array(
                    'desc'=>'状态',
                    'type' => "select",
                    'values' => array('-1'=>"工单状态",'0'=>"已关闭",'1'=>"未关闭"),
                    'match_type' => 'eq',
                    'exclude' => array(-1),                    
                ),                           
            ),
            'fields_alias' => array(
                'id' => 's',
                'tender_id' => 's',
                'type' => 's',
                'status' => 's',
            ),
            'fields_value' => array(
                'id' => '',
                'tender_id' => '',
                'type' => '-1',
                'status' => '-1',
            ),
            'order' => array(
                "fields" => array(
                    'id' => 'id',
                ),
                'orderby' => 'id',
                'sortby' => 'desc',
            ),
        );
        
        foreach($filter['fields_name'] as $name => $info){
            $value =  I('get.filter_'.$name,'',  'htmlspecialchars');
            if($value !== ''){
                if(isset($info['exclude']) && in_array($value,$info['exclude'])){
                    continue;
                }                
                $filter['fields_value'][$name] = $value;
                if(isset($filter['fields_alias'][$name]) && !empty($filter['fields_alias'][$name])){
                    if($info['match_type'] == 'like'){
                        $where[$filter['fields_alias'][$name].".".$name] = array('like','%'.$value.'%');
                    }else{
                        $where[$filter['fields_alias'][$name].".".$name] = $value;
                        
                    }                    
                }else{
                    if($info['match_type'] == 'like'){
                        $where[$name] = array('like','%'.$value.'%');
                    }else{
                        $where[$name] = $value;                       
                    }                    
                }
            }
        }
        $order = array();
        $orerby = I('get.orderby','','htmlspecialchars');
        $sortby = I('get.sortby','','htmlspecialchars');
        if(array_key_exists($orerby, $filter['order']['fields'])){
            $filter['order']['orderby'] = $orerby;
        }
        if(in_array(strtolower($sortby), array('desc','asc'))){
            $filter['order']['sortby'] = strtolower($sortby);
        }
        $order[$filter['order']['orderby']] = $filter['order']['sortby'];
        //筛选        
        
        $page = new \Com\Logdd\Page(M('TenderAfterSale')->alias('s')->where($where)->count(), 50);
        $this->pageInfo = $page->getPageInfo();
        $afterSaleList = M('TenderAfterSale')->alias('s')->field('s.id,s.tender_id,s.user_id,s.type,s.content,s.refund_money,s.is_confirm,s.status,s.add_time,s.need_service,t.tender_name,r.region_name')
                ->join('left join __TENDER__ as t on t.tender_id = s.tender_id')->join('left join __REGION__ as r on r.region_id = t.region_id')
                ->limit($page->firstRow . ',' . $page->listRows)->where($where)->order($order)->select();
        foreach($afterSaleList as &$info){
            $info['add_time'] = local_date('Y-m-d H:i:s', $info['add_time']);
        }
        $this->afterSaleList = $afterSaleList;
        $this->filter = $filter;
        $this->display();
    }
    
    public function detail(){
        $id = I('get.id',0,'intval');     
        $afterSaleInfo = M('TenderAfterSale')->alias('s')
                ->field('s.id,s.tender_id,s.user_id,s.type,s.content,s.refund_money,s.is_confirm,s.status,s.attachment_01,s.attachment_02,s.attachment_03,s.add_time,s.need_service,s.refund_status,s.refund_remark,t.tender_name,t.pay_total,t.region_id')
                ->join('left join __TENDER__ as t on t.tender_id = s.tender_id')->where(array('id' => $id))->find();
       //var_Dump( $afterSaleInfo);die();
        if(empty($afterSaleInfo)){
            $this->error('售后工单不存在');
            exit();
        }
        $afterSaleInfo['add_time'] = local_date('Y-m-d H:i:s', $afterSaleInfo['add_time']);
        $afterSaleInfo['total_refund_money'] = M('TenderAfterSale')->where(array('tender_id' => $afterSaleInfo['tender_id'],'refund_status' => 1))->sum('refund_money');
        if(empty($afterSaleInfo['total_refund_money'])){
            $afterSaleInfo['total_refund_money'] = 0;
        }
        $this->afterSaleInfo = $afterSaleInfo;

        $afterSaleComment = M('TenderAfterSaleComment')->field('c.*,u.user_name,au.user_name as admin_user_name')->alias('c')
                ->join('left join __USERS__ as u on c.user_id = u.user_id')
                ->join('left join __ADMIN_USER__ as au on c.admin_id = au.uid')
                ->where(array('c.id' => $afterSaleInfo['id']))->select();
       
        //var_dump($afterSaleComment);die();
        foreach($afterSaleComment as &$info){
            $info['add_time'] = local_date('Y-m-d H:i:s', $info['add_time']);
        }
        $this->afterSaleComment = $afterSaleComment;
        
        $this->display();
    }
    
    
    public function comment(){
        $id = I('post.id',0,'intval');
        
        $afterSaleInfo =  M('TenderAfterSale')->field('id')->where(array('id' => $id))->find();
        if(empty($afterSaleInfo)){
            $this->error('手工售后工单不存在');
            exit();
        }
        else{
            $data = array( 
                'id' => $id,
                'user_id' => 0,
                'content' => I('post.content','','strip_tags,htmlspecialchars'),
                'add_time' => gmtime(),
                'admin_id' => ADMIN_USER_ID,
            );
            if(empty($data['content'])){
                $this->error('回复内容不能为空');
                exit();                
            }
            $result = M('TenderAfterSaleComment')->add($data);
            if($result === false){
                $this->error('服务器错误，请重试');
                exit();  
            }else{
                $this->success('回复提交成功');
                exit();
            }
        }
    }
    
    public function refundRemark(){
        $id = I('post.id',0,'intval');
        $afterSaleInfo =  M('TenderAfterSale')->field('id,refund_status')->where(array('id' => $id))->find();
        if(empty($afterSaleInfo)){
            $this->error('手工售后工单不存在');
            exit();
        }
        if($afterSaleInfo['refund_status'] == 1){
            $this->error('退款已完成无需重复执行');
            exit();
        }
        $data = array( 
            'refund_status' => 1,
            'refund_remark' => I('post.refund_remark','','strip_tags,htmlspecialchars'),
            'refund_time' => gmtime(),
        );
        
        $result = M('TenderAfterSale')->where(array('id' => $id,'refund_status' => 0))->save($data);
        if($result === false){
            $this->error('退款已完成无需重复执行');
            exit();  
        }else{
            $this->success('退款确认成功');
            exit();
        }

    }    
}
