<?php

namespace Fenzhan\Controller;

class NavigatorController extends CommonController {

    public function index() {
        $page = new \Com\Logdd\Page(D('Nav')->count(), C('PAGE_SIZE'));
        $this->pageInfo = $page->getPageInfo();
        $this->navList = D('Nav')->order('`type` DESC,`order` DESC')->getList($page->firstRow, $page->listRows);
        $this->NavPosList = D('Nav')->getNavPosList();
        $this->display();
    }

    public function edit() {
        $id = intval(I('get.id'));
        if (!IS_POST) {
            $navInfo = D('Nav')->getNavInfo($id);
            if (empty($navInfo)) {
                $this->error('用户不存在');
            } else {
                $this->success($navInfo);
            }
        } else {
            $result = D('Nav')->editNav();
            if (($result === false)) {
                $this->error(D('Nav')->getModelError());
            } else {
                    $this->success('编辑导航成功');
            }   
        }
    }

    public function add() {
        if (IS_POST) {
            $result = D('Nav')->addNav();
            if (($result === false)) {
                $this->error(D('Nav')->getModelError());
            } else {
                    $this->success('导航添加成功');
            }            
        }
    }
}
