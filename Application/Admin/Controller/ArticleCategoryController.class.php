<?php

namespace Admin\Controller;

class ArticleCategoryController extends CommonController {

    public function index() {
        $this->pid = I('get.pid','0','intval');
        if($this->pid != 0)
        {
            $parentCatInfo = D('ArticleCategory')->field('parent_id,level')->where(array('cat_id'=>$this->pid))->find();
            $this->ppid = $parentCatInfo['parent_id'];
            $this->level = $parentCatInfo['level'];
        }
        else
        {
            $this->ppid = 0;
            $this->level = 1;
        }
        $this->categoryList = D('ArticleCategory')->where(array('is_delete' => 0))->getList($this->pid);
        $this->display();
    }
    
    public function add() {
        if (IS_POST) {
            $result = D('ArticleCategory')->addCategory();       
            if ($result === false) {
                $error = D('ArticleCategory')->getModelError();
                $this->error($error);
            } else {
                $this->success('记录增加成功');
            }
        }
    }
    
    public function edit() {
        if (!IS_POST) {
            $catInfo = D('ArticleCategory')->getCatInfo(I('get.cat_id',0,'intval'));
            if (empty($catInfo)) {
                $this->error('记录不存在');
            } else {
                $this->success($catInfo);
            }
        } else {
            $this->result = D('ArticleCategory')->editCategory();       
            if ($this->result === false) {
                $this->error('添加失败');
            } else {
                $this->success('记录增加成功');
            }
        }
    } 
    public function del() {
        $cat_id = I('post.cat_id',0,'intval');
        $child_num = D('ArticleCategory')->where(array('parent_id'=>$cat_id))->count();
        if ($child_num) {
            $this->error('有子分类不能删除');
        } else {
            $catInfo = D('ArticleCategory')->field('cat_id,level')->where(array('cat_id'=>$cat_id))->find();
            if($catInfo['level'] == 1){
                $this->error('不能删除顶级分类');
            }
            $article_num = M('Article')->where(array('cat_id' =>$cat_id))->count();
            if ($article_num) {
                $this->error('分类存在文章,不能直接删除');
            }            
            
            $result = D('ArticleCategory')->where(array('cat_id'=>$cat_id))->delete();
            if(empty($result))
            {
                admin_log("文章分类删除失败");
                $this->error('未知错误');
            }else
            {
               admin_log("文章分类删除成功");
               $this->success('删除成功'); 
            }
        }
    }
 
    public function AttrManage() {
        $act = I('request.act','list');
        $catId = I('request.cat_id',0,'intval');
        
        if($act == 'getAttr'){
            $attr_id = I('get.attr_id',0,'intval');
            $this->success(M('ArticleAttr')->find($attr_id));
        }        
        if($act == 'del'){
            $attr_id = I('post.attr_id',0,'intval');
            $count = M('ArticleAttrValue')->where(array('attr_id' => $attr_id))->count();
            if(!empty($count)){
                $this->error('有属性值不能直接删除');
            }else{
                $result = M('ArticleAttr')->delete($attr_id);
                if($result === false){
                    $this->error('删除失败');
                }else{
                    $this->success('删除成功');
                }                
            }

            
        } 
        if($act == 'edit'){
            $attr_id = I('request.attr_id',0,'intval');
            $data = array(
                'attr_desc' => I('post.attr_desc',''),
                'attr_type' => I('post.attr_type',0,'intval'),
                'status' => I('post.status',0,'intval'),
                'sort' => I('post.sort',0,'intval'),
            );
            $result = M('ArticleAttr')->where(array('attr_id' => $attr_id))->save($data);
            if($result === false){
                $this->error('属性编辑失败');
            }else{
                $this->success('属性编辑成功');
            }
        }
        
        $catInfo = M('ArticleCategory')->field('cat_id,cat_name')->where(array('cat_id'=>$catId))->find();
        if(empty($catInfo)){
            $this->error('分类不存在');
        }
        $this->catId = $catId;
        $this->catInfo = $catInfo;
        if($act == 'list'){
            $this->attrList = M('ArticleAttr')->where(array('article_cat_id' => $catId))->select();
            $this->display('ArticleAttrList');
        }
        if($act == 'add'){
            $data = array(
                'article_cat_id' => $catId,
                'attr_desc' => I('post.attr_desc',''),
                'attr_type' => I('post.attr_type',0,'intval'),
                'status' => I('post.status',0,'intval'),
                'sort' => I('post.sort',0,'intval'),
            );
            $result = M('ArticleAttr')->add($data);
            if($result === false){
                $this->error('属性添加失败');
            }else{
                $this->success('属性添加成功');
            }
        }           
    }

    
    public function AttrValueManage() {
        $act = I('request.act','list');
        $attr_id = I('request.attr_id',0,'intval');
        $attr_value_id = I('request.attr_value_id',0,'intval');
        if($act == 'getValueInfo'){
            $this->success(M('ArticleAttrValue')->find($attr_value_id));
        }
        if($act == 'del'){
                $result = M('ArticleAttrValue')->delete($attr_value_id);
                if($result === false){
                    $this->error('删除失败');
                }else{
                    $this->success('删除成功');
                }                
        } 
        if($act == 'edit'){
            $data = array(
                'attr_value' => I('post.attr_value',''),
                'status' => I('post.status',0,'intval'),
                'sort' => I('post.sort',0,'intval'),
            );
            $result = M('ArticleAttrValue')->where(array('attr_value_id' => $attr_value_id))->save($data);
            if($result === false){
                $this->error('属性值编辑失败');
            }else{
                $this->success('属性值编辑成功');
            }
        }
        if($act == 'list'){
            $attrInfo = M('ArticleAttr')->where(array('attr_id' => $attr_id))->find();
            if(empty($attrInfo)){
                $this->error("属性不存在");
            }else{
                $this->attrInfo = $attrInfo;
                $this->attrValueList = M('ArticleAttrValue')->where(array('attr_id' => $attr_id))->select();
                $this->display('ArticleAttrValueList');                
            }

        }
        if($act == 'add'){
            $attrInfo = M('ArticleAttr')->where(array('attr_id' => $attr_id))->find();
            if(empty($attrInfo)){
                $this->error("属性不存在");
            }else{
                $data = array(
                    'attr_id' => $attr_id,
                    'attr_value' => I('post.attr_value',''),
                    'status' => I('post.status',0,'intval'),
                    'sort' => I('post.sort',0,'intval'),
                );
                $result = M('ArticleAttrValue')->add($data);
                if($result === false){
                    $this->error('属性添加失败');
                }else{
                    $this->success('属性添加成功');
                }              
            }            
        }           
    }    
    
}
