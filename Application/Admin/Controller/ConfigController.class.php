<?php

namespace Admin\Controller;

class ConfigController extends CommonController {

    public function index() {
        $page = new \Com\Logdd\Page(M('Config')->count(), 50);
        $this->pageInfo = $page->getPageInfo();
        $this->configList = M('Config')->field('*')->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->display();
    }

    public function edit() {
        if (!IS_POST) {
            $id = I('get.id', 0, 'intval');
            $configInfo = M('Config')->find($id);
            if (empty($configInfo)) {
                $this->error('配置项不存在');
            } else {
                $this->success($configInfo);
            }
        } else {
            $id = I('post.id', 0, 'intval');
            $data = array(
                'title' => I('post.title',''),
                'name' => I('post.name',''),
                'value' => I('post.value',''),
                'prompt' => I('post.prompt',''),
            );
            $result = M('Config')->where(array('id'=>$id))->save($data);
            if ($result === false) {
                $this->error('配置信息编辑失败');
            } else {
                $data['id'] = $id;
                admin_log("编辑配置信息",json_encode($data));       
                $this->success('编辑成功');
            }
        }
    }

    public function add() {
        if (IS_POST) {
            $data = array(
                'title' => I('post.title',''),
                'name' => I('post.name',''),
                'value' => I('post.value',''),
                'prompt' => I('post.prompt',''),
            );
            $result = M('Config')->add($data);            
            if ($result === false) {
                $this->error('配置信息增加失败');
            } else {
                $data['id'] = $result;
                admin_log("增加配置信息",json_encode($data));                     
                $this->success('配置增加成功');
            }
        }
    }

    public function del() {
        $id = I('post.id', 0, 'intval');
        $result = D('Config')->delete($id);
        if (empty($result)) {
            $this->error('删除失败');
        } else {
            admin_log("删除配置信息,id为".$id); 
            $this->success('删除成功');
        }
    }
}
