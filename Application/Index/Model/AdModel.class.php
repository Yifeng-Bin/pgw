<?php
namespace Index\Model;
class AdModel extends CommonModel {

    protected $fields = array('ad_id','position_id', 'ad_name', 'ad_link', 'ad_image', 'start_time', 'end_time', 'click_count', 'status',
            '_type'=>array(
                'ad_id'=>'smallint(5) unsigned',
                'position_id'=>'smallint(5) unsigned [0]',
                'ad_name'=>'varchar(60) []',
                'ad_link'=>'varchar(255) []',
                'ad_image'=>'varchar(255) []',
                'start_time'=>'int(11) [0]',
                'end_time'=>'int(11) [0]',
                'click_count'=>'mediumint(8) unsigned [0]',
                'status'=>'tinyint(3) unsigned [1]'
                )
        );
    protected $pk = 'ad_id';
    protected $_validate = array(
        //array('ad_name', 'require', '广告名称不能为空！'),
        //array('position_id', 'require', '广告位不能为空！'),
    );
    public $modelErrorInfo = array(
        //'field_duplicate' => '广告名称不能重复',
    );
    public function getAdList($position_id){
        $now_time = gmtime();
        $where = array(
            'status' => 1,
            'position_id' => $position_id,
            'start_time ' => array(array('ELT',$now_time),array('eq',0),'or'),
            'end_time ' => array(array('EGT',$now_time),array('eq',0),'or'),
        );
        $adList = $this->where($where)->select();
        return $adList;
    }

}
