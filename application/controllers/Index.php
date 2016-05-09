<?php
/**
 * @name IndexController
 * @author loken
 * @desc 登录控制器
 */
class IndexController extends BaseController {


  public function init(){
    parent::init();
    $this->checkHasLogin();
  }

  //Check the user is login or not
  function checkHasLogin(){
    global $Redis;
    $login_success_jump_url = $this->_get('login_success_jump_url','http://sh.'.$this->root_domain);

    if( isset($_COOKIE[$this->passport_name]) ){
      $passport_login_status = intval($Redis->get($_COOKIE[$this->passport_name]));
    }else{
      $passport_login_status = 0;
    }
    //这个要改,根据cookie里面的key值来做判断的,自动登录或者自动退出,-1代表退出状态
    if($passport_login_status === -1){

    }else if($passport_login_status === 0){

    }else if($passport_login_status > 0){
      $this->getView()->assign("title", '登陆提示');
      $this->getView()->assign("desc", '您已经登陆了!');
      $this->getView()->assign("url",$login_success_jump_url);
      $this->getView()->assign("type", 'warning');
      $this->display(VIEW_PATH.'/common/tips');
      exit;
    }
  }

  /**
   * 两个登陆页面,但都是提交到同一个action里面的
   */
  public function indexAction($name = "Stranger"){


    return TRUE;
  }

  public function shopAction($name = "Stranger"){
    $login_success_jump_url = $this->_get('login_success_jump_url','http://sh.'.$this->root_domain);
    $this->getView()->assign("login_success_jump_url",$login_success_jump_url);
    return TRUE;
  }

  /*
   * submit action deal
   * */
  public function postAction(){
    global $Redis;
    $account = trim($this->_post('account'));
    //$account = '13723772347';
    $password = trim($this->_post('password'));
    //$password = '111111';
    $keep_login = intval($this->_post('keep_login'));
    //$keep_login = 1;
    //get the possible admin list
    $condition = [
      'OR'=>[
        "passport_user_name" => $account,
        "passport_email" => $account,
        "passport_mobile" => $account,
      ]
    ];
    $field = ['user_id','password'];
    $user_list = $GLOBALS['r_db']->select("user", $field, $condition);

    $user_id = 0;
    foreach($user_list as $item){
      if( password_verify($password,$item['password']) ){
        $user_id = $item['user_id'];
        break;
      }
    }

    if( $user_id ){
      $respond_data['status'] = 1;
      $respond_data['msg'] = '登陆成功!';
      cookie($this->passport_name, $_COOKIE[$this->passport_name], time()+$this->passport_expired,'/',$this->root_domain);
      $Redis->set($_COOKIE[$this->passport_name],$user_id,$this->passport_expired);
      if( $keep_login  ){
        $passport_kepp_login_expired = intval(Yaf_Application::app()->getConfig()->passport->kepp_login_expired);
        cookie($this->passport_name, $_COOKIE[$this->passport_name], $passport_kepp_login_expired,'/',$this->root_domain);
        $Redis->set($_COOKIE[$this->passport_name],$user_id,$passport_kepp_login_expired);
      }
    }else{
      if( $user_list ){
        $respond_data['status'] = 0;
        $respond_data['msg'] = '账号或密码错误!';
      }else{
        $respond_data['status'] = -1;
        $respond_data['msg'] = '账号不存在!';
      }
    }
    ajaxReturn($respond_data);
    return false;
  }





}