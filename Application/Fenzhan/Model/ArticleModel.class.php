<?php

namespace Fenzhan\Model;

class ArticleModel extends CommonModel {

    protected $fields = array('article_id', 'article_name', 'article_brief', 'article_keywords', 'cat_id','cat_id_1','cat_id_2','cat_id_3','cat_id_4','cat_id_5', 'region_id', 'photo_num','main_image','article_desc',
        'is_delete','is_checked', 'add_time', 'sort','visit_time','user_id',
        '_type' => array(
            'article_id' => 'mediumint(8) unsigned',
            'article_name' => 'varchar(100) []',
            'article_brief' => 'varchar(255) []',
            'article_keywords' => 'varchar(255) []',
            'cat_id' => 'smallint(5) unsigned [0]',
            'cat_id_1' => 'smallint(5) unsigned [0]',
            'cat_id_2' => 'smallint(5) unsigned [0]',
            'cat_id_3' => 'smallint(5) unsigned [0]',
            'cat_id_4' => 'smallint(5) unsigned [0]',
            'cat_id_5' => 'smallint(5) unsigned [0]',
            'region_id' => 'smallint(5) unsigned [0]',
            'photo_num' => 'smallint(5) unsigned [0]',
            'main_image' => 'varchar(255) []',
            'article_desc' => 'text',
            'is_delete' => 'tinyint(1) unsigned [1]',
            'is_checked' => 'tinyint(1) unsigned [1]',
            'add_time' => 'int(11) unsigned [0]',
            'sort' => 'smallint(5) unsigned [100]',
            'visit_time' => 'mediumint(8) unsigned [0]',
            'user_id' => 'mediumint(8) unsigned [0]',
        ),
    );
    protected $pk = 'article_id';
    protected $_validate = array(
        array('article_name', 'require', '文章名称不能为空！'),
    );

    public function getList($page, $size = 20, $order = '') {
        if (!empty($order)) {
            $this->order($order);
        } else {
            $this->order('article_id desc');
        }
        return $this->limit($page . ',' . $size)->select();
    }

    public function addArticleInfo() {
        $data = array(
            'article_name' => I('post.article_name', '', ''),
            'article_brief' => I('post.article_brief', '', ''),
            'article_keywords' => I('post.article_keywords', '', ''),
            'article_desc' => I('post.article_desc', '', 'clean_xss'),
            'is_delete' => 0,
            'is_checked' => 1,
            'add_time' => gmtime(),
            'sort' => I('post.sort', 100, 'intval'),
            'cat_id' => I('post.cat_id', 0, 'intval'),
            'region_id' => intval(ADMIN_CITY_ID),
            'main_image' => I('post.main_image', '', ''),
        );
        if(empty($data['main_image'])){
            preg_match("/<[img|IMG].*?src=[\'|\"](.*?[\.bmp|\.jpg|\.png].*?)[\'|\"].*?[\/]?>/",$data['article_desc'],$match);
            if(isset($match[1]) && !empty($match[1])){
                $data['main_image'] = strip_tags($match[1]);
            }
        }
        
        if(empty($data['article_brief'])){
            $data['article_brief'] = mb_substr(strip_tags($data['article_desc']),0,255,'UTF-8');
        }        
        $catInfo = M('ArticleCategory')->field('cat_id,cat_name,level,parent_id')->find($data['cat_id']);     
        if(empty($catInfo)){
            $this->error = '文章分类不能为空';
            return false;
        }        
        if (!$this->create($data, self::MODEL_INSERT)) {
            admin_log("文章创建失败");
            return false;
        } else {
            $result = $this->add($data);
            
            $photo_ids = I('post.photo_ids', array(), 'intval');
            $count = count($photo_ids) + 100;
            foreach ($photo_ids as $photo_id) {
                M('ArticlePhoto')->where(array('img_id' => $photo_id, 'article_id' => 0))->save(array('article_id' => $result, 'sort' => $count));
                $count--;
            }
            $photo_num = M('ArticlePhoto')->where(array('article_id' => $result))->count();
            $this->where(array('article_id' => $result))->save(array('photo_num' => $photo_num));
            if($photo_num > 0){
                $main_image = M('ArticlePhoto')->where(array('article_id' => $result))->order('sort desc')->getField('url');
                $this->where(array('article_id' => $result))->save(array('main_image' => $main_image));
            }else{
                //$this->where(array('article_id' => $result))->save(array('main_image' => ''));
            }
            
            //增加属性
            $attrArr = I('post.attr', array(), 'intval');
            $atricle_cat_ids = array(
                'cat_id_1' => 0,
                'cat_id_2' => 0,
                'cat_id_3' => 0,
                'cat_id_4' => 0,
                'cat_id_5' => 0,
            );  //更新文章的分级分类id
            
            if (!empty($catInfo)) {
                while (true) {
                    $atricle_cat_ids['cat_id_'.$catInfo['level']] = $catInfo['cat_id'];
                    if ($catInfo['parent_id'] != 0) {
                        $catInfo = M('ArticleCategory')->field('cat_id,cat_name,level,parent_id')->find($catInfo['parent_id']);
                    } else {
                        break;
                    }
                }
                M('Article')->where(array('article_id' => $result))->save($atricle_cat_ids);
                $attrInfoList = M('ArticleAttr')->field('attr_id,attr_type')->where(array('article_cat_id' => $atricle_cat_ids['cat_id_1']))->select();
                foreach ($attrInfoList as $attrInfo) {
                    $attrValueList = M('ArticleAttrValue')->where(array('attr_id' => $attrInfo['attr_id']))->getField('attr_value_id', true);

                    if (isset($attrArr[$attrInfo['attr_id']]) && !empty($attrArr[$attrInfo['attr_id']])) {
                        $attrValueIds = array_intersect($attrArr[$attrInfo['attr_id']], $attrValueList);
                        if (empty($attrValueIds)) {
                            continue;
                        } else {
                            if ($attrInfo['attr_type'] == 1) {
                                foreach ($attrValueIds as $attrValueId) {
                                    M('ArticleAttrInfo')->add(array('article_id' => $result, 'attr_id' => $attrInfo['attr_id'], 'attr_value_id' => $attrValueId));
                                }
                            } else {
                                M('ArticleAttrInfo')->add(array('article_id' => $result, 'attr_id' => $attrInfo['attr_id'], 'attr_value_id' => $attrValueIds[0]));
                            }
                        }
                    } else {
                        continue;
                    }
                }
            }
            //增加属性
        }
        if ($result === false) {
            return false;
        }
        $data['article_id'] = $result;
        admin_log("文章增加成功", json_encode($data));
        return $result;
    }

    public function editArticleInfo() {
        $article_id = I('post.article_id', '0', 'intval');
        $data = array(
            'article_name' => I('post.article_name', '', 'htmlspecialchars'),
            'article_brief' => I('post.article_brief', '', 'htmlspecialchars'),
            'article_keywords' => I('post.article_keywords', '', 'htmlspecialchars'),
            'article_desc' => I('post.article_desc', '', 'clean_xss'),
            'is_delete' => 0,
            'is_checked' => I('post.is_checked', 0, 'intval'),
            'sort' => I('post.sort', 100, 'intval'),
            'cat_id' => I('post.cat_id', 0, 'intval'),
            //'region_id' => I('post.region_id', 0, 'intval'),            'region_id' => intval(ADMIN_CITY_ID),
            'main_image' => I('post.main_image', '', ''),
        );
        if(empty($data['main_image'])){
            preg_match("/<[img|IMG].*?src=[\'|\"](.*?[\.bmp|\.jpg|\.png].*?)[\'|\"].*?[\/]?>/",$data['article_desc'],$match);
            if(isset($match[1]) && !empty($match[1])){
                $data['main_image'] = strip_tags($match[1]);
            }
        }
        if(empty($data['article_brief'])){
            $data['article_brief'] = mb_substr(strip_tags($data['article_desc']),0,255,'UTF-8');
        }  
        
        $catInfo = M('ArticleCategory')->field('cat_id,cat_name,level,parent_id')->find($data['cat_id']);     
        if(empty($catInfo)){
            $this->error = '文章分类不能为空';
            return false;
        }
        if (!$this->create($data, self::MODEL_UPDATE)) {
            admin_log("文章编辑失败");
            return false;
        } else {
            $result = $this->where(array('article_id' => $article_id))->save();
        }
        if ($result === false) {
            return false;
        }
        $photo_ids = I('post.photo_ids', array(), 'intval');
        $count = count($photo_ids) + 100;
        M('ArticlePhoto')->where(array('article_id' => $article_id))->save(array('article_id' => 0));
        $article_user_id = $this->where(array('article_id' => $article_id))->getField('user_id');
        foreach ($photo_ids as $photo_id) {
            $where = array(
                'img_id' => $photo_id,
                'article_id' => 0,
                '_complex' => array(
                    '_logic' => 'or',
                    'admin_id' => array('neq',0),
                    'user_id' => $article_user_id,
                ),
            );
            M('ArticlePhoto')->where($where)
                    ->save(array('article_id' => $article_id,'user_id' => $article_user_id, 'sort' => $count));
            $count--;
        }
        $photo_num = M('ArticlePhoto')->where(array('article_id' => $article_id))->count();
        $this->where(array('article_id' => $article_id))->save(array('photo_num' => $photo_num));
        if($photo_num > 0){
            $main_image = M('ArticlePhoto')->where(array('article_id' => $article_id))->order('sort desc')->getField('url');
            $this->where(array('article_id' => $article_id))->save(array('main_image' => $main_image));
        }else{
            //$this->where(array('article_id' => $article_id))->save(array('main_image' => ''));
        }

        //增加属性
        M('ArticleAttrInfo')->where(array('article_id' => $article_id))->delete();
        $attrArr = I('post.attr', array(), 'intval');
        $atricle_cat_ids = array(
            'cat_id_1' => 0,
            'cat_id_2' => 0,
            'cat_id_3' => 0,
            'cat_id_4' => 0,
            'cat_id_5' => 0,
        );  //更新文章的分级分类id        
        
        if (!empty($catInfo)) {
            while (true) {
                $atricle_cat_ids['cat_id_'.$catInfo['level']] = $catInfo['cat_id'];
                if ($catInfo['parent_id'] != 0) {
                    $catInfo = M('ArticleCategory')->field('cat_id,cat_name,level,parent_id')->find($catInfo['parent_id']);
                } else {
                    break;
                }
            }
            M('Article')->where(array('article_id' => $article_id))->save($atricle_cat_ids);
            $attrInfoList = M('ArticleAttr')->field('attr_id,attr_type')->where(array('article_cat_id' => $atricle_cat_ids['cat_id_1']))->select();
            foreach ($attrInfoList as $attrInfo) {
                $attrValueList = M('ArticleAttrValue')->where(array('attr_id' => $attrInfo['attr_id']))->getField('attr_value_id', true);

                if (isset($attrArr[$attrInfo['attr_id']]) && !empty($attrArr[$attrInfo['attr_id']])) {
                    $attrValueIds = array_intersect($attrArr[$attrInfo['attr_id']], $attrValueList);
                    if (empty($attrValueIds)) {
                        continue;
                    } else {
                        if ($attrInfo['attr_type'] == 1) {
                            foreach ($attrValueIds as $attrValueId) {
                                M('ArticleAttrInfo')->add(array('article_id' => $article_id, 'attr_id' => $attrInfo['attr_id'], 'attr_value_id' => $attrValueId));
                            }
                        } else {
                            M('ArticleAttrInfo')->add(array('article_id' => $article_id, 'attr_id' => $attrInfo['attr_id'], 'attr_value_id' => $attrValueIds[0]));
                        }
                    }
                } else {
                    continue;
                }
            }
        }
        //增加属性        


        $data['article_id'] = $article_id;
        admin_log("文章编辑成功", json_encode($data));
        return $result;
    }

    public function getArticleInfo($article_id) {

        $article_info = $this->find($article_id);
        return $article_info;
    }

}
