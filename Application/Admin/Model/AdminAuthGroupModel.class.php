<?php

namespace Admin\Model;

class AdminAuthGroupModel extends CommonModel {

    protected $fields = array('id', 'title', 'status', 'sort', 'rules', 'is_delete',
            '_type'=>array(
                'id'=>'mediumint(8) unsigned',
                'title'=>'varchar(100) []',
                'status'=>'tinyint(1) [1]',
                'sort'=>'smallint(4) unsigned [100]',
                'rules'=>'text',
                'is_delete'=>'tinyint(1) unsigned [0]',
            ),
        );
    protected $pk = 'id';
    protected $_validate = array(
        array('title', 'require', '权限组名称不能为空！'),
    );

    public function getList($page, $size = 20) {
        return $this->limit($page . ',' . $size)->select();
    }

    public function getGroupInfo($id) {
        return $this->where(array('is_delete' => 0))->find($id);
    }

    public function addAuthGroup() {
        $ids = (!isset($_POST['menus'])) ? array() : array_unique(array_filter($_POST['menus'], "intval"));
        foreach ($ids as $k => $v) {
            if ($v < 1) {
                unset($ids[$k]);
            }
        }
        $rules = implode(',', $ids);

        $data = array(
            'title' => I('post.title', ''),
            'sort' => intval(I('post.sort')),
            'status' => intval(I('post.status')) == 0 ? 0 : 1,
            'rules' => $rules,
        );
        if (!$this->create($data, self::MODEL_INSERT)) {
            admin_log("权限组插入失败",json_encode($data));
            return false;
        } else {
            $result = $this->add();
            $data['id'] = $result;
            admin_log("权限组插入成功",json_encode($data));
            return $result;
        }
    }

    public function editGroupInfo() {
        $ids = (!isset($_POST['menus'])) ? array() : array_unique(array_filter($_POST['menus'], "intval"));
        foreach ($ids as $k => $v) {
            if ($v < 1) {
                unset($ids[$k]);
            }
        }
        $rules = implode(',', $ids);
        $data = array(
            'title' => I('post.title', ''),
            'status' => I('post.status', 0, 'intval') == 0 ? 0 : 1,
            'sort' => I('post.sort', 100, 'intval'),
            'rules' => $rules,
            'id' => I('post.id', 0, 'intval'),
        );
        if (!$this->create($data, self::MODEL_UPDATE)) {
            admin_log("权限组更新失败",json_encode($data));
            return false;
        } else {
            admin_log("权限组更新成功",json_encode($data));
            return $this->save();
        }
    }

    public function delGroupInfo() {
        admin_log("删除权限组,id为".I('post.id',0,'intval'));
        return $this->where(array('id' => I('post.id',0,'intval')))->save(array('is_delete' => 1));
    }

}
