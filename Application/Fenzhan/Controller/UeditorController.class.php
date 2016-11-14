<?php
namespace Fenzhan\Controller;
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
        "imagePathFormat" => "files/desc/image/{yyyy}-{mm}-{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
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
        /* 抓取远程图片配置 */
        "catcherLocalDomain"=> ["127.0.0.1", "localhost", "img.baidu.com", "58pgw.com"],
        "catcherActionName"=>"catchimage", /* 执行抓取远程图片的action名称 */
        "catcherFieldName"=> "source", /* 提交的图片列表表单名称 */
        "catcherPathFormat"=> "files/desc/image/{yyyy}-{mm}-{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
        "catcherUrlPrefix"=>"", /* 图片访问路径前缀 */
        "catcherMaxSize"=> 2048000, /* 上传大小限制，单位B */
        "catcherAllowFiles"=> [".png", ".jpg", ".gif", ".bmp"], /* 抓取图片格式显示 */

        /* 上传文件配置 */
        "fileActionName"=> "uploadfile", /* controller里,执行上传视频的action名称 */
        "fileFieldName"=> "upfile", /* 提交的文件表单名称 */
        "filePathFormat"=> "files/desc/file/{yyyy}-{mm}-{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
        "fileUrlPrefix"=> "", /* 文件访问路径前缀 */
        "fileMaxSize"=> 51200000, /* 上传大小限制，单位B，默认50MB */
        "fileAllowFiles"=> [
            ".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
            ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml"
        ], /* 上传文件格式显示 */

        /* 列出指定目录下的图片 */
        "imageManagerActionName"=> "listimage", /* 执行图片管理的action名称 */
        "imageManagerListPath"=> "files/desc/image/", /* 指定要列出图片的目录 */
        "imageManagerListSize"=> 20, /* 每次列出文件数量 */
        "imageManagerUrlPrefix"=> "", /* 图片访问路径前缀 */
        "imageManagerInsertAlign"=> "none", /* 插入的图片浮动方式 */
        "imageManagerAllowFiles"=> [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* 列出的文件类型 */

        /* 列出指定目录下的文件 */
        "fileManagerActionName"=> "listfile", /* 执行文件管理的action名称 */
        "fileManagerListPath"=> "files/desc/file/", /* 指定要列出文件的目录 */
        "fileManagerUrlPrefix"=> "", /* 文件访问路径前缀 */
        "fileManagerListSize"=> 20, /* 每次列出文件数量 */
        "fileManagerAllowFiles"=> [
            ".png", ".jpg", ".gif", ".bmp",
            ".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
            ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml"
        ] /* 列出的文件类型 */
    );

    protected function _initialize() {
        if (!session('user_info.user_id')) {
            $this->error('未登录...', U('Index/index'));
        }
    }
    public function manage() {
        define('UEDITOR', 1);
        $ueditor_path = APP_PATH.'Admin/Common/ueditor/';
        $CONFIG = $this->config;
        $type = I('get.type',0,'intval');
        if($type == 1){
            $path_desc = 'article_desc';
        }else{
                $result = json_encode(array(
                    'state' => '未知类型'
                ));
            echo $result;
            die();
        }
        $CONFIG['imagePathFormat'] = str_replace("desc", $path_desc, $CONFIG['imagePathFormat']);
        $CONFIG['catcherPathFormat'] = str_replace("desc", $path_desc, $CONFIG['catcherPathFormat']);
        $CONFIG['filePathFormat'] = str_replace("desc", $path_desc, $CONFIG['filePathFormat']);
        $CONFIG['imageManagerListPath'] = str_replace("desc", $path_desc, $CONFIG['imageManagerListPath']);
        $CONFIG['fileManagerListPath'] = str_replace("desc", $path_desc, $CONFIG['fileManagerListPath']);
        $action = $_GET['action']; 
        switch ($action) {
            case 'config':
                $result = json_encode($CONFIG);
                break;
            /* 上传图片 */
            case 'uploadimage':
            /* 上传文件 */
            case 'uploadfile':
                $result = include($ueditor_path."action_upload.php");
                break;
            /* 抓取远程文件 */
            case 'catchimage':
                $result = include($ueditor_path."action_crawler.php");
                break;

            default:
                $result = json_encode(array(
                    'state' => '请求地址出错'
                ));
                break;
        }
        if($action != 'config'){
            admin_log("附件上传",$result,  session('user_info.user_id'));  
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
