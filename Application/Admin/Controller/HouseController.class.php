<?php
namespace Admin\Controller;
use Think\Auth;
class HouseController extends CommonController { 
    public function index(){
        $where=array();
        $this->loupan_name=$loupan_name=I("get.p_name","");
        if(!empty($loupan_name)){
        $where['loupan_name']= array('like','%'.$loupan_name.'%');
        }
        $page = new \Com\Logdd\Page(D('Loupan')->alias('u')->where($where)->count(),C('PAGE_SIZE') );
        $this->pageInfo = $page->getPageInfo();     
        $loupan_list = M('Loupan')->alias('l')->field('l.loupan_id,l.region_id,l.loupan_name,l.loupan_price,l.loupan_address,l.visit_time,l.status,l.add_time,l.open_time,l.finish_time,l.developer,wt.attr_value as type,u.region_name,w.attr_value as price')
        ->join('left join __LOUPAN_ATTR_VALUE__ as wt on l.type_id = wt.attr_value_id ')
        ->join('left join __LOUPAN_ATTR_VALUE__ as w on l.price_id = w.attr_value_id ')
        ->join('left join __REGION__ as u on l.region_id = u.region_id')->where($where)
        ->limit($page->firstRow, $page->listRows)->order("add_time desc")->select();
      
        $this->loupan_list=$loupan_list;				//print_r($this->loupan_list);exit;
        $this->display();
    }
    public function add(){
        $attrList = M('LoupanAttr')->field('attr_id,attr_desc,attr_type')->where(array('article_cat_id' => 5,'status' => 1))->order('sort desc')->select();
        // var_Dump($attrList);die();
        if(!empty($attrList)){
            foreach($attrList as $info){
                if(isset($attr[$info['attr_id']])){
                    $is_exist = M('LoupanAttrValue')->where(array('attr_id' => $info['attr_id'],'attr_value_id' =>$attr[$info['attr_id']] ,'status' => 1))->order('sort desc')->count();
                     
                    if($is_exist){
                        $actualAttr[$info['attr_id']] = $attr[$info['attr_id']];
                         
                    }
                }
            }
       
            foreach($attrList as &$info){
                $info['valueList'] = M('LoupanAttrValue')->field('attr_value_id,attr_value')->where(array('attr_id' => $info['attr_id'],'status' => 1))->order('sort desc')->select();
                array_unshift($info['valueList'],array('attr_value_id' => 0,'attr_value' => '不选'));
                $tempAttr = $actualAttr;
                if(isset($tempAttr[$info['attr_id']])){
                    unset($tempAttr[$info['attr_id']]);
                }
                foreach($info['valueList'] as &$value){
                    if((isset($actualAttr[$info['attr_id']]) && $actualAttr[$info['attr_id']] == $value['attr_value_id']) || (!isset($actualAttr[$info['attr_id']]) && $value['attr_value_id'] == 0)){
                        $value['is_current'] = 1;
                    }else{
                        $value['is_current'] = 0;
                        $tempAttr[$info['attr_id']] = $value['attr_value_id'];
                        $temp_str = '';
                        foreach($tempAttr as $k => $v){
                            $temp_str.='_'.$k.'_'.$v;
                        }
                        $value['url'] = U('House/my?attr='.ltrim($temp_str,'_'));
                    }
                }
            }
            unset($tempAttr);
        }		
        $this->attrList = $attrList[0];     
        $this->display();
    }
    
    public function add_ok(){
        $data=array();
        $data['region_id']=I('post.region_id','');
        $data['loupan_name']=I('post.loupan_name','');
        $data['loupan_price']=I('post.loupan_price','');
        $data['finish_time']=strtotime(I('post.finish_time',''));
        $data['open_time']=strtotime(I('post.open_time',''));
        $data['visit_time']=I('post.visit_time','');
        $data['type_id']=isset($_POST['user_type'][0])?$_POST['user_type'][0]:'';				$data['loupan_address']=I('post.loupan_address','');		$data['bankuai']=I('post.bankuai',0,intval);	
        $data['add_time']=time();				$upload = new \Think\Upload();// 实例化上传类				$upload->maxSize   =     333145728 ;// 设置附件上传大小		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型		$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录		$upload->savePath  =     '/'; // 设置附件上传（子）目录		// 上传文件 		$info   =   $upload->upload(); 				$data['thumb'] = '/Uploads'.$info['loupan_thumb']['savepath'].$info['loupan_thumb']['savename'];
        if(empty($data['region_id'])){
            $this->error('请选择地区');
        }
        if(empty($data['loupan_name'])){
            $this->error('请填写楼盘名称');
        }
        if(empty($data['loupan_price'])){
            $this->error('请填写施工均价');
        }
        if(empty($data['open_time'])){
            $this->error('请选择交房时间');
        }
        if(empty($data['finish_time'])){
            $this->error('请选择开盘时间');
        }
        if(empty($data['loupan_address'])){
            $this->error('请填写楼盘地址');
        }
        
         if(M('Loupan')->add($data)){
             $go_url=U('House/index');
             $this->success('添加成功', $go_url);
         }else{
             $this->error('添加失败');
         }
    }
   
    public function edit(){
        $loupan_id=I('get.loupan_id','');
        $this->region_id=$region_id=I('get.region_id','');
        $this->city_id = M('Region')->where(array(
            'region_id' => $region_id
        ))->getField('parent_id');
        $this->province_id = M('Region')->where(array(
            'region_id' => $this->city_id
        ))->getField('parent_id');
        $list= M('Loupan')->where(array('loupan_id'=>$loupan_id))->find($loupan_id);
        $this->list=$list;
       //var_Dump($list);die();
        $attrList = M('LoupanAttr')->field('attr_id,attr_desc,attr_type')->where(array('article_cat_id' => 5,'status' => 1))->order('sort desc')->select();
        // var_Dump($attrList);die();
        if(!empty($attrList)){
            foreach($attrList as $info){
                if(isset($attr[$info['attr_id']])){
                    $is_exist = M('LoupanAttrValue')->where(array('attr_id' => $info['attr_id'],'attr_value_id' =>$attr[$info['attr_id']] ,'status' => 1))->order('sort desc')->count();
                     
                    if($is_exist){
                        $actualAttr[$info['attr_id']] = $attr[$info['attr_id']];
                         
                    }
                }
            }
        
            foreach($attrList as &$info){
                $info['valueList'] = M('LoupanAttrValue')->field('attr_value_id,attr_value')->where(array('attr_id' => $info['attr_id'],'status' => 1))->order('sort desc')->select();
                array_unshift($info['valueList'],array('attr_value_id' => 0,'attr_value' => '不选'));
                $tempAttr = $actualAttr;
                if(isset($tempAttr[$info['attr_id']])){
                    unset($tempAttr[$info['attr_id']]);
                }
                foreach($info['valueList'] as &$value){
                    if((isset($actualAttr[$info['attr_id']]) && $actualAttr[$info['attr_id']] == $value['attr_value_id']) || (!isset($actualAttr[$info['attr_id']]) && $value['attr_value_id'] == 0)){
                        $value['is_current'] = 1;
                    }else{
                        $value['is_current'] = 0;
                        $tempAttr[$info['attr_id']] = $value['attr_value_id'];
                        $temp_str = '';
                        foreach($tempAttr as $k => $v){
                            $temp_str.='_'.$k.'_'.$v;
                        }
                        $value['url'] = U('House/my?attr='.ltrim($temp_str,'_'));
                    }
                }
            }
            unset($tempAttr);
        }
        $this->attrList = $attrList[0];
        $this->display();
    }
    
    public function edit_ok(){
        $data=array();
        $loupan_id=I('post.loupan_id','');
        $data['region_id']=I('post.region_id','');
        $data['loupan_name']=I('post.loupan_name','');
        $data['loupan_price']=I('post.loupan_price','');
        $data['finish_time']=strtotime(I('post.finish_time',''));
        $data['open_time']=strtotime(I('post.open_time',''));
        $data['visit_time']=I('post.visit_time','');
        $data['type_id']=isset($_POST['user_type'][0])?$_POST['user_type'][0]:'';
        $data['loupan_address']=I('post.loupan_address','');		$data['bankuai']=I('post.bankuai',0,intval);
        $upload = new \Think\Upload();// 实例化上传类				$upload->maxSize   =     333145728 ;// 设置附件上传大小		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型		$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录		$upload->savePath  =     '/'; // 设置附件上传（子）目录		// 上传文件 		$info   =   $upload->upload();				$data['thumb'] = '/Uploads'.$info['loupan_thumb']['savepath'].$info['loupan_thumb']['savename'];				$tupian = M('Loupan')->field('thumb')->where('loupan_id='.$loupan_id)->find();		if(!empty($tupian['thumb'])){			unlink($tupian['thumb']);		}		
        if(empty($data['region_id'])){
            $this->error('请选择地区');
        }
        if(empty($data['loupan_name'])){
            $this->error('请填写楼盘名称');
        }
        if(empty($data['loupan_price'])){
            $this->error('请填写施工均价');
        }
        if(empty($data['open_time'])){
            $this->error('请选择交房时间');

        }
        if(empty($data['finish_time'])){
            $this->error('请选择开盘时间');
        }
        if(empty($data['loupan_address'])){
            $this->error('请填写楼盘地址');
        }
        if(M('Loupan')->where(array('loupan_id'=>$loupan_id))->save($data)){
            $go_url=U('House/index');			
            $this->success('修改成功', $go_url);
        }else{
            $this->error('修改失败');
        }
    
    }
    
    public function del(){
        $loupan_id = I('post.loupan_id', 0, 'intval');
        		$tupian = M('Loupan')->field('thumb')->where('loupan_id='.$loupan_id)->find();		if(!empty($tupian['thumb'])){			unlink($tupian['thumb']);		}
        D('loupan')->where(array('loupan_id' => $loupan_id))->delete();
       M('LoupanPhoto')->where(array('loupan_id' => $loupan_id))->delete();
        admin_log("楼盘删除成功");
        $this->success('删除成功');
    }
    
 //楼盘审核
    public function review(){
        $loupan_id = I('post.loupan_id', 0, 'intval');
        $status=I('post.status', 0, 'intval');
        if($status==1){
            $data['status']=0;
        }else{
            $data['status']=1;
        }
        D('loupan')->where(array('loupan_id' => $loupan_id))->save($data);
    
        if($status==1){
            $result= M('Loupan')->alias('l')->field('l.loupan_id,wt.attr_id ,wt.attr_value_id')
            ->join('left join __LOUPAN_ATTR_VALUE__ as wt on l.type_id = wt.attr_value_id || l.price_id= wt.attr_value_id')
            ->where(array('loupan_id'=> $loupan_id ))->select();
            //var_dump($result);
            foreach($result as $v){
    
                $data['loupan_id']=$v['loupan_id'];
                $data['attr_id']=$v['attr_id'];
                $data['attr_value_id']=$v['attr_value_id'];
    
                $attrList = M('LoupanAttrInfo')->add($data);
            }
        }else{
    
            M('LoupanAttrInfo')->where(array('loupan_id'=>$loupan_id))->delete();
        }
         
        admin_log("楼盘审核通过");
    
        $this->success('审核成功');
    }
 //楼盘分类管理   
   public function classify(){
       $this->list=D('loupanAttr')->select();
       $this->display();
   } 
   public function  classifyedit(){
       $data['attr_id'] = I('post.attr_id', 0, 'intval');
       $data['attr_desc']= I('post.attr_value', '', 'strip_tags');
       $data['status'] = I('post.status', 1, 'intval');
       $data['sort'] = I('post.sort', 100, 'intval');
       //echo json_encode($data['attr_value']);
       $result=D('LoupanAttr')->where(array('attr_id' => $data['attr_id']))->save($data);
       if($result){
       
           echo json_encode('ok');
       }else{
       
           echo json_encode('no');
       }  
       
   }
  public function classifyadd(){
      
      $this->display();
  }
   //楼盘分类属性列表
   public function attrlist(){
       $this->attr_id=$attr_id=I('get.attr_id','');
       $this->list=D('loupanAttrValue')->where(array('attr_id'=>$attr_id))->select();
       $this->display();
}

   public function attradd(){
       $attr_value=I("post.attr_value","");
       
       $attr_id=I("get.attr_id");
       $data=array();
      if(!empty($attr_value)){
          
          $data['attr_id']=$attr_id;
          $data['attr_value']=$attr_value;
          $data['status']=1;
          $data['sort']=100;
          
          if(D('LoupanAttrValue')->add($data)){
              $go_url=U('House/attrlist');
              $this->success('添加成功', $go_url);
     
          }else{
              $this->error('添加失败');
              
          }
          
      }else{
       
       $this->display();
      }
   }

    public function attrdel(){
        $attr_value_id = I('post.attr_value_id', 0, 'intval');
        
        D('LoupanAttrValue')->where(array('attr_value_id' => $attr_value_id))->delete();
        
       // M('LoupanPhoto')->where(array('loupan_id' => $loupan_id))->delete();
        
        admin_log("楼盘删除成功");
        $this->success('删除成功',$go_url);
        
    }
    //判断楼盘分类属性启用
   public function attruse(){
       $attr_value_id = I('post.attr_value_id', 0, 'intval');
       $status=I('post.status', 0, 'intval');
       if($status==1){
           $data['status']=0;
       }else{
           $data['status']=1;
       }
       D('LoupanAttrValue')->where(array('attr_value_id' => $attr_value_id))->save($data);
       admin_log("已启用");     
       $this->success('启用成功');
   } 
  public function attredit(){
      $data['attr_value_id'] = I('post.attr_value_id', 0, 'intval');
      $data['attr_value']= I('post.attr_value', '', 'strip_tags');
      $data['status'] = I('post.status', 1, 'intval');
      $data['sort'] = I('post.sort', 100, 'intval');
      //echo json_encode($data['attr_value']);
      $result=D('LoupanAttrValue')->where(array('attr_value_id' => $data['attr_value_id']))->save($data);
      if($result){
          
         echo json_encode('ok'); 
      }else{
          
          echo json_encode('no');
      }
      //var_Dump($post);die();
  } 
  //在建工地
  public function build(){
       $where=array();
       $this->title=$title=I("get.p_name","");
      
        if(!empty($title)){
        $where['title']= array('like','%'.$title.'%');
        }
        //C('PAGE_SIZE')
        $page = new \Com\Logdd\Page(D('LoupanBuild')->alias('u')->where($where)->count(),20 );
        $this->pageInfo = $page->getPageInfo();
      $this->list=M('LoupanBuild')->alias('a')->field('a.*,wt.loupan_name,w.attr_value,u.user_name')
      ->join('left join __LOUPAN__ as wt on a.loupan_id = wt.loupan_id')
      ->join('left join __ARTICLE_ATTR_VALUE__ as w on a.attr_id = w.attr_value_id')
      ->join('left join __USERS__ as u on u.user_id = a.user_id')
      ->where($where)
      ->limit($page->firstRow, $page->listRows)->order('a.add_time desc')->select();
      //var_Dump($this->list);die();
      $this->display();
      
  }
 public function buildreview(){
     $id = I('post.id', 0, 'intval');
     $status=I('post.status', 0, 'intval');
     if($status==0){
         $data['status']=1;
     }else{
         $data['status']=0;
     }
     D('loupanBuild')->where(array('id' => $id))->save($data);
     
     admin_log("楼盘审核通过");
     
     $this->success('审核成功');
 
 }
 //在建工地删除
public function build_del(){
    
    $id = I('post.id', 0, 'intval');
    
    $res=D('LoupanBuild')->where(array('id' => $id))->delete();
     
    if($res){
        
       echo json_encode('ok'); 
    }else{
        
        echo json_encode('no');
    }
        
}
public function build_name(){
   
    $region_id=  I('get.region_id',0,'intval');
    $res1=M('loupan')->field('loupan_id,loupan_name')->where(array('region_id'=>$region_id))->select();
    echo json_encode($res1);
}
//在建工地编辑
public function build_edit(){
    if($_POST){
       // var_dump($_POST);die();
        $data=array();
        $data['id']=I('post.id','');
        $data['title']=I('post.title','');
        $data['region_id']=I('post.region_id','');
        $data['area']=I('post.area','');		$data['unit']=I('post.unit','');
        $data['attr_id']=I('post.attr_id','');
        $data['price']=I('post.price','');
        $data['loupan_id']=I('post.loupan_id','');
        $data['desc']=I('post.desc','');
        $data['add_time']=time();
        $data['user_id']=I('post.user_id','');
         $upload = new \Think\Upload();// 实例化上传类				$upload->maxSize   =     333145728 ;// 设置附件上传大小		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型		$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录		$upload->savePath  =     '/'; // 设置附件上传（子）目录		// 上传文件 		$info   =   $upload->upload();				$data['img'] = '/Uploads'.$info['img']['savepath'].$info['img']['savename'];				$tupian = M('LoupanBuild')->field('img')->where('id='.$data['id'])->find();		if(!empty($tupian['img'])){			unlink($tupian['img']);		}	
        if(empty($data['title'])){
            $this->error('标题不能为空');
        }
        if(empty($data['region_id'])){
            $this->error('请选择区域');
            die();
        }
        if(empty($data['user_id'])){
            $this->error('请选择工人');
            die();
        }
        if(empty($data['area'])){
            $this->error('请填写面积');
            die();
        }
        if(empty($data['attr_id'])){
            $this->error('请选择户型');
            die();
        }
        if(empty($data['price'])){
            $this->error('请填写价格');
            die();
        }
        if(empty($data['loupan_id'])){
            $this->error('请填写小区');
            die();
        }
        if(empty($data['desc'])){
            $this->error('描述不能为空');
            die();
        }
        //  var_dump( $data);die();
        $res=M('LoupanBuild')->where(array('id'=>$data['id']))->save($data);
        if($res){
            $go_url=U('House/build');
            $this->success('编辑成功', $go_url);
        
        }else{
        
            $this->error('编辑失败');
        
        } 
        
    }else{
    $id=I('get.id','');
    $region_id=I('get.region_id','');
    
    $this->region_id =$region_id=I('get.region_id','');
    $this->city_id = M('Region')->where(array(
        'region_id' => $region_id
    ))->getField('parent_id');
    $this->province_id = M('Region')->where(array(
        'region_id' => $this->city_id
    ))->getField('parent_id');
    $this->res=M('ArticleAttrValue')->field('attr_value_id,attr_value')->where(array('attr_id'=>3))->select();
    $this->reslist=M('LoupanBuild')->alias('a')->field('a.*,wt.user_name')
    ->join('left join __USERS__ as wt on a.user_id = wt.user_id')
    ->where(array('id'=>$id))->find();
    $this->res1=M('Loupan')->field('loupan_name,loupan_id')->where(array('region_id'=>$region_id))->select();
    
    $this->display();
    }
}
  
public function build_add(){
    $this->res=M('ArticleAttrValue')->field('attr_value_id,attr_value')->where(array('attr_id'=>3))->select();
   //var_Dump($_POST);die();
    if($_POST){
        $data=array();
        $data['title']=I('post.title','');
        $data['region_id']=I('post.region_id','');
        $data['area']=I('post.area','');		$data['unit']=I('post.unit','');
        $data['attr_id']=I('post.attr_id','');
        $data['price']=I('post.price','');
        $data['loupan_id']=I('post.loupan_id','');
        $data['desc']=I('post.desc','');
        $data['add_time']=time();
        $data['status']=0;
        $data['user_id']=I('post.user_id','');				$upload = new \Think\Upload();// 实例化上传类				$upload->maxSize   =     333145728 ;// 设置附件上传大小		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型		$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录		$upload->savePath  =     '/'; // 设置附件上传（子）目录		// 上传文件 		$info   =   $upload->upload(); 		$data['img'] = '/Uploads'.$info['img']['savepath'].$info['img']['savename'];				//print_r($data);exit;		
        if(empty($data['title'])){
            $this->error('标题不能为空');
        }
        if(empty($data['region_id'])){
            $this->error('请选择区域');
            die();
        }
        if(empty($data['area'])){
            $this->error('请填写面积');
            die();
        }
        if(empty($data['user_id'])){
            $this->error('请选择工人');
            die();
        }
        if(empty($data['attr_id'])){
            $this->error('请选择户型');
            die();
        }
        if(empty($data['price'])){
            $this->error('请填写价格');
            die();
        }
        if(empty($data['loupan_id'])){
            $this->error('请填写小区');
            die();
        }
        if(empty($data['desc'])){
            $this->error('描述不能为空');
            die();
        }
        if(M('LoupanBuild')->add($data)){
            $go_url=U('House/build');
            $this->success('添加成功', $go_url);
            die();
        } else{
            $this->error('添加失败');
            die();
        }
    }
    $this->display();
}
 
public function pop_select(){
    $region_id=  I('get.region_id',0,'intval');
    $user_id=I('get.user_id',0,'intval');
    $user_name=I('get.user_name','');
    $get_page=I('get.page',0,'');
    $where=array();
    if($region_id!=0){
      
        $where['a.region_id']= $region_id;
    }
    if($user_id!=0){
       
        $where['a.user_id']= $user_id;
    }
    if(!empty($user_name)){
        $where['user_name']= array('like','%'.$user_name.'%');
        
    }   
    $where['user_type']=1;
    $count = M('Loupan')->where($where)->count();
    if($get_page!=0){
        
       $page=$get_page; 
    }else{
    $page=1;  //第一页
    }
    $pagesize=8;   //每页条数
    $pages=ceil($count/$pagesize);//总页数
    $offset=$pagesize*($page-1);//偏移量
 
   $list=M('Users')->alias('a')->field('a.user_id,a.user_name,a.real_name,a.user_type,a.mobile,a.enter_year,a.region_id,wt.region_name')
      ->join('left join __REGION__ as wt on a.region_id = wt.region_id')
      ->where($where)
      ->limit($offset, $pagesize)->select();
     $list1['pageShow']=$this->pageShow;
     $list1['list']=$list;
     $list1['page']=$page;
    echo json_encode($list1);
      
}
//优惠活动
public function activity(){
        $where=array();
        $this->title=$title=I("get.p_name","");
        //echo $loupan_name;die();
        if(!empty($title)){
        $where['l.tender_name']= array('like','%'.$title.'%');
        }
        $where['l.is_activity']=1;
        $where['l.is_delete']=0;
        //C('PAGE_SIZE')
        $page = new \Com\Logdd\Page(M('Tender')->alias('l')->where($where)->count(),C('PAGE_SIZE') );
        $this->pageInfo = $page->getPageInfo();     
        $list = M('Tender')->alias('l')->field('l.tender_id,l.loupan_id,l.tender_name,l.area,l.order_price,l.type,l.visit_time,l.people_num,l.is_checked,l.add_time,l.closing_time,wt.attr_value,n.loupan_name,u.region_name,u.region_id,w.user_name')
        ->join('left join __ARTICLE_ATTR_VALUE__ as wt on l.attr_id = wt.attr_value_id ')
        ->join('left join __USERS__ as w on l.user_id = w.user_id ')
        ->join('left join __LOUPAN__ as n on l.loupan_id = n.loupan_id ')
        ->join('left join __REGION__ as u on n.region_id = u.region_id')
        ->where($where)
        ->limit($page->firstRow, $page->listRows)->order("add_time desc")->select();
        $this->list=$list;		
        $this->display();
}
public function activity_add(){
    $this->res=M('ArticleAttrValue')->field('attr_value_id,attr_value')->where(array('attr_id'=>3))->select();
    if($_POST){
        $data['tender_name']=I('post.tender_name','');
        $data['order_price']=I('post.order_price','');
        $data['area']=I('post.area','');
        $data['user_id']=I('post.user_id',''); 
        $data['people_num']=I('post.people_num','');
        $data['loupan_id']=I('post.loupan_id','');
        $data['attr_id']=I('post.attr_id','');
        $data['desc']=I('post.desc','');		$data['region_id']=I('post.region_id','');		$data['type'] = I('post.type',''); 		$data['fanbu'] = session('user_info.user_id'); 
        $data['is_activity']=1;
        $data['closing_time']=strtotime(I('post.closing_time',''));		$data['taocan']=I('post.taocan',0,intval);		$data['mianji']=I('post.mianji',0,intval);
        $data['add_time']=strtotime(I('post.add_time',''));				$upload = new \Think\Upload();// 实例化上传类				$upload->maxSize   =     333145728 ;// 设置附件上传大小		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型		$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录		$upload->savePath  =     '/'; // 设置附件上传（子）目录		// 上传文件 		$info   =   $upload->upload();  		$dataatt['url'] = '/Uploads'.$info['img']['savepath'].$info['img']['savename']; 		/*$image = new \Think\Image();		$image->open($dataatt['url']); 		$dataatt['path'] = '/Uploads'.$info['img']['savepath'].'asc'.$info['img']['savename'];		$image->thumb(1000,1000,\Think\Image::IMAGE_THUMB_SCALE)->save($dataatt['path']);		*/		$dataatt['path'] = $dataatt['url'];
        if(empty($data['tender_name'])){
            $this->error('标题不能为空');
        }
        if(empty($data['user_id'])){
            $this->error('请选择工人');
        }
        if(empty($data['order_price'])){
            $this->error('请填写价格');
        }
        if(empty($data['area'])){
            $this->error('请填写面积');
        }
        if($data['type']==0){
            $this->error('请选择类型');
        }
        if(empty($data['add_time'])){
            $this->error('请选择开始时间');
        }
        if(empty($data['closing_time'])){
            $this->error('请选择结束时间');
        }
        if(empty($data['desc'])){
            $this->error('请填写描述');
        }
        if(M('Tender')->add($data)){						$tender = M('Tender')->field('tender_id')->where($data)->find();						$dataatt['path'] = $dataatt['url'];			$dataatt['user_id'] = $data['user_id'];			$dataatt['tender_id'] = $tender['tender_id'];						M('Tender_attachment')->add($dataatt);			
            $go_url=U('House/activity');
            $this->success('添加成功', $go_url);
            die();
        }else{       
            $this->error('添加失败!');
        }
    }
    $this->display();
}

public function activity_del(){
    $tender_id = I('post.tender_id', 0, 'intval');
    M('Tender')->where(array('tender_id'=>$tender_id))->save(array('is_delete'=>1));	M('Tender_mate')->where(array('tender_id'=>$tender_id))->delete();	 
    admin_log("活动删除成功");
    $this->success('删除成功',$go_url);
  
}

public function activity_edit(){
    if($_POST){
        $tender_id=I('post.tender_id','');
        $data['tender_name']=I('post.tender_name','');
        $data['order_price']=I('post.order_price','');
        $data['area']=I('post.area','');
        $data['user_id']=I('post.user_id','');
        $data['people_num']=I('post.people_num','');
        $data['loupan_id']=I('post.loupan_id','');
        $data['attr_id']=I('post.attr_id','');
        $data['desc']=I('post.desc','');		$data['region_id']=I('post.region_id','');		$data['taocan']=I('post.taocan',0,intval);		$data['mianji']=I('post.mianji',0,intval);		$data['type'] = I('post.type',''); 		$data['fanbu'] = session('user_info.user_id');		
        $data['closing_time']=strtotime(I('post.closing_time',''));
        $data['add_time']=strtotime(I('post.add_time',''));				$upload = new \Think\Upload();// 实例化上传类				$upload->maxSize   =     333145728 ;// 设置附件上传大小		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型		$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录		$upload->savePath  =     '/'; // 设置附件上传（子）目录		// 上传文件 		$info   =   $upload->upload();		$datatt['url'] = '/Uploads'.$info['img']['savepath'].$info['img']['savename'];		/*		$image = new \Think\Image();		$image->open('http://58pgw.com/Uploads/2016-10-31/58170cdf97e79.jpg'); 		$datatt['path'] = '/Uploads'.$info['img']['savepath'].'ajh'.$info['img']['savename'];		$image->thumb(1000,1000,\Think\Image::IMAGE_THUMB_SCALE)->save($datatt['path']);		*/		$datatt['path'] = $datatt['url'];		$tupian = M('Tender_attachment')->field('url,path')->where('tender_id='.$tender_id)->find();		if(!empty($tupian['url'])){			unlink($tupian['url']); 			//unlink($tupian['path']);		}				M('Tender_attachment')->where(array('tender_id'=>$tender_id))->save($datatt);		
        if(empty($data['tender_name'])){
            $this->error('标题不能为空');
        }
        if(empty($data['user_id'])){
            $this->error('请选择工人');
        }
        if(empty($data['order_price'])){
            $this->error('请填写价格');
        }
        if(empty($data['area'])){
            $this->error('请填写面积');        
        }
        if($data['type']==0){
            $this->error('请选择类型');
        }
        if(empty($data['add_time'])){
            $this->error('请选择开始时间');
        }
        if(empty($data['closing_time'])){
            $this->error('请选择结束时间');
        }
        if(empty($data['desc'])){
            $this->error('请填写描述');
        }
      //var_Dump(M('Tender')->where(array('tender_id'=>$tender_id))->save($data));die();
        if(M('Tender')->where(array('tender_id'=>$tender_id))->save($data)){
            $go_url=U('House/activity');
            $this->success('编辑成功', $go_url);
            die();
        }else{
            $this->error('编辑失败!');
        }
    }else{
        $id=I('get.tender_id','');
        $region_id=I('get.region_id','');
        $this->region_id =$region_id=I('get.region_id','');
        $this->city_id = M('Region')->where(array(
        'region_id' => $region_id
        ))->getField('parent_id');
        $this->province_id = M('Region')->where(array(
             'region_id' => $this->city_id
        ))->getField('parent_id');
        $this->res=M('ArticleAttrValue')->field('attr_value_id,attr_value')->where(array('attr_id'=>3))->select();
        $this->res1=M('Loupan')->field('loupan_name,loupan_id')->where(array('region_id'=>$region_id))->select();
        $this->list = M('Tender')->alias('l')->field('l.tender_id,l.loupan_id,l.tender_name,l.people_num,l.order_price,l.area,l.type,l.visit_time,l.add_time,l.closing_time,l.desc,l.attr_id,wt.attr_value,n.loupan_name,u.region_name,u.region_id,w.user_name,w.user_id')
        ->join('left join __ARTICLE_ATTR_VALUE__ as wt on l.attr_id = wt.attr_value_id ')
        ->join('left join __USERS__ as w on l.user_id = w.user_id ')
        ->join('left join __LOUPAN__ as n on l.loupan_id = n.loupan_id ')
        ->join('left join __REGION__ as u on n.region_id = u.region_id')
        ->where(array('l.tender_id'=>$id))
        ->find();
       // var_dump( $this->list );die();
        $this->display();
    }
}
public function region(){		$this->display();}

public function activity_review(){
    $tender_id = I('post.tender_id', 0, 'intval');
    $status=I('post.is_checked', 0, 'intval');
    if($status==0){
        $data['is_checked']=1;
    }else{
        $data['is_checked']=0;
    }
    D('Tender')->where(array('tender_id' => $tender_id))->save($data);
    admin_log("楼盘审核通过");
    $this->success('审核成功');
  }
	public function activity_bao(){		$this->id = I('get.tender_id',0,intval);		$this->huodong = M('Tender')->field('tender_id,tender_name,type')->where('tender_id='.$this->id)->find();		$this->display();	}
  	public function activitybao_add(){		$data['tender_id'] = I('post.acti_name','',intval);		$data['bao_id'] = I('post.type','',intval);		$typeDetail = I('post.typeDetail');		$pinpai = I('post.pinpai');		$price = I('post.price');		$len = count($typeDetail);				$upload = new \Think\Upload();// 实例化上传类		$upload->maxSize   =     333145728 ;// 设置附件上传大小		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型		$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录		$upload->savePath  =     '/'; // 设置附件上传（子）目录 		$info   =   $upload->upload();		$len= count($info);		$arrs = array();		$arrb = array();		for($i=0;$i<$len;$i++){			if($info[$i]['key'] == 'img'){				array_push($arrs,$info[$i]['savename']);			}elseif($info[$i]['key'] == 'imgb'){				array_push($arrb,$info[$i]['savename']);			}		}				$tender_mate = M('tender_mate');		for($i=0;$i<$len;$i++){			$data['qinggong'] = $typeDetail[$i];			$data['pinpai'] = $pinpai[$i];			$data['price'] = $price[$i];			$data['img'] = '/Uploads'.$info[0]['savepath'].$arrs[$i];			$data['imgb'] = '/Uploads'.$info[0]['savepath'].$arrb[$i];			$tender_mate->add($data);		}				 $go_url=U('House/activity');         $this->success('添加成功', $go_url);	}
 
	public function activitybao_del(){		$tender_id = I('get.tender_id','',intval);		$img =M('tender_mate')->field('img,imgb')->where('tender_id='.$tender_id)->find();		if(!empty($img['img'])){			unlink('/Uploads/'.date('Y-m-d',time()).'/'.$img['img']);			unlink('/Uploads/'.date('Y-m-d',time()).'/'.$img['imgb']);		}		$zsz =M('tender_mate')->where('tender_id='.$tender_id)->delete();		$go_url=U('House/activity');        if($zsz){			$this->success('删除成功', $go_url);		}	}
}