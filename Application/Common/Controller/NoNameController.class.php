<?php
namespace Common\Controller;
use Think\Controller;
class NoNameController extends Controller {
    public function showError($massage){
        $this->error($massage);
    }
}
