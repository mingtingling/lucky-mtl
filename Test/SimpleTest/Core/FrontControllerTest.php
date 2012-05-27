<?php
/**
 * @author:mingtingling
 * @date:2012-3-9
 * aim:测试Common.config
 */
 define('APP_PATH',dirname(__FILE__).'/../../../../..');
 define('TP_PATH',dirname(__FILE__).'/../../..');
 define('MODULES_PATH',dirname(__FILE__).'/../../../../../Modules');
include(dirname(__FILE__).'/../../../Core/FrontController.class.php');
/*
 * test constructor
 */

//$test = new Tp_FrontController(); //Error,private constructor

/**
 * test getInstance
 */
$test = Tp_FrontController::getInstance();
$test2 = Tp_FrontController::getInstance();
/*
 * test init
 */
//$test->init('dd');
/*
$test->init(array(
	'test'=>'test',
	'test2'=>'test2'
));
*/
/*
$test->init(array(
	'db'=>'db'
));
*/
$test->init(array('appDebug'=>true));
P(C());

/*
 * test run
 */
 $test->run();
