<?php
namespace Admin\Model;
class WorkTypeModel extends CommonModel {
    
    protected $fields = array('type_id', 'type_name', 'type_decs', 'type_keywords', 'status','sort','parent_id','level','work_type_img_01',
            '_type'=>array(
                'type_id'=>'smallint(5) unsigned',
                'type_name'=>'varchar(100) []',
                'type_decs'=>'varchar(255) []',
                'type_keywords'=>'varchar(255) []',  
                'status'=>'tinyint(1) unsigned [1]',  
                'sort'=>'smallint(5) unsigned [100]',  
                'parent_id'=>'smallint(5) unsigned [0]',  
                'level'=>'smallint(5) unsigned [1]',  
                'work_type_img_01'=>'varchar(255) []',
            ),
        );
    protected $pk = 'type_id';
    protected $_validate = array(
        array('type_name', 'require', '工种名称不能为空！'),
    );    
    
    public function getList($pid = 0) {
        return $this->where(array('parent_id'=>$pid))->select();
    }
    
    public function addType()
    {
        $parent_id = I('post.parent_id',0,'intval');
        if($parent_id == 0){
          $level  = 1;  
        }else{
            $level = $this->where(array('type_id'=>$parent_id))->getField('level');
            if($level > 2){
                $this->error = '工种只支持3级！';
                return false;
            } 
            $level = $level + 1;
        }

        $data = array(
            'type_name' => I('post.type_name','','htmlspecialchars'),
            'type_decs' => I('post.type_decs','','htmlspecialchars'),
            'type_keywords' => I('post.type_keywords','','htmlspecialchars'),
            'status' => I('post.status',0,'intval')==0?0:1,
            'sort' => I('post.sort',0,'intval'),
            'parent_id' => I('post.parent_id',0,'intval'),
            'level' => $level,
            'work_type_img_01' => I('post.work_type_img_01','','htmlspecialchars'),
        );
        
        if (!$this->create($data, self::MODEL_INSERT)) {
            return false;
        } else {
            $type_id = $this->add();
            $data['type_id'] = $type_id;
            admin_log("增加工种",json_encode($data));                
            return $type_id;
        }
    }

    public function editType()
    {
        $data = array(
            'type_name' => I('post.type_name','','htmlspecialchars'),
            'type_decs' => I('post.type_decs','','htmlspecialchars'),
            'type_keywords' => I('post.type_keywords','','htmlspecialchars'),
            'status' => I('post.status',0,'intval')==0?0:1,
            'sort' => I('post.sort',0,'intval'),
            'work_type_img_01' => I('post.work_type_img_01','','htmlspecialchars'),
        );  
        if (!$this->create($data, self::MODEL_UPDATE)) {
            return false;
        } else {
            $result = $this->where(array('type_id'=>I('post.type_id',0,'intval')))->save();
            $data['type_id'] = I('post.type_id',0,'intval');
            admin_log("编辑工种",json_encode($data));
            return $result;
        }
    }    
    
    public function getTypeInfo($type_id)
    {
        return $this->where(array('type_id' => $type_id))->find();
    }
    
    public function getAllCat($level = 0) {
        $order = array(
            'level' => 'desc',
            'sort' => 'desc',
        );
        $where = array();
        if($level > 0){
            $where['level'] = array('ELT',$level);
        }
        $type_arr = $this->field('type_id,type_name,level,parent_id')->where($where)->index('type_id')->order($order)->select();
        foreach($type_arr as $key => &$type_info){
            if($type_info['parent_id'] != 0){ 
                $type_arr[$cat_info['parent_id']]['children'][$type_info['type_id']] = $type_info;
                unset($type_arr[$key]);
            }
        }
        return $type_arr;
    }
}
