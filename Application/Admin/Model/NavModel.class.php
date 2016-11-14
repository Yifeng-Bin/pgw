<?php
namespace Admin\Model;
use Think\Model;
class NavModel extends CommonModel {
    
    protected $fields = array('id', 'name', 'status', 'order', 'opennew', 'url','url_type', 'type',
            '_type'=>array(
                'id'=>'mediumint(8)',
                'name'=>'varchar(255)',
                'status'=>'tinyint(1) unsigned [1]',
                'order'=>'tinyint(1) unsigned [100]',
                'opennew'=>'tinyint(1) unsigned [0]',
                'url_type'=>'tinyint(1) unsigned [1]',
                'url'=>'varchar(255)',
                'type'=>'varchar(10) [top]'
                )
        );
    protected $pk = 'id';
    protected $_validate = array(
        array('name', 'require', '导航名称不能为空！'),
    );
    public $modelErrorInfo = array(
        //'field_duplicate' => '广告位名称不能重复',
    );    
    
    protected $nav_pos_list = array(
        'top' => '顶部',
        'middle' => '中间',
        'bottom' => '底部',
    );
    
    public function getNavPosList() {
        return $this->nav_pos_list;
    }
    
    public function getList($page, $size = 20) {
        $nav_list = $this->field('*')->limit($page . ',' . $size)->select();
        foreach($nav_list as $k => $navInfo){
            $nav_list[$k]['typeInfo'] = $this->nav_pos_list[$navInfo['type']];
        }
        return $nav_list;
    }

    public function addNav() {
        $data = array(
            'name' => I('post.name', '', 'htmlspecialchars'),
            'status' => intval(I('post.status')) == 0 ? 0 : 1,
            'order' => I('post.order', '1', 'intval'),
            'opennew' => I('post.opennew', '1', 'intval'),
            'url' => I('post.url', '', 'htmlspecialchars'),
            'url_type' => I('post.url_type', '1', 'intval'),
            'type' => I('post.type', '', 'htmlspecialchars'),
        );
        if (!$this->create($data, self::MODEL_INSERT)) {
            return false;
        } else {
            $id = $this->add();
            $data['id'] = $id;
            admin_log("增加导航信息",json_encode($data));              
            return $id;
        }
    }

    public function editNav() {
        $id = intval(I('post.id'));
        $data = array(
            'name' => I('post.name', '', 'htmlspecialchars'),
            'status' => intval(I('post.status')) == 0 ? 0 : 1,
            'order' => I('post.order', '1', 'intval'),
            'opennew' => I('post.opennew', '1', 'intval'),
            'url' => I('post.url', '', 'htmlspecialchars'),
            'url_type' => I('post.url_type', '1', 'intval'),
            'type' => I('post.type', '', 'htmlspecialchars'),
        );
        if (!$this->create($data, self::MODEL_UPDATE)) {
            return false;
        } else {
            $result = $this->where(array('id' => $id))->save();
            $data['id'] = $id;
            admin_log("编辑导航信息",json_encode($data));
            return $result;
        }
    }

    public function getNavInfo($id) {
        $navInfo = $this->where(array('id' => $id))->find();
        return $navInfo;
    }

}


