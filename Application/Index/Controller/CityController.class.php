<?php

namespace Index\Controller;
use Think\Controller;
class CityController extends CommonController {

    public function change() {

       C(M('Config')->getField('name,value'));
        $this->pageHeadInfo = array(
            'title' => '选择区域-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $regionList = M('Region')->field('region_id,region_name')->where(array('level' => 2,'status' => 1,'is_delete' => 0))->order('sort asc')->select();
        foreach($regionList as &$regionInfo){
            $regionInfo['list'] = M('Region')->field('region_id,region_name,domain')->where(array('is_delete' => 0,'parent_id' => $regionInfo['region_id'],'status' => 1,'domain' => array('neq','')))->order('sort asc')->select();
            foreach($regionInfo['list'] as &$child){
                $child['url'] = 'http://'.$child['domain'].'.'.C('city_domain_prefix').'.'.C('root_domain');
            }
        }
        $this->regionList = $regionList;
        
		$this->display();
      
    }
    
//var_dump(password_verify('yanghang', '$2y$10$A8tVd5TUCbPieukv0kgl4uu5DWf/LOV8L1T3kxsC3IPNQr.UbMvmq'));//
//echo password_hash('123456', PASSWORD_BCRYPT, ['cost' => 10]); 
//password_hash($value, PASSWORD_BCRYPT, ['cost' => 10]);
//$2y$10$A8tVd5TUCbPieukv0kgl4uu5DWf/LOV8L1T3kxsC3IPNQr.UbMvmq
//password_verify($value, $hashedValue);

}
