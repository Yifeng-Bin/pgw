<?php

namespace Index\Controller;

class PublicController extends CommonController {

    //验证码
    public function verify() {
        $key = I('get.key','');
        if(in_array($key,array('comment'))){
            $Verify = new \Think\Verify(array('length' => 4,'useCurve'=>false,'fontSize' => 40,'imageH' => 80));
            $Verify->entry($key);            
        }
        exit();
    }
}