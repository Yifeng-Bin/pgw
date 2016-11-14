<?php

namespace Fenzhan\Controller;

class WorkTypeController extends CommonController {

    public function index() {
        $this->pid = I('get.pid','0','intval');
        if($this->pid != 0)
        {
            $parentTypeInfo = D('WorkType')->field('parent_id,level')->where(array('cat_id'=>$this->pid))->find();
            $this->ppid = $parentTypeInfo['parent_id'];
            $this->level = $parentTypeInfo['level'];
        }
        else
        {
            $this->ppid = 0;
            $this->level = 1;
        }
        $this->typeList = D('WorkType')->getList($this->pid);
        $this->display();
    }
    
    public function add() {
        if (IS_POST) {
            $result = D('WorkType')->addType();       
            if ($result === false) {
                $error = D('WorkType')->getModelError();
                $this->error($error);
            } else {
                $this->success('记录增加成功');
            }
        }
    }
    
    public function edit() {
        if (!IS_POST) {
            $typeInfo = D('WorkType')->getTypeInfo(I('get.type_id',0,'intval'));
            if (empty($typeInfo)) {
                $this->error('记录不存在');
            } else {
                $this->success($typeInfo);
            }
        } else {
            $this->result = D('WorkType')->editType();       
            if ($this->result === false) {
                $this->error(D("WorkType")->getModelError());
            } else {
                $this->success('记录增加成功');
            }
        }
    } 
    public function del() {
        $type_id = I('post.type_id',0,'intval');
        $type_info = D('WorkType')->find($type_id);
        if(empty($type_info)){
            $this->error('工种不存在');
        }
        //判断是否存在下级工种
        $child_num = D('WorkType')->where(array('parent_id'=>$type_id))->count();
        //判断工种是否存在工人
        $goods_num = D('UserToWorkType')->where(array('type_id_'.$type_info['level']=>$type_id))->count();   
        if ($goods_num) {
            $this->error('工种下存在工人信息，不能直接删除');
        }        
        if ($child_num) {
            $this->error('有下级工种不能删除');
        } else {
            $result = D('WorkType')->where(array('type_id'=>$type_id))->delete();
            if(empty($result))
            {
                $this->error('未知错误');
            }else
            {
               admin_log("删除工种，id为".$type_id);
               $this->success('删除成功'); 
            }
            
        }
    }    
}
