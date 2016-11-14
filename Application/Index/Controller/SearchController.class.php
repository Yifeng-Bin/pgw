<?php
namespace Index\Controller;
class SearchController extends CommonController {
    public function worker(){
        $this->pageHeadInfo = array(
            'title' => '找工人-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );        
        $keywords = I('get.keywords','','trim');
        $keywords = I('get.keywords','','trim');
        if(empty($keywords)){
            $this->error("关键字不能为空");
        }
        $this->keywords = addslashes(htmlspecialchars($keywords));
        $where = array(
            'u.is_delete' => 0,
            'u.status' => 1,
            'u.user_type' => 1,            
            'u.region_id' => REGION_ID,
            'u.user_name' => array('like', '%'.$keywords.'%'),
        );
        $count = M('Users')->alias('u')->where($where)->count();
        $Page = new \Think\Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        //$Page->lastSuffix = false;
        $Page->setConfig('first', '首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '…%TOTAL_PAGE%');
        $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
        $Page->rollPage = 11;
        $this->pageShow = $Page->show();
        $workerList = M('Users')->alias('u')->field('u.user_id,u.avatar,u.user_name,u.real_name,u.day_price,u.enter_year,u.case_num,u.profile,u.service_idea,u.qq,u.score1,u.score2,u.score3,u.tenders_take_num')
            ->where($where)->limit($Page->firstRow.','.$Page->listRows)->order($sortby)->where($where)->select();
        foreach($workerList as &$info){
            $info['url'] = U('Worker/info?id='.$info['user_id']);
        }
        $this->workerList = $workerList;      
        $this->city_id = M('Region')->where(array('region_id' => REGION_ID))->getField('parent_id');
        $this->province_id = M('Region')->where(array('region_id' => $this->city_id))->getField('parent_id');           
        $this->display();
    }
    public function teach(){
        
        $this->pageHeadInfo = array(
            'title' => '找教学-'.C('web_name'),
            'keywords' => C('web_keywords'),
            'description' => C('web_description'),
        );
        
        $keywords = I('get.keywords','','trim');
        if(empty($keywords)){
            $this->error("关键字不能为空");
        }
        $this->keywords = addslashes(htmlspecialchars($keywords));
        vendor('aliyujCloudSearch.CloudsearchClient');
        vendor('aliyujCloudSearch.CloudsearchIndex');
        vendor('aliyujCloudSearch.CloudsearchDoc');
        vendor('aliyujCloudSearch.CloudsearchSearch');
        $access_key = C('OPEN_SEARCH_ACCESS_KEY_ID');
        $secret = C('OPEN_SEARCH_ACCESSKEY_SECRET');
        $host = "http://intranet.opensearch-cn-hangzhou.aliyuncs.com";//根据自己的应用区域选择API
        $key_type = "aliyun";  //固定值，不必修改
        $client = new \CloudsearchClient($access_key,$secret,array("host"=>$host),$key_type);
        //$client->getRequest();
        $search_obj = new \CloudsearchSearch($client);
        $search_obj->addIndex("pgw_search_article");
        $search_obj->setFormat("json");
        
        
        $page = I('get.'.C('VAR_PAGE'),1,'intval');
        if($page < 1){
            $page = 1;
        }

        $startHit = 20 * ($page - 1);
        $search_obj->setStartHit($startHit);
        $search_obj->setHits(20);
        $search_obj->addSort("add_time", '-');
        $search_obj->addFilter('cat_id_1 ='.C('TEACH_CAT_TOP_ID')."&&query=default:'".addslashes($keywords)."'");
        $search_obj->addFetchFields("article_id");
        $result = $search_obj->search();
        $result = json_decode($result);
        //print_r($result);
        //die();
        if($result->status == 'FAIL'){
            $this->error('服务器查询繁忙，请稍后再试！');
        }else{
            $count = $result->result->total;
            $Page = new \Think\Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            //$Page->lastSuffix = false;
            $Page->setConfig('first', '首页');
            $Page->setConfig('prev', '上一页');
            $Page->setConfig('next', '下一页');
            $Page->setConfig('last', '…%TOTAL_PAGE%');
            $Page->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %DOWN_PAGE%');
            $Page->rollPage = 11;
            $this->pageShow = $Page->show();   
            $articleIds = array();
            foreach($result->result->items as $item){
                $articleIds[] = $item->article_id;
            }
            //print_r($articleIds);
            $teachList = M('Article')->field('a.article_id,a.article_name,a.main_image,a.visit_time,a.add_time,a.article_keywords,a.article_brief')->alias('a')
                            ->order('add_time desc')->where(array('a.article_id' => array('in', $articleIds)))->select();
            foreach ($teachList as &$info) {
                $info['url'] = U('Teach/detail?id=' . $info['article_id']);
                $info['main_image'] = str_replace('style_org_img', 'style_thumb_img', $info['main_image']);
                $info['date'] = local_date('Y-m-d', $info['add_time']);
            }
            $this->teachList = $teachList;                 
            $this->display();            
        }
    }
}