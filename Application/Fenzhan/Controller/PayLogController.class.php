<?php

namespace Fenzhan\Controller;

class PayLogController extends CommonController {

    public function index() {
        $where = array(

            'u.region_id' => ADMIN_CITY_ID,
        );
        //筛选
        $filter = array(
            'fields_name' => array(
                'user_name' => array(
                    'desc'=>'用户名',
                    'type' => "input",
                    'match_type' => 'eq',
                ),
                'mobile' => array(
                    'desc'=>'手机号码',
                    'type' => "input",
                    'match_type' => 'eq',
                ),                
                'log_type' => array(
                    'desc'=>'支付类型',
                    'type' => "select",
                     'values' => array('-1'=>"支付类型",'1'=>"保证金",'2'=>"金币充值",'3'=>"需求支付"),
                     'match_type' => 'eq',
                     'exclude' => array(-1),
                ),                
                 'pay_status' => array(
                    'desc'=>'支付状态',
                    'type' => "select",
                     'values' => array('-1'=>"支付状态",'0'=>"未支付",'1'=>"已支付"),
                     'match_type' => 'eq',
                     'exclude' => array(-1),
                ),               
            ),
            'fields_alias' => array(
                'user_name' => 'u',
                'mobile' => 'u',
                'log_type' => 'p',
                'pay_status' => 'p',
            ),
            'fields_value' => array(
                'user_name' => '',
                'mobile' => '',
                'log_type' => '-1',
                'pay_status' => '-1',
            ),
            'order' => array(
                "fields" => array(
                    'log_time' => '支付时间',                 
                ),
                'orderby' => 'log_time',
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
        
        $page = new \Com\Logdd\Page(M('PayLog')->alias('p')->join('left join __USERS__ as u on p.user_id = u.user_id')->where($where)->count(), C('PAGE_SIZE'));
        $this->pageInfo = $page->getPageInfo();
        $payLogList = M('PayLog')->field('p.*,u.user_name,pt.pay_name')->alias('p')->join('left join __USERS__ as u on p.user_id = u.user_id')
                        ->join('left join __PAYMENT__ as pt on p.pay_id = pt.pay_id')
                        ->where($where)->order($order)->limit($page->firstRow.",".$page->listRows)->select();
        foreach($payLogList as &$info){
            if($info['out_id'] > 0){
                $info['detail'] = M('Tender')->field('tender_name')->where(array('tender_id' => $info['out_id']))->find();
            }
            if($info['pay_time'] > 0){
                $info['pay_time'] = local_date('Y-m-d H:i:s', $info['pay_time']);
            }
        }
        
        $this->payLogList = $payLogList;
        $this->filter = $filter;
        $this->display();
    }
}
