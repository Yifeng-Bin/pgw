<?php
/*
 * smarty 模版内的图片css的自动修正
 */
namespace Index\Behavior;
class TemplateReplaceBehavior {
    public function run(&$params) {
            /* 修正css路径 */
            $params = preg_replace('/(<link\shref=["|\'])(\.\/)([a-z0-9A-Z_\-\.\/]+\.css[\?v\=0-9]*["|\']\stype=["|\']text\/css["|\']\srel=["|\']stylesheet["|\'])/i', '\1'.C('STATIC_CACHE_URL') .__ROOT__ .'/'. THEME_PATH . '\3', $params);
            /* 修正js目录下js的路径 */
            $params = preg_replace('/(<script\ssrc=["|\'])(\.\/)([a-z0-9A-Z_\-\.\/]+\.(?:js|vbs)[\?v\=0-9]*["|\']><\/script>)/', '\1'.C('STATIC_CACHE_URL') .__ROOT__ .'/'. THEME_PATH . '\3', $params);
        $pattern = array(
            '/((?:background|src)\s*=\s*["|\'])(images\/.*?["|\'])/is', // 在images前加上 $tmp_dir
            '/(["|\'])(\.\/)(images\/.*?["|\'])/is', // 在images前加上 $tmp_dir
            '/((?:background|background-image):\s*?url\()(images\/)/is', // 在images前加上 $tmp_dir
            '/(["|\'])(\/)(Statics\/.*?["|\'])/is', // 在Statics前加上 $tmp_dir
        );
        $replace = array(
            '\1' .C('STATIC_CACHE_URL').__ROOT__ .'/'. THEME_PATH . '\2',
            '\1' .C('STATIC_CACHE_URL').__ROOT__ .'/'. THEME_PATH . '\3',
            '\1' .C('STATIC_CACHE_URL').__ROOT__ .'/'. THEME_PATH . '\2',
            '\1' .C('STATIC_CACHE_URL').'/' . '\3',
        );
        $params = preg_replace($pattern, $replace, $params);            
    }
}
