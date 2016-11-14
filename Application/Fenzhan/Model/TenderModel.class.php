<?php

namespace Fenzhan\Model;

class TenderModel extends CommonModel {

    protected $pk = 'tender_id';
    protected $_validate = array(
        array('tender_name', 'require', '文章名称不能为空！'),
    );

//    public function getList($page, $size = 20, $order = '') {
//        if (!empty($order)) {
//            $this->order($order);
//        } else {
//            $this->order('article_id desc');
//        }
//        return $this->limit($page . ',' . $size)->select();
//    }

    public function addInfo() {
        $user_id = I('post.user_id', '0', 'intval');
        $userInfo = M("Users")->field('user_id')->where(array('user_id' => $user_id))->find();
        if(empty($userInfo)){
            $this->error = '用户不存在';
            return false;
        }        
        $data = array(
            'user_id' => $user_id,
            'tender_name' => I('post.tender_name', '', 'htmlspecialchars'),
            'work_type_id' => I('post.work_type_id', 0, 'intval'),
            'region_id' => I('post.region_id', 0, 'intval'),
            'address' => I('post.address', '', 'htmlspecialchars'),
            'contacts' => I('post.contacts', '', 'htmlspecialchars'),
            'mobile' => I('post.mobile', '', 'htmlspecialchars'),
            'budget' => I('post.budget', 0, 'intval'),
            'days' => I('post.days', 0, 'intval'),
            'is_checked' => I('post.is_checked', 0, 'intval'),
            'status' => I('post.status', 0, 'intval'),
            'work_rank' => I('post.work_rank', 0, 'intval'),
            'remark' => I('post.remark', '', 'strip_tags,htmlspecialchars'),
            'closing_time' => I('post.closing_time', 0, 'gmstr2time'),
            'add_time' => gmtime(),
        );
        if($data['work_type_id'] == 0){
            $this->error = '未选择工种';
            return false;
        }
        if($data['region_id'] == 0){
            $this->error = '未选择地区';
            return false;
        }        
        if (!$this->create($data, self::MODEL_INSERT)) {
            admin_log("需求增加失败");
            return false;
        } else {
            $result = $this->add();
        }
        if ($result === false) {
            return false;
        }
        $attachment_ids = I('post.attachment_id', array(), 'intval');
        if(!empty($attachment_ids)){
            M('TenderAttachment')->where(array('attachment_id' => array('in',$attachment_ids),'user_id' => $user_id ,'tender_id' => 0))
            ->save(array('tender_id' => $result));                
        }
        $data['tender_id'] = $tender_id;
        admin_log("需求编辑成功", json_encode($data));
        return $result;
    }

    public function editInfo() {
        $tender_id = I('post.tender_id', '0', 'intval');
        $data = array(
            'tender_name' => I('post.tender_name', '', 'htmlspecialchars'),
            'work_type_id' => I('post.work_type_id', 0, 'intval'),
            'region_id' => I('post.region_id', 0, 'intval'),
            'address' => I('post.address', '', 'htmlspecialchars'),
            'contacts' => I('post.contacts', '', 'htmlspecialchars'),
            'mobile' => I('post.mobile', '', 'htmlspecialchars'),
            'budget' => I('post.budget', 0, 'intval'),
            'days' => I('post.days', 0, 'intval'),
            'is_checked' => I('post.is_checked', 0, 'intval'),
            'status' => I('post.status', 0, 'intval'),
            'work_rank' => I('post.work_rank', 0, 'intval'),
            'remark' => I('post.remark', '', 'strip_tags,htmlspecialchars'),
            'closing_time' => I('post.closing_time', 0, 'gmstr2time'),
        );
        if($data['work_type_id'] == 0){
            $this->error = '未选择工种';
            return false;
        }
        if($data['region_id'] == 0){
            $this->error = '未选择地区';
            return false;
        }        
        if (!$this->create($data, self::MODEL_UPDATE)) {
            admin_log("需求编辑失败");
            return false;
        } else {
            $result = $this->where(array('tender_id' => $tender_id))->save();
        }
        if ($result === false) {
            return false;
        }
        $user_id = $this->where(array('tender_id' => $tender_id))->getField('user_id');
        M('TenderAttachment')
            ->where(array('tender_id' => $tender_id))
            ->save(array('tender_id' => 0));        
        $attachment_ids = I('post.attachment_id', array(), 'intval');
        if(!empty($attachment_ids)){
            M('TenderAttachment')->where(array('attachment_id' => array('in',$attachment_ids),'user_id' => $user_id ,'tender_id' => 0))
            ->save(array('tender_id' => $tender_id));                
        }
        $data['tender_id'] = $tender_id;
        admin_log("需求编辑成功", json_encode($data));
        return $result;
    }

    public function getInfo($tender_id) {

        $tender_info = $this->find($tender_id);
        if(!empty($tender_info)){
            $tender_info['attachmentList'] = M('TenderAttachment')->where(array('tender_id' => $tender_info['tender_id']))->select();
            $tender_info['city_id'] = M('Region')->where(array('region_id' => $tender_info['region_id']))->getField('parent_id');
            $tender_info['province_id'] = M('Region')->where(array('region_id' => $tender_info['city_id']))->getField('parent_id');     
            $tender_info['top_work_type_id'] = M('WorkType')->where(array('type_id' => $tender_info['work_type_id']))->getField('parent_id');  
            $tender_info['closing_time'] = local_date('Y-m-d H:i:s', $tender_info['closing_time']);
        }
    
        return $tender_info;
    }

}
