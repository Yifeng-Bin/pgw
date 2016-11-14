<?php
function appLock($name='',$time=0,$type=""){
    if($name == ''){
        $name = USER_ID ? USER_ID : SESS_ID ;
    }
    if($type != ''){
        $name = $name.":".$type;
    }
    if(intval($time) <= 0){
        $time = 1;
    }
    $Model = new \Think\Model();
    $result = $Model->query("SELECT GET_LOCK('".$name."',".$time.") as getlock");
    return $result;
}
function appUnlock($name='',$type=""){
    if($name == ''){
        $name = USER_ID ? USER_ID : SESS_ID ;
    }
    if($type != ''){
        $name = $name.":".$type;
    }    
    $Model = new \Think\Model();
    $result = $Model->query("SELECT RELEASE_LOCK('".$name."') as getlock"); 
    return $result;
    //return $result[0]['GetLock'];
}

/*
function appLock($name='',$time=0,$type="",$timeout = 3){
    $lock = 0;
    $gmtime = gmtime();
    if($name == ''){
        $name = USER_ID ? USER_ID : SESS_ID ;
    }
    if($type != ''){
        $name = $type.":".$name;
    }
    if(intval($time) <= 0){
        $time = 1;
    }    
    
    $timestamp = $gmtime + $timeout;
    while (!$lock) {
        $redis = Think\Cache::getInstance('Redisadv');
        $lock = $redis->setnx('lock:' . $name, $timestamp);
        if (($lock) || ($gmtime > $redis->get('lock:' . $name) && $gmtime > $redis->getset('lock:' . $name, $timestamp))) {
            $lock = $timestamp;
            break;
        } else {
            $time--;
            if ($time < 0) {
                break;
            }
            usleep(100000);
        }
    }
    return $lock;
}
function appUnlock($name='',$type=""){
    if($name == ''){
        $name = USER_ID ? USER_ID : SESS_ID ;
    }
    if($type != ''){
        $name = $type.":".$name;
    }    
    $redis = Think\Cache::getInstance('Redisadv');
    if (gmtime() < $redis->get('lock:' . $name)) {
        $redis->rm('lock:' . $name);
    }
}
 */
?>