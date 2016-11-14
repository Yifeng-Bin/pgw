<?php
namespace Index\Controller;
use Think\Controller;
class EmptyController extends CommonController{
    public function index(){
        $this->error("页面不存在",$this->baseurl);
    }
}