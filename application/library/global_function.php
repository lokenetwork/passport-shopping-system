<?php
/**
 * Created by PhpStorm.
 * User: loken_mac
 * Date: 1/29/16
 * Time: 11:01 PM
 */
function ajaxReturn($data, $type = '', $json_option = 0){
  if(empty($type))
    $type = 'JSON';
  switch(strtoupper($type)){
    case 'JSON' :
      // 返回JSON数据格式到客户端 包含状态信息
      header('Content-Type:application/json; charset=utf-8');
      exit(json_encode($data, $json_option));
    case 'XML'  :
      // 返回xml格式数据
      header('Content-Type:text/xml; charset=utf-8');
      exit(xml_encode($data));
    case 'JSONP':
      // 返回JSON数据格式到客户端 包含状态信息
      header('Content-Type:application/json; charset=utf-8');
      $handler = $_GET['callback'];
      exit($handler . '(' . json_encode($data, $json_option) . ');');
    case 'EVAL' :
      // 返回可执行的js脚本
      header('Content-Type:text/html; charset=utf-8');
      exit($data);
    default     :
  }
}

function cookie($name,$value,$expired_time=36000,$path='/',$domain=''){
  $expired_time += time();
  if( $domain ){
    setcookie($name, $value,$expired_time,$path,$domain);
  }else{
    var_dump($name);
    var_dump($value);
    var_dump($expired_time);
    var_dump($path);
    setcookie($name, $value,$expired_time,$path);
  }
}

function GetUrlToDomain($domain) {
  $re_domain = '';
  $domain_postfix_cn_array = array("com", "net", "org", "gov", "edu", "com.cn", "cn");
  $array_domain = explode(".", $domain);
  $array_num = count($array_domain) - 1;
  if ($array_domain[$array_num] == 'cn') {
    if (in_array($array_domain[$array_num - 1], $domain_postfix_cn_array)) {
      $re_domain = $array_domain[$array_num - 2] . "." . $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
    } else {
      $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
    }
  } else {
    $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
  }
  return $re_domain;
}