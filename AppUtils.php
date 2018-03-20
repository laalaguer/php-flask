<?php
/* Json encode the variables.
   Unicode is escaped. New in PHP5.4
   Return false if failed, otherwise the json object
*/
function tJsonEncode($var){
    return json_encode($var,JSON_UNESCAPED_UNICODE);
}

/* Debug an array of values, print to a log file
*/
function debug_array($error_prefix,$arr){
    foreach ($arr as $key=>$value){
        error_log ($error_prefix.": ".$key.":".$value." \n", 3, $error_prefix."_debug.txt");
    }
}

/* Debug a single value, print to a log file
*/
function debug_value($error_prefix,$value){
    error_log ($error_prefix.": ".$value." \n", 3, $error_prefix."_debug.txt");
}

/*Signature:
* string handler ( string $buffer )
* Receive a buffer string, return a buffer string
* 探测有没有 DISCUZ 的错误，有就改写回复内容
*/
function checkErrors($buffer){
    if(strpos($buffer, '<!DOCTYPE html PUBLIC')!==false && strpos( $buffer,'<div id="messagetext" class="alert_error">')!==false){
        if(preg_match('/<div id="messagetext" class="alert_error">\s*<p>(.*)<\/p>/isU', $buffer,$m)){
            return tJsonEncode(['success'=>false,'message'=>$m[1],'code'=>500, 'data'=>'']);
        }
    }
    else{
        return $buffer;
    }
}

/* Get a value, if empty return default value
*/
function get_value(&$var, $default=null) {
    return isset($var) ? $var : $default;
}


function get_boolean(&$var, $default=false){
    if (isset($var)){
        if (strtolower($var) == 'true'){
            return true;
        } else {
            return $default;
        }
    } else {
        return $default;
    }
}
function get_string(&$var, $default=null){
    if (isset($var)){
        $new_var = trim($var);
        return $new_var;
    } else {
        return $default;
    }
}

function get_positive_number(&$var, $default=null){
    if (isset($var)){
        if (intval($var) > 0){
            return intval($var);
        } else {
            // intval(x) == 0, so it cannot be intable or is 0
            return $default;
        }
    } else {
        return $default;
    }
}

function get_a_number(&$var, $default=null){
    if (isset($var) && is_numeric($var)){
        return intval($var);
    } else {
        return $default;
    }
}

// God like debug info
function enable_debug(){
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}

/* 
函数名称：post_check() 
函数作用：对提交的编辑内容进行处理 
参　　数：$post: 要提交的内容 
返 回 值：$post: 返回过滤后的内容 
*/ 
function post_check($post) { 
if (!get_magic_quotes_gpc()) { // 判断magic_quotes_gpc是否为打开 
$post = addslashes($post); // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤 
} 
$post = str_replace("_", "\_", $post); // 把 '_'过滤掉 
$post = str_replace("%", "\%", $post); // 把 '%'过滤掉 
$post = nl2br($post); // 回车转换 
$post = htmlspecialchars($post); // html标记转换 

return $post; 
} 