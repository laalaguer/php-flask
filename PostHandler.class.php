<?php
/** 
* DEMO 带有URL 参数匹配的处理
 */
require_once dirname(__FILE__).'/AppRequestHandler.class.php';
require_once dirname(__FILE__).'/AppUtils.php';

class PostInfoHandler extends AppRequestHandler{
    function __construct($matched_regex){
        parent::__construct($matched_regex);
    }

    // GET 方法覆盖，有匹配参数
    public function get($matched_regex){
        // enable_debug();
        $pid = intval($matched_regex[1]);
        return tJsonEncode(['success'=>$success, 'message'=>$message, 'code'=>200, 'data'=>$pid]);
    }
    // POST 方法覆盖
    // 无参数
    public function post(){
        return tJsonEncode(['success'=>$success, 'message'=>$message, 'code'=>200, 'data'=>$pid]);
    }
}