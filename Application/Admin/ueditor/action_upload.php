<?php
/**
 * 上传附件和上传视频
 * User: Jinqn
 * Date: 14-04-09
 * Time: 上午10:17
 */
if (!defined('UEDITOR')) {
    exit('attack');
}
include "Uploader.class.php";
/* 上传配置 */
$base64 = "upload";
switch (htmlspecialchars($_GET['action'])) {
    case 'uploadimage':
        $config = array(
            "pathFormat" => $CONFIG['imagePathFormat'],
            "maxSize" => $CONFIG['imageMaxSize'],
            "allowFiles" => $CONFIG['imageAllowFiles']
        );
        $fieldName = $CONFIG['imageFieldName'];
        break;
    case 'uploadfile':
    default:
        $config = array(
            "pathFormat" => $CONFIG['filePathFormat'],
            "maxSize" => $CONFIG['fileMaxSize'],
            "allowFiles" => $CONFIG['fileAllowFiles']
        );
        $fieldName = $CONFIG['fileFieldName'];
        break;
}
/* 生成上传实例对象并完成上传 */
$up = new Uploader($fieldName, $config, $base64);

/**
 * 得到上传文件所对应的各个参数,数组结构
 * array(
 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
 *     "url" => "",            //返回的地址
 *     "title" => "",          //新文件名
 *     "original" => "",       //原始文件名
 *     "type" => ""            //文件类型
 *     "size" => "",           //文件大小
 * )
 */
/* 返回数据 */
$info = $up->getFileInfo();
if($info['state'] != 'SUCCESS'){
    return json_encode($info);
}
if($_GET['action'] == 'uploadimage'){
    $result = \Com\Logdd\AliyunOssManager::uploadFile($info['url'], 2);  
}else{
    $result = \Com\Logdd\AliyunOssManager::uploadFile($info['url'], 1);  
}

if($result === false){
   return json_encode(array('state' => '上传错误，请重试！')); 
}
/*
$data = array(
    'url' => $result['info'],
    'path' => $info['url'],
    'add_time' => gmtime(),
);
if($_GET['action'] == 'uploadimage'){
    $data['url'] = $data['url'].'@!style_org_img';
}
$data['admin_id'] = session('user_info.user_id');
$data['user_id'] = 0;

if($_GET['action'] == 'uploadimage'){
    $data['is_images'] = 1;
}else{
    $data['is_images'] = 0;
}
M('UploadFilesInfo')->add($data);
*/

unlink($info['url']);
$info['url'] = $result['url'];

return json_encode($info);
