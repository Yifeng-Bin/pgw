<?php
namespace Index\Controller;
use Think\Controller;
class GetInfoController extends Controller{
    public function ipInfo(){
        $ip = get_client_ip(0,true);
        echo $ip;
        $ipInfo = json_decode(\Com\Logdd\BaiduLbsApi::locatioIp($ip));
        echo "<pre>";
        //print_r($_SERVER);
        echo "</pre>";
        echo "<pre>";
        print_r($ipInfo);
        echo "</pre>";
        if(isset($ipInfo) && $ipInfo->status == 0){
            $geocoder = json_decode(\Com\Logdd\BaiduLbsApi::geocoder($ipInfo->content->point->y.','.$ipInfo->content->point->x));
            echo "<pre>";
            //print_r($geocoder);
            echo "</pre>";            
            if(isset($geocoder) && $geocoder->status == 0){
                echo $geocoder->result->addressComponent->district;
            }else{
               echo "状态错误1"; 
            }
        }else{
            echo "状态错误2";
        }

    }
}


