<?php

namespace Fenzhan\Controller;

class RegionController extends CommonController {

    public function index() {
        $this->pid = I('get.pid','0','intval');
        if($this->pid != 0)
        {
            $parentRegionInfo = D('Region')->field('parent_id,level')->where(array('region_id'=>$this->pid))->find();
            $this->ppid = $parentCatInfo['parent_id'];
            $this->level = $parentCatInfo['level'];
        }
        else
        {
            $this->ppid = 0;
            $this->level = 1;
        }
        $this->regionList = D('Region')->where(array('parent_id' => $this->pid,'is_delete' => 0))->order('sort desc')->select();
        $this->display();
    }
    
    public function add() {
        if (IS_POST) {
            $result = D('Region')->addRegion();       
            if ($result === false) {
                $error = D('Region')->getModelError();
                $this->error($error);
            } else {
                $this->success('记录增加成功');
            }
        }
    }
    
    public function edit() {
        if (!IS_POST) {
            $regionInfo = D('Region')->getRegionInfo(I('get.region_id',0,'intval'));
            if (empty($regionInfo)) {
                $this->error('记录不存在');
            } else {
                $this->success($regionInfo);
            }
        } else {
            $this->result = D('Region')->editRegion();       
            if ($this->result === false) {
                $this->error('添加失败');
            } else {
                $this->success('记录增加成功');
            }
        }
    }
    public function del() {
        $region_id = I('post.region_id',0,'intval');
        $child_num = D('Region')->where(array('parent_id'=>$region_id))->count();
        if ($child_num) {
            $this->error('有下级区域不能删除');
        } else {
            $result = D('Region')->where(array('region_id'=>$region_id))->save(array('is_delete' => 1));
            if(empty($result))
            {
                admin_log("区域删除失败");
                $this->error('未知错误');
            }else
            {
               admin_log("区域删除成功");
               $this->success('删除成功'); 
            }
        }
    }
}
