<?php
/**
* 抽象类 任何 request handler 都是它的具体实现子类
*/
require_once dirname(__FILE__).'/AppUtils.php';

if(!defined('IN_OUR_API')) {
    exit('Access Denied');
}

$_G['appv2']['session'] = null; // 存储 session 有关字段，一次初始化不再SQL查询
$_G['appv2']['user'] = null; // 存储用户相关字段，一次初始化不再 SQL 查询
$_G['appv2']['group'] = null; // 存储用户的用户组相关字段，一次初始化，不再 SQL 查询
$_G['appv2']['debug'] = null; // debug object


class AppRequestHandler {
    public $matched_regex = null;

    public function __construct($matched_regex) {
        $this->matched_regex = $matched_regex;
        $this->get_auth();
    }

    // 看看是不是有 session 存在, 存在则初始化用户，初始化用户组两个基本全局变量
    public function get_auth(){
        global $_G; // reference global variable
        $accessTokenHeader = $this->get_a_header("Authorization");
        // 在这里 写下对于 Auth 的检查代码
        // 各个业务不同 分别实现
    }

    // Util: Get headers dict
    public function get_request_headers() {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if(strpos($key, 'HTTP_') === 0) {
                $headers[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            }
        }
        return $headers;
    }

    // Util: Get a single header value, or null
    public function get_a_header($key){
        return get_value($this->get_request_headers()[$key], null);
    }

    // Util: Structuring a response header
    protected function set_header($key, $value){
        header($key.': '.$value);
    }

    // Util: Structuring a response http code
    public function set_http_code($httpCode){
        http_response_code($httpCode);
    }

    // Customize: Structuring JSON response header
    public function set_json_header(){
        $this->set_header("Content-Type", "application/json;charset=utf-8");
        $this->set_header("Access-Control-Allow-Origin", "*");
        $this->set_header("Access-Control-Allow-Methods", "POST,GET");
    }

    protected function unauth_response(){
        return tJsonEncode(['success'=>false,'message'=>'未处理请求','code'=>500,'data'=>'']);
    }

    // 用户登陆了没
    protected function is_auth(){
        global $_G; // reference global variables
        // 这里写下判断用户是否登录的代码
    }

    // 返回错误的函数
    public function error($httpcode){
        return tJsonEncode(['success'=>false,'message'=>'失败','code'=>$httpcode,'data'=>'']);
    }

    // Override it!
    public function get($matched_regex){
        return tJsonEncode(['success'=>false,'message'=>'失败','code'=>405,'data'=>'']);
    }

    // Override it!
    public function post($matched_regex){
        return tJsonEncode(['success'=>false,'message'=>'失败','code'=>405,'data'=>'']);
    }

    // Override it!
    public function delete($matched_regex){
        return tJsonEncode(['success'=>false,'message'=>'失败','code'=>405,'data'=>'']);
    }

    // Dispatch call to different handlers
    public function call(){
        ob_start('checkErrors'); // 如果有 discuz 错误 随时准备重写 output
        switch ($_SERVER['REQUEST_METHOD']){
            case 'GET':
                $this->set_http_code(200);
                $this->set_json_header();
                $content = $this->get($this->matched_regex);
                echo $content;
                break;
            case 'POST':
                $this->set_json_header();
                $this->set_http_code(200);
                $content = $this->post($this->matched_regex);
                echo $content;
                break;
            case 'DELETE':
                $this->set_json_header();
                $this->set_http_code(200);
                $content = $this->delete($this->matched_regex);
                echo $content;
                break;
            case 'OPTIONS':
                $this->set_header('Access-Control-Allow-Origin',' *');
                $this->set_header('Access-Control-Allow-Methods',' POST, GET, OPTIONS, PUT, DELETE');
                $this->set_header('Access-Control-Allow-Headers',' *');
                $this->set_http_code(200);
                break;
            default:
                $this->set_json_header();
                $this->set_http_code(405);
                $content = $this->error(405);
                echo $content;
                break;
        }
        ob_end_flush();  // dispatch the output
    }
}