<?php

/**
 * 抓取远程图片
 * User: Jinqn
 * Date: 14-04-14
 * Time: 下午19:18
 */
if (!defined('UEDITOR')) {
    exit('attack');
}
set_time_limit(0);
include("Uploader.class.php");

/* 上传配置 */
$config = array(
    "pathFormat" => $CONFIG['catcherPathFormat'],
    "maxSize" => $CONFIG['catcherMaxSize'],
    "allowFiles" => $CONFIG['catcherAllowFiles'],
    "oriName" => "remote.png"
);
$fieldName = $CONFIG['catcherFieldName'];

/* 抓取远程图片 */
$list = array();
if (isset($_POST[$fieldName])) {
    $source = $_POST[$fieldName];
} else {
    $source = $_GET[$fieldName];
}

foreach ($source as $imgUrl) {
    $item = new Uploader($imgUrl, $config, "remote");
    $info = $item->getFileInfo();

    if($info['state'] == 'SUCCESS'){
        $result = \Com\Logdd\AliyunOssManager::uploadFile($info['url'], 2);  
    }

    if($result === false){
       $info['state'] == '上传到oss错误！';
       continue;
    }
    /*
    $data = array(
        'url' => $result['info'].'@!style_org_img',
        'path' => $info['url'],
        'add_time' => gmtime(),
    );
    $data['admin_id'] = session('user_info.user_id');
    $data['user_id'] = 0;
    $data['is_images'] = 1;
    M('UploadFilesInfo')->add($data);
    if(isset($info['url'])){
        unlink($info['url']);
        $info['url'] = $data['url'];
    }
     */
    unlink($info['url']);
    $info['url'] = $result['url'];      
    
    array_push($list, array(
        "state" => $info["state"],
        "url" => $info["url"],
        "size" => $info["size"],
        "title" => htmlspecialchars($info["title"]),
        "original" => htmlspecialchars($info["original"]),
        "source" => htmlspecialchars($imgUrl)
    ));
}

/* 返回抓取数据 */
return json_encode(array(
    'state' => count($list) ? 'SUCCESS' : 'ERROR',
    'list' => $list
        ));
