<?php
/**
 * @author:mingtingling
 * @date:2012-3-9
 * aim:测试Helper.Encode
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
P(Tp_Encode::tp('赵江华'));
P(Tp_Encode::url('hello'));
P(Tp_Encode::password('hello'));


P(Tp_Decode::tp(Tp_Encode::tp('赵江华')));
P(Tp_Decode::url(Tp_Encode::url('hello')));
