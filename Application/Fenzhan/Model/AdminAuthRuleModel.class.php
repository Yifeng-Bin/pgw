<?php
namespace Fenzhan\Model;
class AdminAuthRuleModel extends CommonModel {
    
    protected $fields = array('id','name','title','type','status', 'sort', 'pid','level','icon','is_show','is_delete',
            '_type'=>array(
                'id'=>'mediumint(8) unsigned',
                'name'=>'char(80) []',
                'title'=>'char(20) []',
                'type'=>'tinyint(1) [1]',
                'status'=>'tinyint(1) [1]',
                'sort'=>'smallint(4) unsigned [1]',
                'pid'=>'mediumint(8) unsigned [0]',
                'level'=>'tinyint(1) [1]',
                'icon'=>'varchar(20) [nav-home]',
                'is_show'=>'tinyint(1) unsigned [1]',
                'is_delete'=>'tinyint(1) unsigned [0]',
            )
        );
    protected $pk = 'id';
    protected $_validate = array(
        array('title', 'require', '规则名称不能为空！'),
    );    
    
    public function getRuleListByPid($pid = 0) {
        $where = array(
            'is_delete' => 0,
            'pid' => $pid,
        );
        return $this->order("name asc")->where($where)->select();
    }
    public function getAuthInfo($id) {
        return $this->find($id);
    }
    public function addAuthRule()
    {
        $data = array(
            'name' => I('post.name','','htmlspecialchars'),
            'title' => I('post.title','','htmlspecialchars'),
            'status' => I('post.status',1,'intval')==0?0:1,
            'sort' => I('post.sort',100,'intval'),
            'pid' => I('post.pid',0,'intval'),
            'is_show' => I('post.is_show',1,'intval')==0?0:1,
        );
        $pInfo = $this->where(array('id' => $data['pid']))->field('level')->find();
        if($pInfo['level'] >= 3){
            $this->error = "规则列表仅支持三级";
            return false;
        }
        if($pInfo['level'] == 2){
            $data['type'] = 1;
            $data['level'] = 3;
        }else{
            $data['type'] = 0;
            $data['level'] = $pInfo['level'] + 1;     
            $data['name'] = '';
        }
        
        
        if (!$this->create($data, self::MODEL_INSERT)) {
            admin_log("规则插入失败",json_encode($data));
            return false;
        } else {
            $result = $this->add();
            $data['id'] = $result;
            admin_log("权限组插入成功",json_encode($data));
            return $result;
        }        
        
    }
    public function editAuthRule()
    {
        $data = array(
            'name' => I('post.name','','htmlspecialchars'),
            'title' => I('post.title','','htmlspecialchars'),
            'status' => I('post.status',1,'intval')==0?0:1,
            'sort' => I('post.sort',100,'intval'),
            'is_show' => I('post.is_show',1,'intval')==0?0:1,
        );
        if (!$this->create($data, self::MODEL_UPDATE)) {
            admin_log("规则编辑失败",json_encode($data));
            return false;
        } else {
            admin_log("规则编辑成功",json_encode($data));
            return $this->where(array('id'=>intval(I('post.id'))))->save($data);
        }
        
    }
    public function delAuthRule()
    {
        admin_log("规则删除成功,id为".I('post.id',0,'intval'));
        return $this->where(array(id=>intval(I('post.id'))))->save(array('is_delete' => 1));
    }       
}
