<?php
/**
 * @author:mingtingling
 * @date:2012-3-9
 * aim:测试Helper.Session
 */
define('APP_PATH',dirname(__FILE__).'/../../../../..');
define('TP_PATH',dirname(__FILE__).'/../../..');
define('MODULES_PATH',dirname(__FILE__).'/../../../../../Modules');
include(dirname(__FILE__).'/../../../Common/function.php');
include(dirname(__FILE__).'/../../../Helper/Session.class.php');
$config = include(dirname(__FILE__).'/../../../Common/config.php');
C($config);
Tp_Session::set('zhaojianghua','sb');
Tp_Session::set('zhaojianghua2','sb');
Tp_Session::set('zhaojianghua3','sb');
Tp_Session::remove('zhaojianghua');
//Tp_Session::clear();
P(Tp_Session::have('zhaojianghua2'));
echo(Tp_Session::get('zhaojianghua'));
P('aa');
P($_SESSION);
?>