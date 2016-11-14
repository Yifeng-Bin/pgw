<?php
namespace Index\Controller;
class IndexController extends CommonController {
    public function index() {		
        $this->pageHeadInfo = array(
            'title' => '首页-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );				$sd = M('TemplateData')->field('data_id,data_text,data_link')->find();
        $this->display();
    }
    
    public  function tuliao(){
        $this->pageHeadInfo = array(
            'title' => '涂料计算器-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $this->display();
    }
    
    public  function diban(){
        $this->pageHeadInfo = array(
            'title' => '地板计算器-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $this->display();
    }
    
    public  function bizhi(){
        $this->pageHeadInfo = array(
            'title' => '壁纸计算器-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $this->display();
    }
    
    public  function qiangzhuan(){
        $this->pageHeadInfo = array(
            'title' => '墙砖计算器-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $this->display();
    }
    
    public  function chuanglian(){
        $this->pageHeadInfo = array(
            'title' => '窗帘计算器-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $this->display();
    }
    
    public  function dizhuan(){
        $this->pageHeadInfo = array(
            'title' => '地砖计算器-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $this->display();
    }
    
    
    
}
