<?php
define('IN_OUR_API', true);
require_once dirname(__FILE__).'/AppRequestHandler.class.php'; // 这个 handler 是所有 handler 的父 class
require_once dirname(__FILE__).'/AppUtils.php';

// 用户设立 route table, 路径都是 regex
$route_table = array(
    '/^check$/' => array('VersionHandler','VersionHandler.class.php'), // handler 可以不带参数
    '/^posts\/(\d+)$/'=>array('PostInfoHandler','PostHandler.class.php'), // handler 可以带参数
);

// 寻找合适的 handler 来处理 request 
// 返回值 ($handler, $matched) matched 包含了完整匹配到的路径
// 没找的合适的就返回 (defualthander, empty array)
function get_handler($incoming_route, $route_table, $default_handler){
    $matched_object = array();
    foreach ($route_table as $key => $value){
        if (preg_match($key, $incoming_route, $matched_object) > 0 ){
            return array($route_table[$key], $matched_object);
        }
    }
    return array($default_handler, array());
}

// api 节点: user/login, or thread/page, etc
$app_route = $_GET['r'];
// 寻找合适的handler
list($route_handler_class, $matched_regex) = get_handler($app_route, $route_table, array('ErrorHandler','ErrorHandler.class.php'));
// handler 所在的 class file
require_once dirname(__FILE__).'/'.$route_handler_class[1];
// 执行该 handler
$handler = new $route_handler_class[0]($matched_regex);
$handler->call();