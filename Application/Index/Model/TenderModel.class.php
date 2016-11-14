<?php
namespace Index\Model;
class TenderModel extends CommonModel {

    protected $_validate = array(
        //array('ad_name', 'require', '广告名称不能为空！'),
        //array('position_id', 'require', '广告位不能为空！'),
    );
    public $modelErrorInfo = array(
        //'field_duplicate' => '广告名称不能重复',
    );
}
