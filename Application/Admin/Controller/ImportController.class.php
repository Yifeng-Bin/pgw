<?php
namespace Admin\Controller;
use Think\Controller;
class ImportController extends Controller {
    //导入地区信息
    //  /usr/bin/php /home/wwwroot/www.58pgw.com/cli.php Import/region
    public function region() {

        header("Content-type: text/html; charset=utf-8"); 
        $regionList = M('DataProvince','pg_','DB_CONFIG_OLD')->field('province_id,province_name')->select();         
        foreach($regionList as $regionInfo){
            if(empty(M('Region')->find($regionInfo['province_id']))){
                M('Region')->add(array(
                    'region_id' => $regionInfo['province_id'],
                    'parent_id' => 0,
                    'region_name' => $regionInfo['province_name'],
                    'region_type' => 0,
                    'status' => 1,
                ));
                echo "导入".$regionInfo['province_name']."\n";                
            }
        }
        
        $regionList = M('DataCity','pg_','DB_CONFIG_OLD')->field('city_id,province_id,city_name')->select();         
        foreach($regionList as $regionInfo){
            if(empty(M('Region')->find($regionInfo['city_id']))){
                M('Region')->add(array(
                    'region_id' => $regionInfo['city_id'],
                    'parent_id' => $regionInfo['province_id'],
                    'region_name' => $regionInfo['city_name'],
                    'region_type' => 1,
                    'status' => 1,
                ));
                echo "导入".$regionInfo['city_name']."\n";                
            }
        }
    }
    
    //导入工种信息
    //  /usr/bin/php /home/wwwroot/www.58pgw.com/cli.php Import/workType
    public function workType() {

        header("Content-type: text/html; charset=utf-8"); 
        $workTypeList = M('DataAttr','pg_','DB_CONFIG_OLD')->field('attr_id,title')->select();         
        foreach($workTypeList as $workTypeInfo){
            if(empty(M('WorkType')->find($workTypeInfo['attr_id']))){
                M('WorkType')->add(array(
                    'type_id' => $workTypeInfo['attr_id'],
                    'type_name' => $workTypeInfo['title'],
                    'parent_id' => 0,
                    'level' => 1,
                    'status' => 1,
                ));
                echo "导入".$workTypeInfo['title']."\n";                
            }
            
            $workTypeList1 = M('DataAttrValue','pg_','DB_CONFIG_OLD')->where(array('attr_id' => $workTypeInfo['attr_id']))->field('attr_value_id,title')->select();   
            foreach($workTypeList1 as $workTypeInfo1){
                if(empty(M('WorkType')->find($workTypeInfo1['attr_value_id']))){
                    M('WorkType')->add(array(
                        'type_id' => $workTypeInfo1['attr_value_id'],
                        'type_name' => $workTypeInfo1['title'],
                        'parent_id' => $workTypeInfo['attr_id'],
                        'level' => 2,
                        'status' => 1,
                    ));
                    echo "导入".$workTypeInfo1['title']."\n";                
                }
            }            
        }
    }    
    
    
    //导入文章分类信息
    //  /usr/bin/php /home/wwwroot/www.58pgw.com/cli.php Import/articleCategory
    public function articleCategory() {

        header("Content-type: text/html; charset=utf-8"); 
        $categoryList = M('ArticleCate','pg_','DB_CONFIG_OLD')->field('cat_id,parent_id,title,level,from,seo_title,seo_keywords,seo_description,orderby,dateline')->select();         
        foreach($categoryList as $categoryInfo){
            if(empty(M('ArticleCategory')->find($categoryInfo['cat_id']))){
                M('ArticleCategory')->add(array(
                    'cat_id' => $categoryInfo['cat_id'],
                    'cat_name' => $categoryInfo['title'],
                    'cat_decs' => $categoryInfo['seo_description'],
                    'cat_keywords' => $categoryInfo['seo_keywords'],
                    'status' => 1,
                    'sort' => $categoryInfo['orderby'],
                    'parent_id' => $categoryInfo['parent_id'],
                    'level' => $categoryInfo['level'],
                ));
                echo "导入".$categoryInfo['title']."\n";                
            }         
        }
    }    
    
    //导入文章信息
    //  /usr/bin/php /home/wwwroot/www.58pgw.com/cli.php Import/article
    public function article() {

        header("Content-type: text/html; charset=utf-8"); 
        $articleList = M('Article','pg_','DB_CONFIG_OLD')->select();         
        foreach($articleList as $articleInfo){
            if(empty(M('Article')->find($articleInfo['article_id']))){
                M('Article')->add(array(
                    'article_id' => $articleInfo['article_id'],
                    'article_name' => $articleInfo['title'],
                    'article_brief' => $articleInfo['desc'],
                    'article_keywords' => '',
                    'cat_id' => $articleInfo['cat_id'],
                    'region_id' => $articleInfo['city_id'],
                    'article_desc' => '',
                    'is_delete' => 0,
                    'add_time' => $articleInfo['dateline'],
                    'sort' => $articleInfo['orderby'],
                ));
                echo "导入".$articleInfo['title']."\n";                
            }         
        }
    }   
    
    //导入文章内容
    //  /usr/bin/php /home/wwwroot/www.58pgw.com/cli.php Import/articleContent
    public function articleContent() {

        header("Content-type: text/html; charset=utf-8"); 
        $articleList = M('Article')->select();  
        foreach($articleList as $articleInfo){
            $content = M('ArticleContent','pg_','DB_CONFIG_OLD')->where(array('article_id' => $articleInfo['article_id']))->getField('content');
            if(!empty($content)){
                M('Article')->where(array('article_id' => $articleInfo['article_id']))->save(array(
                    'article_desc' => $content,
                ));
                echo "导入".$articleInfo['title']."内容\n";                
            }         
        }
    }     
    
    //导入用户所属工种
    //  /usr/bin/php /home/wwwroot/www.58pgw.com/cli.php Import/userToWorkType
    public function userToWorkType() {
        header("Content-type: text/html; charset=utf-8"); 
        $userWorkTypeList = M('DesignerAttr','pg_','DB_CONFIG_OLD')->select();         
        foreach($userWorkTypeList as $userWorkTypeInfo){
            if(empty(M('UserToWorkType')->where(array('user_id' => $userWorkTypeInfo['uid'],'type_id_1' => $userWorkTypeInfo['attr_id'],'type_id_2' => $userWorkTypeInfo['attr_value_id']))->find())){
                M('UserToWorkType')->add(array(
                    'user_id' => $userWorkTypeInfo['uid'],
                    'type_id_1' => $userWorkTypeInfo['attr_id'],
                    'type_id_2' => $userWorkTypeInfo['attr_value_id'],
                ));
                echo "导入".$userWorkTypeInfo['uid'].'-'.$userWorkTypeInfo['attr_id'].'-'.$userWorkTypeInfo['attr_value_id']."\n";                
            }         
        }
    }      
    
    //导入用户
    //  /usr/bin/php /home/wwwroot/www.58pgw.com/cli.php Import/user
    public function user() {
        header("Content-type: text/html; charset=utf-8");
        $userList = M('users',NUll,'DB_CONFIG_OLD')->select();
        foreach($userList as $userInfo){
            if(empty(M('Users')->where(array('user_id' => $userInfo['id']))->find())){
                M('Users')->add(array(
                    'user_id' => $userInfo['id'],
                    'user_name' => $userInfo['email'],
                    //'nick_name' => $userInfo['name'],
                    'password' => $userInfo['password'],
                    'add_time' => gmstr2time($userInfo['created_at']),
                    'update_time' => gmstr2time($userInfo['updated_at']),
                ));
                echo "导入".$userInfo['name']."登录基本信息\n";
                $designerInfo = M('designer','pg_','DB_CONFIG_OLD')->where(array('uid'=> $userInfo['id']))->find();
                if(!empty($designerInfo)){
                    M('Users')->where(array('user_id'=> $userInfo['id']))->save(array(
                        'qq' => $designerInfo['qq'],
                        'attention_num' => $designerInfo['attention_num'],
                        'yuyue_num' => $designerInfo['yuyue_num'],
                        'case_num' => $designerInfo['case_num'],
                        'blog_num' => $designerInfo['blog_num'],
                        'views' => $designerInfo['views'],
                        'tenders_num' => $designerInfo['tenders_num'],
                        'score1' => $designerInfo['score1'],
                        'score2' => $designerInfo['score2'],
                        'score3' => $designerInfo['score3'],
                        'slogan' => $designerInfo['slogan'],
                        'money' => $designerInfo['money'],
                        'enter_date' => $designerInfo['rh_y']."-".$designerInfo['rh_m']."-".$designerInfo['rh_d'],
                    ));                    
                }
                
                $memberInfo = M('member','pg_','DB_CONFIG_OLD')->where(array('uid'=> $userInfo['id']))->find();
                if(!empty($designerInfo)){
                    M('Users')->where(array('user_id'=> $userInfo['id']))->save(array(
                        'mobile' => $memberInfo['mobile'],
                        'region_id' => $memberInfo['city_id'],
                        'real_name' => $memberInfo['realname'],
                        'avatar' => '',
                        'gold' => $memberInfo['gold'],
                    ));                    
                }                
                
            }
        }
    }         
}
