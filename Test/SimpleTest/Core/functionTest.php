<?php
/**
 * @author:mingtingling
 * @date:2012-3-9
 * aim:测试Common.function
 */
 define('TP_PATH',dirname(__FILE__).'/../../..');
 define('MODULES_PATH',dirname(__FILE__).'/../../../../../Modules');
require_once(dirname(__FILE__).'/../../../Core/function.php');
/*
 * test import
 */
//测试系统导入
define('MTL_IMPORT_BASEURL',TP_PATH.'/Core');
import('Tp.Core.Tp');
import('Tp.Core.Helper.Encode');
import('Tp.Core.Helper.Encode');
P(Tp_Encode::tp('加密'));
//测试用户类导入
import('Tp.Core.FrontController');
import('Mtl.FrontController','.class.php');
$test = new Test_Test_TestImport();
$test->test();
import('Test2.TestModel');
$testModel = new Test2_TestModel();
$testModel->testImport();
import('Test2.TestController');
$testController = new Test2_TestController();
$testController->testImport();
import('Test2.Test.Test');
$test = new Test2_Test_Test();
$test->testImport();
import('Test.Test.TestImport');
$test = new Test_Test_TestImport();
$test->test();
import('Test.Test.TestImport2','','.php');
$test = new Test_Test_TestImport2();
$test->testImport();


/*
 * test tp_include
 */
 P(tp_include(dirname(__FILE__).'/../../../Helper/Encode.class.php')); //测试已经被导入的类
 P(tp_include(dirname(__FILE__).'/../../../Helper/Decode.class.php')); //测试未被导入且存在的类
 P(tp_Decode::tp(Tp_Encode::tp('这个是测试解密的')));
 //tp_include(dirname(__FILE__).'/../../../Helper/Encode.php'); //测试不存在的类

/*
 * test is_win
 */
P(is_win());

/*
 * test tp_slashes
 */
P(tp_slashes('insert into')); //测试add
P(tp_slashes('insert into',false)); //测试remove


/*
 * test tp_get_type
 */
 P(tp_get_type('ddd'));
 P(tp_get_type(true));
 P(tp_get_type(array()));
 P(tp_get_type($test));
 P(tp_get_type(12.3));

 /*
  * test tp_lcfirst
  */
 P(tp_lcfirst('TEST'));

/*
 * test tp_echo
 */
 tp_echo('first');
 tp_echo('second',2);
 tp_echo('third',3);

 /*
  * test C
  */
 $arr = array(
 	'test' => array(
 		'test1'=> 'test1',
 		'test2' => 'test2',
 		'test3' => 'test3'
 	),
 	'test4' => '测试'
 );
 C($arr); //设置数组
 P(C());
 C('test4','test4'); //设置一个元素
 P(C('test4')); //得到一个元素
 P(C('test=>test1')); //得到一个元素
 C(array(
 	'test5' => 'test5',
 	'test' => 'test'
 )); //设置多个
 P(C());


 /*
  * test U
  */
$arr = array(
 	'test' => array(
 		'test1'=> 'test1',
 		'test2' => 'test2',
 		'test3' => 'test3'
 	),
 	'test4' => '测试'
 );
 U($arr); //设置数组
 P(U());
 U('test4','test4'); //设置一个元素
 P(U('test4')); //得到一个元素
 P(U('test=>test1')); //得到一个元素
 U(array(
 	'test5' => 'test5',
 	'test' => 'test'
 )); //设置多个
 P(U());


 /*
  * test __autoload
  */
$test = new TestHelper(); //导入辅助类
$test->test();