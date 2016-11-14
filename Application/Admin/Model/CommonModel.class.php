<?php

namespace Admin\Model;

use Think\Model;

class CommonModel extends Model {
    public $modelErrorInfo = array(
        'field_duplicate' => '字段重复',
    );
    public function getModelError() {
        if ($this->getError()) {
            return $this->getError();
        } elseif ($this->getDbError()) {
            $error = $this->getDbError();
            $errorInfo = explode(':', $error, 2);
            switch ($errorInfo[0]) {
                case 1169;
                case 1062:
                    $error = $this->modelErrorInfo['field_duplicate'];
                    break;
                default:
                    $error = "错误：".$errorInfo[0];
            }
            return $error;
        } else {
            return "未知错误！";
        }
    }
    
    protected function check_delete($str){
        if(substr($str, 0, 3) ==  'del'){
            return true;
        }else{
            return false;
        }
    }    

}
