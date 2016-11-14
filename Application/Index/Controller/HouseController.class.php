<?php

namespace Index\Controller;

class HouseController extends CommonController {

    public function my() {
		$region = $_COOKIE['region_id'];
		$bankuai = I('get.x',0,intval);
		$taocan = I('get.t',0,intval);
		if(!empty($bankuai)){
			$where = 'status=0 AND region_id='.$region.' AND bankuai='.$bankuai;
		}else{
			$where = 'status=0 AND region_id='.$region;
		}	
		
		$count = M('loupan')->where($where)->count();
		$Page  = new \Think\Page($count,5); // 实例化分页类 传入总记录数和每页显示的记录数(25)
		$this->pageShow  = $Page->show(); // 分页显示输出
		
		$this->loupan_list=M('loupan')->field('loupan_name,loupan_price,loupan_address,loupan_id,thumb,visit_time')->where($where)->order('loupan_price desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$this->LoupanAttrValue=M('LoupanAttrValue')->field('attr_value,attr_value_id')->where('status=1 AND attr_id=0')->select();
		$this->addtime=M('Article')->field('article_id,article_name')->where('cat_id_1=4 AND cat_id_2=99')->order('add_time desc')->limit(0,10)->select();
		$this->visittime=M('Article')->field('article_id,article_name')->where('cat_id_1=4 AND cat_id_2=99')->order('visit_time desc')->limit(0,10)->select();
		
		$this->display();
    }
	
	public function detail() {
		$loupan_id = I('get.id',0,intval);
		$fenye = I('get.type');
		$region = $_COOKIE['region_id'];
		if($fenye=='1'){
			$this->tishi = '成功案例';
			$where = 'is_checked=1 AND region_id='.$region.' AND cat_id_1=49 AND loupan_id='.$loupan_id.' AND is_delete=0';
			$count = M('Article')->where($where)->count();
			$Page  = new \Think\Page($count,4); // 实例化分页类 传入总记录数和每页显示的记录数(25)
			$this->pageShow  = $Page->show(); // 分页显示输出
			
			$this->zhuanti = M('Article')->field('article_id,article_name,main_image,user_id,article_desc,visit_time,type_desc,area')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
			
		}elseif($fenye=='2'){
			$this->tishi = '在建工程';
			$where = 'status=1 AND loupan_id='.$loupan_id.' AND region_id='.$region;
			$count = M('loupan_build')->where($where)->count();
			$Page  = new \Think\Page($count,4); // 实例化分页类 传入总记录数和每页显示的记录数(25)
			$this->pageShow  = $Page->show(); // 分页显示输出
			
			$this->zaijian = M('loupan_build')->field('id,title,desc,img,user_id,unit,visit_time,loupan_id')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		}elseif($fenye=='3'){
			$this->tishi = '小区活动';
			$where = 'is_checked=1 AND region_id='.$region.' AND is_activity=1 AND loupan_id='.$loupan_id.' AND is_delete=0';
			
			$count = M('tender')->where($where)->count();
			$Page  = new \Think\Page($count,6); // 实例化分页类 传入总记录数和每页显示的记录数(25)
			$this->pageShow  = $Page->show(); // 分页显示输出
			
			$this->zaijian = M('tender')->field('tender_id,add_time,closing_time,desc,bidder_num')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		}
		
		$where = 'status=0 AND loupan_id='.$loupan_id;
		$loupan = M('loupan')->field('loupan_id,loupan_name,loupan_price,loupan_address,loupan_id,type_id,thumb,visit_time')->where($where)->find();
		
		$this->Loupanlist = M('LoupanAttrValue')->field('attr_value,attr_value_id')->where('status=1 AND attr_value_id='.$loupan['type_id'])->find();
        $this->loupan = $loupan;
		
		$this->display();
    }  
	
	 public function joinajax() {
		$id = I('post.id',0,intval);
		$lid = I('post.lid',0,intval);
		$uid = I('post.uid',0,intval);
		$data['name'] = I('post.concat','','htmlspecialchars');
		$data['mobile'] = I('post.mobile');
		$data['bao'] = I('post.bao');
		
		$num = M('Tender')->field('people_num')->where('tender_id='.$id.' AND loupan_id='.$lid)->find();
		
		$count = M('Tender_activity')->where('tender_id='.$id.' AND loupan_id='.$lid)->count();
		if(($count+1) > $num['people_num']){
			$error = '参加人数已满！';
			$this->ajaxReturn($error);
			die;
		}else{
			$data['tender_id'] = $id;
			$data['user_id'] = $uid;
			$data['loupan_id'] = $lid;
			$saveok = M('Tender_activity')->add($data);
			if($saveok){
				$ok = '报名成功！';
				$this->ajaxReturn($ok);
				die;
			}else{
				$error = '报名失败！';
				$this->ajaxReturn($error);
				die;
			}
		}
    } 
	
    public function design() {
		$this->lid = I('get.lid',0,intval);
		$this->id = I('get.id',0,intval);
		$where = 'status=0 AND loupan_id='.$this->lid;
		$this->loupan = M('loupan')->field('loupan_name')->where($where)->find();
		
		$where = 'is_checked=1 AND article_id='.$this->id.' AND cat_id_1=49 AND is_delete=0';
		$programme = M('Article')->field('article_name,add_time,main_image,user_id,article_desc,visit_time,type_desc,area')->where($where)->find();
        
		$this->user = M('Users')->field('user_id,user_name,avatar,service_idea,case_num,is_verified')->where('user_id='.$programme['user_id'])->find();
		$this->photo = M('Article_photo')->field('url,img_desc')->where('article_id='.$this->id)->select();
		
		$this->region_id = $_COOKIE['region_id'];
		$this->city_id = M('Region')->where(array('region_id' => $this->region_id))->getField('parent_id');
        $this->province_id = M('Region')->where(array('region_id' => $this->city_id))->getField('parent_id');       

		$this->programme = $programme;
		$this->display();
    }
	
	public function engineering() {
		$this->lid = I('get.lid',0,intval);
		$this->id = I('get.id',0,intval);
		$this->region_id = $_COOKIE['region_id'];
		$where = 'status=0 AND loupan_id='.$this->lid;
		$this->loupan = M('loupan')->field('loupan_name')->where($where)->find();
		
		$where = 'status=1 AND id='.$this->id.' AND region_id='.$this->region_id;
		$this->zaijian = M('loupan_build')->field('title,desc,img,user_id,visit_time,attr_id,area')->where($where)->find();
		
		$this->user = M('Users')->field('user_id,user_name,avatar,service_idea,case_num,is_verified')->where('user_id='.$this->zaijian['user_id'])->find();
		$this->city_id = M('Region')->where(array('region_id' => $this->region_id))->getField('parent_id');
        $this->province_id = M('Region')->where(array('region_id' => $this->city_id))->getField('parent_id');       
		
		$this->jushi = M('Article_attr_value')->field('attr_value')->where('attr_value_id='.$this->zaijian['attr_id'])->find();
		$this->madelist = M('LoupanBuildMade')->field('bid,made_name,made_desc,img')->where(array('bid'=>$this->id))->select();
		$this->madecount = M('LoupanBuildMade')->where(array('bid'=>$this->id))->count();
		//print_r($this->madecount);
        $this->display();
    }
	
	public function activity() {
		$this->lid = I('get.lid',0,intval);
		$this->id = I('get.id',0,intval);
		$where = 'status=0 AND loupan_id='.$this->lid;
		$this->loupan = M('loupan')->field('loupan_name')->where($where)->find();
		
		$wheret = 'is_checked=1 AND is_activity=1 AND tender_id='.$this->id;
		$this->tenderz = M('tender')->field('tender_name,add_time,closing_time,order_price,type')->where($wheret)->find();
		$this->tendert = M('tender_attachment')->field('url')->where('tender_id='.$this->id)->find();
       
	    $this->actibao = M('tender_mate')->field('qinggong,img,imgb,pinpai,price')->where('tender_id='.$this->id.' AND bao_id='.$this->tenderz['type'])->select();
		
		$this->display();
    }
	public function mianji(){
		$id = I('post.id',0,intval);
		$region = I('post.region',0,intval);
		if($id==1 || $id==2 || $id==3 || $id==4 || $id==5 || $id==6 || $id==7 || $id==8){
			$field = 'mianji='.$id;
		}
		if($id==11){
			$id=1; $field = 'taocan='.$id;
		}elseif($id==22){
			$id=2; $field = 'taocan='.$id;
		}elseif($id==33){
			$id=3; $field = 'taocan='.$id;
		}elseif($id==44){
			$id=4; $field = 'taocan='.$id;
		}elseif($id==55){
			$id=5; $field = 'taocan='.$id;
		}
		$time = time();
		$where = 'is_checked=1 AND region_id='.$region.' AND is_activity=1 AND '.$field .' AND add_time<'.$time.' AND closing_time>'.$time;	
		$zaijian = M('Tender')->join('pgw_tender_attachment ON pgw_tender.tender_id = pgw_tender_attachment.tender_id')->where($where)->limit(0,12)->select();
	
		$this->ajaxReturn($zaijian);
	}
	
	public function manli(){
		$this->display();
	}
	
	public function mactivity(){
		$this->display();
	}
	
    public function fenzhan() {
			
        $this->display();
    }
	
    public function zhaoshang() {
	
        $this->display();
    }
}
