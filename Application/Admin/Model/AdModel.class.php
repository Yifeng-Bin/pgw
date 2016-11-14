<?php

namespace Admin\Model;

class AdModel extends CommonModel {

    protected $fields = array('ad_id', 'position_id', 'ad_name', 'ad_text', 'ad_link', 'ad_image', 'start_time', 'end_time', 'click_count', 'status','is_delete',
        '_type' => array(
            'ad_id' => 'smallint(5) unsigned',
            'position_id' => 'smallint(5) unsigned [0]',
            'ad_name' => 'varchar(60) []',
            'ad_text' => 'varchar(255) []',
            'ad_link' => 'varchar(255) []',
            'ad_image' => 'varchar(255) []',
            'start_time' => 'int(11) [0]',
            'end_time' => 'int(11) [0]',
            'click_count' => 'mediumint(8) unsigned [0]',
            'status' => 'tinyint(3) unsigned [1]',
            'is_delete' => 'tinyint(1) unsigned [0]',
        )
    );
    protected $pk = 'ad_id';
    protected $_validate = array(
        array('ad_name', 'require', '广告名称不能为空！'),
        array('position_id', 'require', '广告位不能为空！'),
    );
    public $modelErrorInfo = array(
            //'field_duplicate' => '广告名称不能重复',
    );

    public function getList($page, $size = 20) {
        $adList = $this->limit($page . ',' . $size)->where(array('is_delete' => 0))->select();
        foreach ($adList as $key => $adInfo) {
            $adList[$key]['start_time'] = local_date('Y-m-d H:i:s', $adInfo['start_time']);
            $adList[$key]['end_time'] = local_date('Y-m-d H:i:s', $adInfo['end_time']);
        }
        return $adList;
    }

    public function addAd() {
        $data = array(
            'position_id' => I('post.position_id', '0', 'intval'),
            'ad_name' => I('post.ad_name', '', 'htmlspecialchars'),
            'ad_text' => I('post.ad_text', '', 'htmlspecialchars'),
            'ad_link' => I('post.ad_link', '', 'htmlspecialchars'),
            'ad_image' => I('post.ad_image', '', 'htmlspecialchars'),
            'start_time' => I('post.start_time', '0', 'gmstr2time'),
            'end_time' => I('post.end_time', '0', 'gmstr2time'),
            'status' => I('post.status', '0', 'intval'),
        );
        if (!$this->create($data, self::MODEL_INSERT)) {
            admin_log("广告创建失败");
            return false;
        } else {
            $ad_id = $this->add();
            $data['ad_id'] = $ad_id;
            admin_log("广告创建成功(" . $ad_id . ")", json_encode($data));
            return $ad_id;
        }
    }

    public function editAd() {
        $ad_id = intval(I('post.ad_id'));
        $data = array(
            'position_id' => I('post.position_id', '0', 'intval'),
            'ad_name' => I('post.ad_name', '', 'htmlspecialchars'),
            'ad_text' => I('post.ad_text', '', 'htmlspecialchars'),
            'ad_link' => I('post.ad_link', '', 'htmlspecialchars'),
            'ad_image' => I('post.ad_image', '', 'htmlspecialchars'),
            'start_time' => I('post.start_time', '0', 'gmstr2time'),
            'end_time' => I('post.end_time', '0', 'gmstr2time'),
            'status' => I('post.status', '0', 'intval'),
        );
        if (!$this->create($data, self::MODEL_UPDATE)) {
            admin_log("广告编辑失败(" . $ad_id . ")");
            return false;
        } else {
            $result = $this->where(array('ad_id' => $ad_id))->save();
            $data['ad_id'] = $ad_id;
            admin_log("广告编辑成功(" . $ad_id . ")",json_encode($data));
        }
        return $result;
    }

    public function delAd() {
        $ad_id = I('post.ad_id', 0, 'intval');
        admin_log("广告删除(" . $ad_id . ")");
        return $this->where(array('ad_id' => $ad_id))->save(array('is_delete' => 1));
    }

    public function getAd($ad_id) {
        $adInfo = $this->field('*')->find($ad_id);
        if (!empty($adInfo)) {
            $adInfo['start_time'] = local_date('Y-m-d H:i:s', $adInfo['start_time']);
            $adInfo['end_time'] = local_date('Y-m-d H:i:s', $adInfo['end_time']);
        }
        return $adInfo;
    }

}
