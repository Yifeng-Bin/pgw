<?php

namespace Fenzhan\Controller;

class AdsController extends CommonController {

    public function index() {
        $page = new \Com\Logdd\Page(D('AdPosition')->where(array('is_delete' => 0))->count(), 50);
        $this->pageInfo = $page->getPageInfo();
        $this->adPositionList = D('AdPosition')->field('*')->where(array('is_delete' => 0))->getList($page->firstRow, $page->listRows);
        $this->display();
    }

    public function editPosition() {
        $position_id = I('get.position_id', 0, 'intval');
        if (!IS_POST) {
            $adPositionInfo = D('AdPosition')->getAdPosition($position_id);
            if (empty($adPositionInfo)) {
                $this->error('广告位不存在');
            } else {
                $this->success($adPositionInfo);
            }
        } else {
            $result = D('AdPosition')->editAdPosition();
            if ($result === false) {
                $this->error(D("AdPosition")->getModelError());
                $this->error('广告位编辑失败');
            } else {
                $this->success('广告位编辑成功');
            }
        }
    }

    public function addPosition() {
        if (IS_POST) {
            $result = D('AdPosition')->addAdPosition();
            if ($result === false) {
                $this->error(D("AdPosition")->getModelError());
            } else {
                $this->success('广告位增加成功');
            }
        }
    }

    public function delPosition() {
        $result = D('AdPosition')->delAdPosition();
        if (empty($result)) {
            $this->error('删除失败');
        } else {
            $this->success('删除成功');
        }
    }

    public function adList() {
        $position_id = intval(I('get.position_id'));
        $page = new \Com\Logdd\Page(D('Ad')->where(array('position_id'=>$position_id))->count(), 50);
        $this->pageInfo = $page->getPageInfo();
        $this->position_id = $position_id;
        $this->adList = D('Ad')->where(array('position_id'=>$position_id))->field('*')->getList($page->firstRow, $page->listRows);
        $this->display();
    }
    public function add() {
        if (IS_POST) {
            $result = D('Ad')->addAd();
            if ($result === false) {
                $this->error(D("Ad")->getModelError());
            } else {
                $this->success('广告增加成功');
            }
        }        
    }
    public function edit() {
        $ad_id = I('get.ad_id', 0, 'intval');
        if (!IS_POST) {
            $adInfo = D('Ad')->getAd($ad_id);
            if (empty($adInfo)) {
                $this->error('广告不存在');
            } else {
                $this->success($adInfo);
            }
        } else {
            $result = D('Ad')->editAd();
            if ($result === false) {
                $this->error(D("Ad")->getModelError());
                $this->error('广告编辑失败');
            } else {
                $this->success('广告编辑成功');
            }
        }        
    }
    public function del() {
        $result = D('Ad')->delAd();
        if (empty($result)) {
            $this->error('删除失败');
        } else {
            $this->success('删除成功');
        }        
    }

}
