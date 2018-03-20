<?php
/**
* Demo 函数
*/
require_once dirname(__FILE__).'/AppRequestHandler.class.php';
require_once dirname(__FILE__).'/AppUtils.php';

class VersionHandler extends AppRequestHandler{
    function __construct($matched_regex) {
        parent::__construct($matched_regex);
    }

    // 覆盖 GET 方法
    public function get(){
        // GET 参数设置
        $clientVersion = trim(get_value($_GET['version'], ''));
        // 检查是否已经登录
        if(!$this->is_auth()){ // 父对象已经帮我们统一设置好了
            return tJsonEncode(['success'=>false, 'message'=>'请先登录', 'code'=>500, 'data'=>null]);
        }
        return tJsonEncode(['success'=>true,'message'=>'成功','code'=>200,'data'=>$upgrade]);
    }
}