<?php
namespace Fenzhan\Model;
class TemplateDataPositionModel extends CommonModel {

    protected $fields = array('position_id', 'position_name', 'width', 'height', 'position_desc','position_type',
            '_type'=>array(
                'position_id'=>'tinyint(3) unsigned',
                'position_name'=>'varchar(60) []',
                'width'=>'smallint(5) unsigned [0]',
                'height'=>'smallint(5) unsigned [0]',
                'position_desc'=>'varchar(255) []',
                'position_type' => "enum('link','worker','article') [link]",
                )
        );
    protected $pk = 'position_id';
    protected $_validate = array(
        array('position_name', 'require', '模版位名称不能为空！'),
    );
    public $modelErrorInfo = array(
        'field_duplicate' => '模版位名称不能重复',
    );

    public function getList($page, $size = 20) {
       return $this->limit($page . ',' . $size)->select();
    }

    public function addTemplateDataPosition() {
        $data = array(
            'position_name' => I('post.position_name', '', 'htmlspecialchars'),
            'position_desc' => I('post.position_desc', '', 'htmlspecialchars'),
            'position_type' => I('post.position_type', '', 'htmlspecialchars'),
        );
        if (!$this->create($data, self::MODEL_INSERT)) {
            admin_log("模版位创建失败");
            return false;
        } else {
            $position_id = $this->add();
            $data['position_id'] = $position_id;
            admin_log("模版位创建成功",json_encode($data));
        }
        return $position_id;
    }

    public function editTemplateDataPosition() {
        $position_id = intval(I('post.position_id'));
        $data = array(
            'position_name' => I('post.position_name', '', 'htmlspecialchars'),
            'position_desc' => I('post.position_desc', '', 'htmlspecialchars'),
            'position_type' => I('post.position_type', '', 'htmlspecialchars'),
        );
        if (!$this->create($data, self::MODEL_UPDATE)) {
            admin_log("模版位编辑失败");
            return false;
        } else {
            $result = $this->where(array('position_id' => $position_id))->save();
            $data['position_id'] = $position_id;
            admin_log("模版位编辑成功",json_encode($data));
        }
        return $result;
    }

    public function delTemplateDataPosition() {
        $position_id  = I('post.position_id',0,'intval');
        $count = M('TemplateData')->where(array('position_id'=>$position_id))->count();
        if($count){
            return false;
        }else{
            admin_log("模版位删除,id为".$position_id);
            return $this->where(array('position_id' => $position_id))->delete();                     
        }

    }


    public function getTemplateDataPosition($position_id) {
        return $this->field('*')->find($position_id);
    }

}
