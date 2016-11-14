<?php

namespace Fenzhan\Model;

class TemplateDataModel extends CommonModel {

    protected $fields = array('data_id','region_id','position_id' ,'data_name', 'data_text', 'data_link', 'data_image', 'start_time', 'end_time', 'click_count', 'status', 'sort','is_delete',
        '_type' => array(
            'data_id' => 'smallint(5) unsigned',
            'region_id' => 'smallint(5) unsigned',
            'position_id' => 'smallint(5) unsigned [0]',
            'data_name' => 'varchar(60) []',
            'data_text' => 'varchar(255) []',
            'data_link' => 'varchar(255) []',
            'data_image' => 'varchar(255) []',
            'start_time' => 'int(11) [0]',
            'end_time' => 'int(11) [0]',
            'click_count' => 'mediumint(8) unsigned [0]',
            'status' => 'tinyint(3) unsigned [1]',
            'sort' => 'tinyint(3) unsigned [100]',
            'is_delete' => 'tinyint(1) unsigned [0]',
        )
    );
    protected $pk = 'data_id';
    protected $_validate = array(
        array('data_name', 'require', '广告名称不能为空！'),
        array('position_id', 'require', '广告位不能为空！'),
    );
    public $modelErrorInfo = array(
            //'field_duplicate' => '广告名称不能重复',
    );

    public function getList($page, $size = 20) {
        $templateDataList = $this->limit($page . ',' . $size)->where(array('is_delete' => 0))->select();
        foreach ($templateDataList as &$info) {
            $info['start_time'] = local_date('Y-m-d H:i:s', $info['start_time']);
            $info['end_time'] = local_date('Y-m-d H:i:s', $info['end_time']);
        }
        return $templateDataList;
    }

    public function addTemplateData() {
        $data = array(
            //'region_id' => I('post.region_id', '0', 'intval'),
            'region_id' => intval(ADMIN_CITY_ID),
            'position_id' => I('post.position_id', '0', 'intval'),
            'data_name' => I('post.data_name', '', 'htmlspecialchars'),
            'data_text' => I('post.data_text', '', 'htmlspecialchars'),
            'data_link' => I('post.data_link', '', 'htmlspecialchars'),
            'data_image' => I('post.data_image', '', 'htmlspecialchars'),
            'start_time' => I('post.start_time', '0', 'gmstr2time'),
            'end_time' => I('post.end_time', '0', 'gmstr2time'),
            'status' => I('post.status', '0', 'intval'),
            'sort' => I('post.sort', '0', 'intval'),
        );
        if (!$this->create($data, self::MODEL_INSERT)) {
            admin_log("模版数据创建失败");
            return false;
        } else {
            $data_id = $this->add();
            $data['data_id'] = $data_id;
            admin_log("模版数据创建成功(" . $data_id . ")", json_encode($data));
            return $data_id;
        }
    }

    public function editTemplateData() {
        $data_id = intval(I('post.data_id'));
        $data = array(
            //'region_id' => I('post.region_id', '0', 'intval'),
            'region_id' => intval(ADMIN_CITY_ID),
            'position_id' => I('post.position_id', '0', 'intval'),
            'data_name' => I('post.data_name', '', 'htmlspecialchars'),
            'data_text' => I('post.data_text', '', 'htmlspecialchars'),
            'data_link' => I('post.data_link', '', 'htmlspecialchars'),
            'data_image' => I('post.data_image', '', 'htmlspecialchars'),
            'start_time' => I('post.start_time', '0', 'gmstr2time'),
            'end_time' => I('post.end_time', '0', 'gmstr2time'),
            'status' => I('post.status', '0', 'intval'),
            'sort' => I('post.sort', '0', 'intval'),
        );
        if (!$this->create($data, self::MODEL_UPDATE)) {
            admin_log("模版数据编辑失败(" . $data_id . ")");
            return false;
        } else {
            $result = $this->where(array('data_id' => $data_id))->save();
            $data['data_id'] = $data_id;
            admin_log("模版数据编辑成功(" . $data_id . ")",json_encode($data));
        }
        return $result;
    }

    public function delTemplateData() {
        $data_id = I('post.data_id', 0, 'intval');
        admin_log("模版数据删除(" . $data_id . ")");
        return $this->where(array('data_id' => $data_id))->save(array('is_delete' => 1));
    }

    public function getTemplateData($data_id) {
        $info = $this->field('*')->find($data_id);
        if (!empty($info)) {
            $info['start_time'] = local_date('Y-m-d H:i:s', $info['start_time']);
            $info['end_time'] = local_date('Y-m-d H:i:s', $info['end_time']);
        }
        return $info;
    }

}
