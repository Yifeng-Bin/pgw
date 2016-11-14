<?php
namespace Index\Controller;
use Think\Controller;
class UeditorController extends Controller {
    protected $config = array(
        "imageActionName"=> "uploadimage",
        "imageFieldName"=> "upfile", /* 提交的图片表单名称 */
        "imageMaxSize"=> 2048000, /* 上传大小限制，单位B */
        "imageAllowFiles" => array(".png", ".jpg", ".gif", ".bmp"), /* 上传图片格式显示 */
        "imageCompressEnable"=> true, /* 是否压缩图片,默认是true */
        "imageCompressBorder"=> 1600, /* 图片压缩最长边限制 */
        "imageInsertAlign"=> "none", /* 插入的图片浮动方式 */
        "imageUrlPrefix"=> "", /* 图片访问路径前缀 */
        "imagePathFormat" => "userfiles/user_id/type/{yyyy}-{mm}-{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                                    /* {filename} 会替换成原文件名,配置这项需要注意中文乱码问题 */
                                    /* {rand:6} 会替换成随机数,后面的数字是随机数的位数 */
                                    /* {time} 会替换成时间戳 */
                                    /* {yyyy} 会替换成四位年份 */
                                    /* {yy} 会替换成两位年份 */
                                    /* {mm} 会替换成两位月份 */
                                    /* {dd} 会替换成两位日期 */
                                    /* {hh} 会替换成两位小时 */
                                    /* {ii} 会替换成两位分钟 */
                                    /* {ss} 会替换成两位秒 */
                                    /* 非法字符 \ : * ? " < > | */
                                    /* 具请体看线上文档: fex.baidu.com/ueditor/#use-format_upload_filename */
    );

    protected function _initialize() {
        if (!session('user_info.user_id')) {
            $this->error('未登录...', U('Index/index'));
        }
    }
    public function manage() {
        define('UEDITOR', 1);
        $ueditor_path = APP_PATH.'Index/Common/ueditor/';
        $CONFIG = $this->config;
        $type = I('get.type','');
        if($type == 'diary'){
            $path_desc = 'diary';
        }else{
                $result = json_encode(array(
                    'state' => '未知类型'
                ));
            echo $result;
            die();
        }
        $CONFIG['imagePathFormat'] = str_replace("type", $path_desc, $CONFIG['imagePathFormat']);
        $CONFIG['imagePathFormat'] = str_replace("user_id", session('user_info.user_id'), $CONFIG['imagePathFormat']);
        $action = $_GET['action']; 
        switch ($action) {
            case 'config':
                $result = json_encode($CONFIG);
                break;
            /* 上传图片 */
            case 'uploadimage':
                $result = include($ueditor_path."action_upload.php");
                break;
            default:
                $result = json_encode(array(
                    'state' => '请求地址出错'
                ));
                break;
        }
        if($action != 'config'){

        }
        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state' => 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
            die();
        }
    }
}
