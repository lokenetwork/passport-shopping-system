<?php

/**
 * Created by PhpStorm.
 * User: loken_mac
 * Date: 1/22/16
 * Time: 9:52 PM
 */
class BaseController extends Yaf_Controller_Abstract {

  protected $passport_expired;
  protected $passport_name;
  protected $root_domain;



  public function init(){
    $this->root_domain = GetUrlToDomain($_SERVER['SERVER_NAME']);
    $this->passport_expired = intval(Yaf_Application::app()->getConfig()->passport->expired);
    $this->passport_name = Yaf_Application::app()->getConfig()->passport->name;
    $this->getView()->assign("css_rel", CSS_REL);
    $this->getView()->assign("css_type", CSS_TYPE);

    if( ini_get("yaf.environ") == 'dev' ){
      $this->getView()->assign("client_less", '<script src="http://cdn.bootcss.com/less.js/1.7.0/less.min.js"></script>');
    }else{
      $this->getView()->assign("client_less", '');
    }
    $this->getView()->company_name = '服装店';
  }


  /*
   * 再封装下get,post,为以后过滤做准备,直接改yaf我们不熟
   * */
  protected function _get($name, $default_value = ''){
    $Yaf_Request_Http = new Yaf_Request_Http();
    $value = $Yaf_Request_Http->get($name);
    if( $value === null ){
      $responed = $default_value;
    }else{
      $responed = $value;
    }
    if( is_string($responed) ){
      $responed = trim($responed);
    }
    return $responed;
  }

  protected function _post($name, $default_value = ''){
    $Yaf_Request_Http = new Yaf_Request_Http();
    $value = $Yaf_Request_Http->getPost($name);
    if( $value === null  ){
      $responed = $default_value;
    }else{
      $responed = $value;
    }
    if( is_string($responed) ){
      $responed = trim($responed);
    }
    return $responed;
  }


  /*
   * pdo 查看错误信息demo
   * */
  function pdo_error_demo(){
    global $r_db;
    var_dump($r_db->pdo->errorInfo());
  }


  protected function getPassportKey(){
    global $Redis;


    $Cstring = new Cstring();

    $passport_key = $Cstring->randString('30');
    $passport_key_value = $Redis->get($passport_key);

    /*循环获取到键值为至*/
    while($passport_key_value !== false){
      $passport_key = $Cstring->randString('30');
      $passport_key_value = $Redis->get($passport_key);
    };
    $Redis->set($passport_key,0,$this->passport_expired);
    return($passport_key);

  }


  function setPassportKeyInCookieAction(){
    if( !isset($_COOKIE[$this->passport_name]) ){
      cookie($this->passport_name, $this->getPassportKey(), $this->passport_expired,'/',$this->root_domain);
    }
    return false;
  }


}