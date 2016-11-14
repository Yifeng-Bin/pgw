<?php
namespace Fenzhan\Model;
class RegionModel extends CommonModel {
    protected $fields = array('region_id', 'parent_id', 'region_name', 'level', 'domain','status','sort','is_delete',
            '_type'=>array(
                'region_id'=>'smallint(5) unsigned',
                'parent_id'=>'smallint(5) unsigned [0]',
                'region_name'=>'varchar(120) []',
                'level'=>'tinyint(1) unsigned [2]',  
                'domain'=>'varchar(10) []',  
                'status'=>'tinyint(1) unsigned [1]',  
                'sort'=>'smallint(3) unsigned [100]',  
                'is_delete'=>'tinyint(1) unsigned [0]',  
            ),
        );
    protected $pk = 'region_id';
    protected $_validate = array(
        array('region_name', 'require', '名称不能为空！'),
    );    


    public function addRegion()
    {
        $parent_id = I('post.parent_id',0,'intval');
        if($parent_id == 0){
          $level  = 1;  
        }else{
            $level = $this->where(array('region_id'=>$parent_id))->getField('level');
            if($level > 3){
                $this->error = '分类只支持4级！';
                return false;
            } 
            $level = $level + 1;
        }

        $data = array(
            'parent_id' => I('post.parent_id',0,'intval'),
            'region_name' => I('post.region_name','',''),
            'level' => $level,
            'domain' => I('post.domain','',''),
            'status' => I('post.status',0,'intval')==0?0:1,
            'sort' => I('post.sort',0,'intval'),
            'is_delete' => I('post.is_delete',0,'intval')==0?0:1,  
        );
        if (!$this->create($data, self::MODEL_INSERT)) {
            admin_log("区域增加失败");
            return false;
        } else {
            $result = $this->add();
            $data['region_id'] = $result;
            admin_log("区域增加成功",json_encode($data));
            return $result;
        }
    }

    public function editRegion()
    {
        $data = array(
            'region_name' => I('post.region_name','',''),
            'domain' => I('post.domain','',''),
            'status' => I('post.status',0,'intval')==0?0:1,
            'sort' => I('post.sort',0,'intval'),
        );
        
        if (!$this->create($data, self::MODEL_UPDATE)) {
            admin_log("区域编辑失败");
            return false;
        } else {
            $result = $this->where(array('region_id'=>intval(I('post.region_id'))))->save();
            $data['region_id'] = intval(I('post.region_id'));
            admin_log("区域编辑成功",json_encode($data));
            return $result;
        }
    }
    
    public function getRegionInfo($region_id)
    {
        return $this->where(array('region_id' => $region_id))->find();
    }

    
    public function getAllRegion($level = 1) {
        $order = array(
            'level' => 'desc',
        );        
        $where = array();
        if($level > 0){
            $where['level'] = array('ELT',$level);
        }
        $region_arr = $this->where($where)->order($order)->getField('region_id,region_name,level,parent_id');
        foreach($region_arr as $key => &$region_info){
            if($region_info['parent_id'] != 0){
                $region_arr[$region_info['parent_id']]['children'][$region_info['region_id']] = $region_info;
                unset($region_arr[$key]);
            }
        }
        return $region_arr;
    }    
}
