<?php
namespace Index\Controller;
class AskController extends CommonController {
    public function index(){
        $this->pageHeadInfo = array(
            'title' => '知识问答-' . C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $type = I('get.type','');
        if(!in_array($type, array('','resolved','notresolved'))){
            $type = '';
        }
        $this->type = $type;
        $where = array(
            'a.is_delete' => 0,
            'a.cat_id_1' => 51,
            'a.is_checked' => 1,
        );
        if($type == 'resolved'){
            $where['is_resolved'] = 1;
        }elseif($type == 'notresolved'){
            $where['is_resolved'] = 0;
        }
        $count = M('Article')->alias('a')->where($where)->count();
        $Page = new \Think\Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        //$Page->lastSuffix = false;
        $Page->setConfig('first', '首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '…%TOTAL_PAGE%');
        $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
        $Page->rollPage = 11;
        $this->pageShow = $Page->show();        
        $askList = M('Article')->alias('a')->field('a.article_id,a.is_resolved,a.article_name,a.article_desc,a.comment_num,a.add_time,a.is_resolved,u.user_name,u.avatar')
                ->join('left join __USERS__ as u on a.user_id = u.user_id')
                ->limit($Page->firstRow . ',' . $Page->listRows)->order('add_time desc')->where($where)->select();
        foreach($askList as &$info){
            $info['add_time'] = local_date('Y-m-d H:i', $info['add_time']);
            $info['url'] = U('Ask/detail?id='.$info['article_id']);
        }
        $this->askList = $askList;
        $this->display();
    }   
    
    public function submit(){
        if (!USER_ID) {
            $this->error("请登录后再进行提交", U('User/login'));
            exit();
        }
        
        if(IS_POST){
            $act = I('post.act', '');
            if ($act == 'upload_attachment') {
                $upload = new \Think\Upload();
                $upload->maxSize   =     1024*1024 ;// 设置附件上传大小 1M
                $upload->exts = array('jpg', 'bmp', 'png'); // 设置附件上传类型
                $upload->rootPath = 'userfiles/'; // 设置附件上传根目录         
                $upload->savePath = USER_ID.'/ask/'; // 设置附件上传根目录         
                $info = $upload->uploadOne($_FILES['Filedata']);
                $path = $upload->rootPath . $info['savepath'] . $info['savename'];
                if (empty($info)) {
                    $this->error($upload->getError(), '', true);
                } else {
                    $result = \Com\Logdd\AliyunOssManager::uploadFile($path, 2);                      
                    if($result !== false){
                        $url = $result['url'];
                        $arr = array(
                            'url' => $url,
                            'path' => $path,
                            'user_id' => USER_ID,
                            'add_time' => gmtime(),
                        );
                        $img_id = M('ArticlePhoto')->add($arr);
                        if($img_id){
                            $result['img_id'] = $img_id;
                            unlink($path);
                            $this->success($result); 
                        }else{
                            $this->error('服务器处理错误，请重试'); 
                            exit();
                        }
                    }else{
                        $this->error('文件上传出现错误'); 
                        exit();
                    }
                }
            
            } else {
                $data = array(
                    'article_name' => I('post.ask_name', '', 'trim,strip_tags,htmlspecialchars'),
                    'article_desc' => I('post.ask_add', '', 'trim,strip_tags,htmlspecialchars'),
                    'cat_id' => 51,
                    'cat_id_1' => 51,
                    'is_checked' => 0,
                    'add_time' => gmtime(),
                    'user_id' => USER_ID,
                );
                if (empty($data['article_name'])) {
                    $this->error('问题不能为空');
                    exit();
                }
                $result = M("Article")->add($data);
                if ($result !== false) {
                    $img_ids = I('post.img_id',array(),'intval');
                    if(!empty($img_ids)){
                        $img_ids = M('ArticlePhoto')
                                ->where(array('img_id' => array('in',$img_ids),'user_id' => USER_ID ,'article_id' => 0))
                                ->getField('img_id',true);
                    }
                    if(!empty($img_ids)){
                        M('ArticlePhoto')
                        ->where(array('img_id' => array('in',$img_ids),'user_id' => USER_ID ,'article_id' => 0))
                        ->save(array('article_id' => $result));
                    }
                    
                    $this->success("问答提交成功，请等待管理员审核", U("Ask/index"));
                    exit();
                } else {
                    $this->error("问答提交失败，请重新提交，如提交继续出现问题，请联系客服人员进行处理！");
                    exit();
                }
            }
        }else{
            $this->pageHeadInfo = array(
                'title' => '知识问答-' . C('web_name'),
                'keywords' => C('web_keywords'),
                'description' => C('web_description'),
            );
            $this->display();
        }
    }
    
    public function detail(){
        $id = I('get.id',0,'intval');
        $where = array(
            'a.is_delete' => 0,
            'a.cat_id_1' => 51,
            'a.is_checked' => 1,  
            'a.article_id' => $id,
        );
        $askInfo = M('Article')->alias('a')->join('left join __USERS__ as u on a.user_id = u.user_id')
                ->field('a.article_id,a.article_name,a.visit_time,a.add_time,a.article_desc,a.user_id,u.user_name,u.avatar,a.is_resolved,a.best_comment_id')->where($where)->find();
        if(empty($askInfo)){
            $this->error('问答信息不存在');
            exit();
        }
        $this->pageHeadInfo = array(
            'title' => '知识问答-' . C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        $askInfo['add_time'] = local_date('Y-m-d H:i:s', $askInfo['add_time']);
        $askInfo['gallery'] = M('ArticlePhoto')->where(array('article_id' => $id))->order('sort desc')->select(); 
        $askInfo['commentList'] = M('ArticleComment')->alias('c')->join('__USERS__ as u on c.user_id = u.user_id')
                ->field('c.comment_id,c.content,c.add_time,u.user_name,u.avatar')->where(array('c.article_id' => $id,'c.is_checked' => 1,'c.is_delete' => 0))->limit('0,50')->order('add_time desc')->select();
        foreach($askInfo['commentList'] as &$info){
            $info['add_time'] = local_date('Y-m-d H:i:s', $info['add_time']);
        }
        $askInfo['commentNumber'] = count($askInfo['commentList']);
        $this->askInfo = $askInfo;
        
        $relateAskList = M('Article')->alias('a')->field('a.article_id,a.article_name,a.article_desc')
                ->limit('0,5')->order('add_time desc')->where(array('a.is_delete' => 0,'a.cat_id_1' => 51,'a.is_checked' => 1,))->select();
        foreach($relateAskList as &$info){
            $info['url'] = U('Ask/detail?id='.$info['article_id']);
        }
        $this->relateAskList = $relateAskList;             
        
        M('Article')->where(array('article_id' => $id))->setInc('visit_time');
        $this->display();
    }
}