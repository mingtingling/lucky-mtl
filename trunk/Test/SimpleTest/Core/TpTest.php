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
$tp = new Tp();

/*
 * testt __call
 */
//$tp->setLog(); //调用不存在的函数


/*
 * test __set
 */
//$tp->aaa = 2;


/*
 * test __get
 */
P($tp->aaa);

/*
 * test __callStatic
 */
//Tp::test();

/*
 * 静默者模式暂时不测
 */
?>