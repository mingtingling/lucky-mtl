<?php
/**
 * @author:mingtingling
 * @date:2012-3-9
 * aim:测试Helper.Cookie
 */
define('APP_PATH',dirname(__FILE__).'/../../../../..');
define('TP_PATH',dirname(__FILE__).'/../../..');
define('MODULES_PATH',dirname(__FILE__).'/../../../../../Modules');
include(dirname(__FILE__).'/../../../Common/function.php');
include(dirname(__FILE__).'/../../../Helper/Cookie.class.php');
$config = include(dirname(__FILE__).'/../../../Common/config.php');
C($config);
Tp_Cookie::set('zhaojianghua','sb');
Tp_Cookie::set('zhaojianghua2','sb');
Tp_Cookie::set('zhaojianghua3','sb');
//Tp_Cookie::remove('zhaojianghua');
Tp_Cookie::clear();
P(Tp_Cookie::have('zhaojianghua2'));
echo(Tp_Cookie::get('zhaojianghua'));
P('aa');
P($_COOKIE);
?>