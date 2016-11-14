<?php

namespace Fenzhan\Controller;

class SyslogController extends CommonController {

    public function index() {
        $page = new \Com\Logdd\Page(M('AdminLog')->count(), 50);
        $this->pageInfo = $page->getPageInfo();
        $this->logList = M('AdminLog')->alias('l')->join('left join __ADMIN_USER__ as u on l.user_id = u.uid')->field('u.username,l.log_id,l.log_format_time,l.user_id,l.log_info,l.ip_address')
                ->limit($page->firstRow . ',' . $page->listRows)->order('log_id desc')->select();
        $this->display();
    }
}
