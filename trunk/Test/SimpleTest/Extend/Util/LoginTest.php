<?php
/**
 * @author:mingtingling
 * @date:2012-3-9
 * aim:测试Common.config
 */
define('APP_PATH',dirname(__FILE__).'/../../../../..');
define('TP_PATH',dirname(__FILE__).'/../../..');
define('MODULES_PATH',dirname(__FILE__).'/../../../../../Modules');
include(dirname(__FILE__).'/../../../Common/function.php');
$config = include(dirname(__FILE__).'/../../../Common/config.php');
C($config);
/*
 * test run
 */
 P(Tp_Cookie::get('_user'));
//Tp_Login::setLogin('你好');
P($_COOKIE);
P(Tp_Login::getLogin());
?>