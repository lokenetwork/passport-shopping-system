<?php

define('APPLICATION_PATH', dirname(__FILE__));
define('VIEW_PATH', APPLICATION_PATH.'/application/views/');
include_once(APPLICATION_PATH."/application/library/global_function.php");

$application = new Yaf_Application( APPLICATION_PATH . "/conf/application_".ini_get('yaf.environ').".ini");
define('CSS_TYPE', Yaf_Application::app()->getConfig()->css->type);
define('CSS_REL', Yaf_Application::app()->getConfig()->css->rel);

/* redis 单例 */
$Redis  = new Redis();
$Redis->connect(Yaf_Application::app()->getConfig()->redis->server, Yaf_Application::app()->getConfig()->redis->port);
$Redis->auth(Yaf_Application::app()->getConfig()->redis->password);
//默认选择0数据库
$Redis->select(0);

/* mysql 单例 */
$r_db = new Medoo();
$w_db = new Medoo(['control_type' => 2]);


$application->getDispatcher()->throwException(false);
$application->bootstrap()->run();


?>
