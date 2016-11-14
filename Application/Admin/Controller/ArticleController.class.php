<?php

namespace Admin\Controller;

class ArticleController extends CommonController {

    public function index() {
        $where = array(
            'a.is_delete' => 0,
        );                $where['a.cat_id_1']  = array('neq',51);//排除问题回答列表
        //筛选
        $filter = array(
            'fields_name' => array(
                'article_name' => array(
                    'desc'=>'文章名',
                    'type' => "input",
                    'match_type' => 'like',
                ),
                'is_checked' => array(
                    'desc'=>'审核状态',
                    'type' => "select",
                     'values' => array('-1'=>"审核状态",'0'=>"未审核",'1'=>"已审核"),
                     'match_type' => 'eq',
                     'exclude' => array(-1),
                ),                
                 'cat_id' => array(
                    'desc'=>'文章分类',
                    'type' => "select",
                     'values' => '',
                     'match_type' => 'eq',
                ),               
            ),
            'fields_alias' => array(
                'article_name' => 'a',
                'cat_id' => 'a',
                'is_checked' => 'a',
            ),
            'fields_value' => array(
                'article_name' => '',
                'cat_id' => '',
                'is_checked' => '-1',
            ),
            'order' => array(
                "fields" => array(
                    'article_id' => '文章id',
                    'article_name' => '文章名',                    
                ),
                'orderby' => 'article_id',
                'sortby' => 'desc',
            ),
        );
        $catList = D('ArticleCategory')->getAllCat(3);
        array_unshift($catList,array('cat_id'=> '' ,'cat_name'=>"所有分类" ,'level'=>"1" ,'parent_id'=>"0"));
        array_unshift($catList,array('cat_id'=> '0' ,'cat_name'=>"未分类",'level'=>"1" ,'parent_id'=>"0"));
        $filter['fields_name']['cat_id']['values'] = $catList;        
        foreach($filter['fields_name'] as $name => $info){
            $value =  I('get.filter_'.$name,'',  'htmlspecialchars');
            if($value !== ''){
                if(isset($info['exclude']) && in_array($value,$info['exclude'])){
                    continue;
                }                
                $filter['fields_value'][$name] = $value;
                if(isset($filter['fields_alias'][$name]) && !empty($filter['fields_alias'][$name])){
                    if($info['match_type'] == 'like'){
                        $where[$filter['fields_alias'][$name].".".$name] = array('like','%'.$value.'%');
                    }else{
                        if($name == 'cat_id' && $value != 0){
                            $catLevel = M('ArticleCategory')->field('level')->where(array('cat_id' => $value))->find();
                            $where[$filter['fields_alias'][$name].".".$name.'_'.$catLevel['level']] = $value;
                        }else{
                            $where[$filter['fields_alias'][$name].".".$name] = $value;
                        }
                        
                    }                    
                }else{
                    if($info['match_type'] == 'like'){
                        $where[$name] = array('like','%'.$value.'%');
                    }else{
                        if($name == 'cat_id' && $value != 0){
                            $catLevel = M('ArticleCategory')->field('level')->where(array('cat_id' => $value))->find();
                            $where[$name.'_'.$catLevel['level']] = $value;
                        }else{
                            $where[$name] = $value;
                        }                        
                    }                    
                }
            }
        }
        $order = array();
        $orerby = I('get.orderby','','htmlspecialchars');
        $sortby = I('get.sortby','','htmlspecialchars');
        if(array_key_exists($orerby, $filter['order']['fields'])){
            $filter['order']['orderby'] = $orerby;
        }
        if(in_array(strtolower($sortby), array('desc','asc'))){
            $filter['order']['sortby'] = strtolower($sortby);
        }
        $order[$filter['order']['orderby']] = $filter['order']['sortby'];
        //筛选
        
        $page = new \Com\Logdd\Page(D('Article')->alias('a')->where($where)->count(), C('PAGE_SIZE'));
        $this->pageInfo = $page->getPageInfo();
        $this->articleList = D('Article')->field('a.article_id,a.article_name,a.article_keywords,a.add_time,a.sort,c.cat_name,a.is_checked,r.region_name')
                        ->alias('a')->join('left join __ARTICLE_CATEGORY__ as c on a.cat_id = c.cat_id')                                                ->join('left join __REGION__ as r on a.region_id = r.region_id')
                        ->where($where)->getList($page->firstRow, $page->listRows,$order);
        
        $this->filter = $filter;
        session("goUrl.ArticleListUrl",get_page_url());
        $this->display();
    }

    public function add() {
        if (IS_POST) {
            $act = I('post.act','');
            if($act == 'getAttr'){
                $cat_id = I('post.cat_id',0,'intval');
                $this->success(D('ArticleCategory')->getCatAttrList($cat_id));
                exit();
            }else{
                $result = D('Article')->addArticleInfo();
                if (($result === false)) {
                    $this->error(D('Article')->getModelError());
                } else {
                    $go_url = U('Article/index');
                    if(session("goUrl.ArticleListUrl")){
                        $go_url = htmlentities(session("goUrl.ArticleListUrl"));
                    }
                    $this->success('编辑成功', $go_url);
                }                
            }
        } else {
            $catList = D('ArticleCategory')->getAllCat(3);
            array_unshift($catList,array('cat_id'=> '0' ,'cat_name'=>"无分类",'level'=>"1" ,'parent_id'=>"0"));  
            $this->catId = 0;
            $this->attrInfo = json_encode(array());
            $this->catList = $catList;
            $regionList = D('Region')->getAllRegion(3);
            
            array_unshift($regionList,array('region_id'=> '0' ,'region_name'=>"总站",'parent_id'=>"0",'level'=>"0"));  
            $this->regionList = $regionList;
            
            $this->display();
        }
    }

    public function edit() {
        if (IS_POST) {
            
            $act = I('post.act','');
            if($act == 'getAttr'){
                $cat_id = I('post.cat_id',0,'intval');
                $this->success(D('ArticleCategory')->getCatAttrList($cat_id));
                exit();
            }else{
                $result = D('Article')->editArticleInfo();
                if (($result === false)) {
                    $this->error(D('Article')->getModelError());
                } else {
                    $go_url = U('Article/index');
                    if(session("goUrl.ArticleListUrl")){
                        $go_url = htmlentities(session("goUrl.ArticleListUrl"));
                    }
                    $this->success('编辑成功', $go_url);
                }              
            }            
            
        } else {
            $article_id = I('get.article_id', 0, 'intval');
            $this->articleInfo = D('Article')->where(array('is_delete' => 0))->getArticleInfo($article_id);
            if (empty($this->articleInfo)) {
                $this->error('文章不存在');
            }
            $catList = D('ArticleCategory')->getAllCat(3);
            array_unshift($catList,array('cat_id'=> '0' ,'cat_name'=>"无分类",'level'=>"1" ,'parent_id'=>"0"));  
            $this->catList = $catList;
            $regionList = D('Region')->getAllRegion(3);
            array_unshift($regionList,array('region_id'=> '0' ,'region_name'=>"总站",'parent_id'=>"0",'level'=>"0"));  
            $this->regionList = $regionList;
            $this->attrInfo = json_encode(M('ArticleAttrInfo')->field('attr_id,attr_value_id')->where(array('article_id' => $article_id))->select());
            $this->gallery = M('ArticlePhoto')->where(array('article_id' => $article_id))->order('sort desc')->select();            
            $this->display();
        }
    }

    public function del() {
        $article_id = I('post.article_id', 0, 'intval');
        D('Article')->data(array('is_delete' => 1))->where(array('article_id' => $article_id))->save();
        M('ArticlePhoto')->where(array('article_id' => $article_id))->save(array('article_id' => 0));
        admin_log("文章删除成功");
        $this->success('删除成功');
    }

    public function changeCheckedStatus(){
        $article_ids = I('post.article_ids', array(), 'intval');
        $article_ids = array_unique($article_ids);
        $status = I('post.status', 0, 'intval');
        if(!empty($article_ids)){
            $where = array(
                'article_id' => array('in', $article_ids),
            );
            
            //赠送积分 审核的赠送积分并清零预分配积分
            $preList = M('Article')->field('article_id,user_id,cat_id_1,pre_integral,article_name')
                    ->where(array('user_id' => array('neq',0) ,'article_id' => array('in', $article_ids),'pre_integral' => array('gt',0)))->select();
            foreach($preList as $info){
                if($info['cat_id_1'] == 49){
                    $change_desc = '写案例赠送';
                    $change_type = 3;
                }elseif($info['cat_id_1'] == 51){
                    $change_desc = '写日志赠送';
                    $change_type = 4;
                }
                $data = array(
                    'user_id' => $info['user_id'],
                    'change_type' => $change_type,
                    'change_desc' => $change_desc,
                    'change_time' => gmtime(),
                    'id' => $info['article_id'],
                    'integral' => $info['pre_integral'],
                    'in_out' => 0,
                );
                M('IntegralLog')->add($data);
                M('Article')->where(array('article_id' => $info['article_id']))->save(array('pre_integral' => 0));
            }
            //赠送积分
            
            M('Article')->where($where)->data(array('is_checked' => $status))->save();
            admin_log("修改审核状态为".$status,  json_encode($article_ids));
            $this->success('审核状态修改成功');             
        }else{
            $this->error('未选择文章');  
        }

    }
}
