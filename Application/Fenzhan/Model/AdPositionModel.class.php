<?php
namespace Fenzhan\Model;
class AdPositionModel extends CommonModel {

    protected $fields = array('position_id', 'position_name', 'ad_width', 'ad_height', 'position_desc','is_delete',
            '_type'=>array(
                'position_id'=>'tinyint(3) unsigned',
                'position_name'=>'varchar(60) []',
                'ad_width'=>'smallint(5) unsigned [0]',
                'ad_height'=>'smallint(5) unsigned [0]',
                'position_desc'=>'varchar(255) []',
                'is_delete' => 'tinyint(1) unsigned [0]',
                )
        );
    protected $pk = 'position_id';
    protected $_validate = array(
        array('position_name', 'require', '广告位名称不能为空！'),
    );
    public $modelErrorInfo = array(
        'field_duplicate' => '广告位名称不能重复',
    );
    public function getList($page, $size = 20) {
        return $this->limit($page . ',' . $size)->select();
    }

    public function addAdPosition() {
        $data = array(
            'position_name' => I('post.position_name', '', 'htmlspecialchars'),
            'position_desc' => I('post.position_desc', '', 'htmlspecialchars'),
        );
        if (!$this->create($data, self::MODEL_INSERT)) {
            admin_log("广告位创建失败");
            return false;
        } else {
            $position_id = $this->add();
            $data['position_id'] = $position_id;
            admin_log("广告位创建成功",json_encode($data));
        }
        return $position_id;
    }

    public function editAdPosition() {
        $position_id = intval(I('post.position_id'));
        $data = array(
            'position_name' => I('post.position_name', '', 'htmlspecialchars'),
            'position_desc' => I('post.position_desc', '', 'htmlspecialchars'),
        );
        if (!$this->create($data, self::MODEL_UPDATE)) {
            admin_log("广告位编辑失败");
            return false;
        } else {
            $result = $this->where(array('position_id' => $position_id))->save();
            $data['position_id'] = $position_id;
            admin_log("广告位编辑成功",json_encode($data));
        }
        return $result;
    }

    public function delAdPosition() {
        $position_id  = I('post.position_id',0,'intval');
        $count = M('Ad')->where(array('position_id'=>$position_id,'is_delete' => 0))->count();
        if($count){
            return false;
        }else{
            admin_log("广告位删除,id为".$position_id);
            return $this->where(array('position_id' => $position_id,'is_delete' => 0))->save(array('is_delete' => 1));                     
        }

    }


    public function getAdPosition($position_id) {
        return $this->field('*')->find($position_id);
    }

}
