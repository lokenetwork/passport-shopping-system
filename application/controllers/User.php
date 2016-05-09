<?php
/**
 * @name UserController
 * @author root
 * @desc use this control must be auth
 * @see
 */
class UserController extends AuthController {

  public function init(){
    parent::init();
  }
  /**
   * echo the user info api
   * 返回格式,
   * $respond = [
   *    'passport_login_status' => *, //这里*有3种情况,-1代表退出状态.0代表未登录状态,未登录user_info为空.大于0代表登录状态,大于0的是用户的uid.
   *    'user_info' => [] //这里面返回用户数据
   * ]
   */
  public function echoJsonInfoAction($field='passport_user_name,passport_mobile,passport_email,user_id'){
    if( $field == '*' ){
      //limit select with *
      ajaxReturn('禁止使用*查询!');
    }
    $field = explode(',',$field);
    $passport_login_key = $this->_get('passport_login_key');
    if( !$passport_login_key  ){
      //limit select with *
      ajaxReturn('$passport_login_key 不能为空!');
    }

    global $Redis;
    if( $passport_login_key ){
      $passport_login_status = intval($Redis->get($passport_login_key));
    }else{
      $passport_login_status = 0;
    }
    $ajax_respond['passport_login_status'] = $passport_login_status;

    //只要不是未登录状态,都会返回用户信息
    if( $passport_login_status != 0 ){
      $passport_login_user_id = $passport_login_status;
      $condition = [
        "user_id" => $passport_login_user_id,
      ];
      $passport_user_info = $GLOBALS['r_db']->get("user", $field, $condition);
      $ajax_respond['passport_user_info'] = $passport_user_info;
    }
    ajaxReturn($ajax_respond);
  }

  function logoutAction(){
    unset($_SESSION['admin_id']);
    $this->getView()->assign("title", '温馨提示');
    $this->getView()->assign("desc", '您已成功退出!');
    $this->getView()->assign("url", '/Login/index');
    $this->getView()->assign("type", 'success');
    $this->getView()->display('common/tips.html');
    return false;
  }


}