<?php
namespace Admin\Model;
class ArticleCategoryModel extends CommonModel {
    
    protected $fields = array('cat_id', 'cat_name', 'cat_decs', 'cat_keywords', 'status','sort','parent_id','level','is_delete',
            '_type'=>array(
                'cat_id'=>'smallint(5) unsigned',
                'cat_name'=>'varchar(100) []',
                'cat_decs'=>'varchar(255) []',
                'cat_keywords'=>'varchar(255) []',  
                'status'=>'tinyint(1) unsigned [1]',  
                'sort'=>'smallint(5) unsigned [100]',  
                'parent_id'=>'smallint(5) unsigned [0]',  
                'level'=>'smallint(5) unsigned [1]',  
                'is_delete'=>'tinyint(1) unsigned [0]', 
            ),
        );
    protected $pk = 'cat_id';
    protected $_validate = array(
        array('cat_name', 'require', '文章分类名称不能为空！'),
    );
    public function getList($pid = 0) {
        $where = array(
            'parent_id'=>$pid,
        );
        return $this->where($where)->order("sort desc")->select();
    }
    
    public function addCategory()
    {
        $parent_id = I('post.parent_id',0,'intval');
        if($parent_id == 0){
          $level  = 1;  
        }else{
            $level = $this->where(array('cat_id'=>$parent_id))->getField('level');
            if($level > 2){
                $this->error = '分类只支持3级！';
                return false;
            } 
            $level = $level + 1;
        }

        $data = array(
            'cat_name' => I('post.cat_name','',''),
            'cat_decs' => I('post.cat_decs','',''),
            'cat_keywords' => I('post.cat_keywords','',''),
            'status' => I('post.status',0,'intval')==0?0:1,
            'sort' => I('post.sort',0,'intval'),
            'parent_id' => I('post.parent_id',0,'intval'),
            'level' => $level,
        );
        if (!$this->create($data, self::MODEL_INSERT)) {
            admin_log("文章分类增加失败");
            return false;
        } else {
            $result = $this->add();
            $data['cat_id'] = $result;
            admin_log("文章分类增加成功",json_encode($data));
            return $result;
        }
    }

    public function editCategory()
    {
        $data = array(
            'cat_name' => I('post.cat_name','','htmlspecialchars'),
            'cat_decs' => I('post.cat_decs','','htmlspecialchars'),
            'cat_keywords' => I('post.cat_keywords','','htmlspecialchars'),
            'status' => I('post.status',0,'intval')==0?0:1,
            'sort' => I('post.sort',0,'intval'),
        );
        
        if (!$this->create($data, self::MODEL_UPDATE)) {
            admin_log("文章分类编辑失败");
            return false;
        } else {
            $result = $this->where(array('cat_id'=>intval(I('post.cat_id'))))->save();
            $data['cat_id'] = intval(I('post.cat_id'));
            admin_log("文章分类编辑成功",json_encode($data));
            return $result;
        }
    }
    
    public function getCatInfo($cat_id)
    {
        return $this->where(array('cat_id' => $cat_id))->find();
    }
    
    public function getAllCat($level = 1) {
        $order = array(
            'level' => 'desc',
            'sort' => 'desc',
        );
        $where = array();
        if($level > 0){
            $where['level'] = array('ELT',$level);
        }
        $category_arr = $this->where($where)->order($order)->getField('cat_id,cat_name,level,parent_id');
        foreach($category_arr as $key => &$cat_info){
            if($cat_info['parent_id'] != 0){
                $category_arr[$cat_info['parent_id']]['children'][$cat_info['cat_id']] = $cat_info;
                unset($category_arr[$key]);
            }
        }
        return $category_arr;
    }    
    
    public function getCatAttrList($cat_id = 0) {
        $catInfo = $this->field('cat_id,cat_name,parent_id')->find($cat_id);
        if(empty($catInfo)){
            return array();
        }
        while(true){
            if($catInfo['parent_id'] != 0){
                $catInfo = $this->field('cat_id,cat_name,parent_id')->find($catInfo['parent_id']);
            }else{
                break;
            }
        }
        $attrList = M('ArticleAttr')->field('attr_id,attr_desc,attr_type')->where(array('article_cat_id' => $catInfo['cat_id'],'status' => 1))->order('sort desc')->select();
        if(!empty($attrList)){
            foreach($attrList as &$info){
                $info['valueList'] = M('ArticleAttrValue')->field('attr_value_id,attr_value')->where(array('attr_id' => $info['attr_id'],'status' => 1))->order('sort desc')->select();
            }
            return $attrList;
        }else{
            return array();
        }
    }
}
