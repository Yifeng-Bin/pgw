<?php
namespace Index\Behavior;
class CheckTemplateBehavior {
    public function run(&$params) {		/*
        $isMobile = cookie('is_mobile');
        if($isMobile === null){
            $checkMobile = new \Com\Logdd\Mobile_Detect;
            $isMobile = $checkMobile->isMobile();       
            if($isMobile){
                cookie('is_mobile',1,30*24*3600);
            }else{
                cookie('is_mobile',0,30*24*3600);
            }
        }
        if($isMobile){
            C('DEFAULT_THEME','m');
            define('WEB_TYPE', 'm');            
        }else{
            define('WEB_TYPE', 'pc');
        }		*/
    }
}
