<?php

define('APP_PATH',dirname(__FILE__).'/../../../../..');
define('MODULES_PATH',APP_PATH.'/Modules');
define('TP_PATH',APP_PATH.'/Library/Toper');
define('PUBLIC_PATH',APP_PATH.'/Public');
require_once(dirname(__FILE__).'/../../../Core/FrontController.class.php');
require_once(dirname(__FILE__).'/../../../Core/App.class.php');
require_once(dirname(__FILE__).'/../../../Core/Route.class.php');
require_once(dirname(__FILE__).'/../../../Core/Controller.class.php');
require_once(dirname(__FILE__).'/../../../../../Modules/Controllers/Test/IndexController.class.php');
require_once(dirname(__FILE__).'/../../../Helper/Session.class.php');

//C($config);
$_SERVER = array
(
    'HTTP_HOST' => 'www.lucky.com',
    'REQUEST_URI' => '/Test/Index/test2',
    'PHP_SELF' => '/index.php'
);
$config = include (dirname(__FILE__).'/../../../../../config.php');
$frontController = Tp_FrontController::getInstance(false);
$frontController->init($config);
$frontController->run();
//模拟路由
class SessionTest extends PHPUnit_Framework_TestCase {

	public function __construct() {
	}
	public function testSet() {
		Tp_Session::set('test','test');
		$val = Tp_Session::get('test');
		$this->assertEquals('test',$val);
	}
	public function testHave() {
		Tp_Session::set('testHave','testHave',3);
		$val = Tp_Session::have('testHave');
		$this->assertTrue($val);
		sleep(2);
		$val = Tp_Session::have('testHave');
		$this->assertTrue($val);
		sleep(1);
		$val = Tp_Session::have('testHave');
		$this->assertTrue($val);
		sleep(1);
		$val = Tp_Session::have('testHave');
		$this->assertFalse($val);
	}
	public function testConfig() {
		Tp_Session::config(array(
			'encode' => true
		));
		$now = mktime(0,0,0);
		$name = C('session=>prefix').'testConfig';
		Tp_Session::set('testConfig','testConfig');
		$this->assertNotEquals(($now.'=>0=>').'testConfig',$_SESSION[$name]);
		Tp_Session::config(array(
			'prefix' => 'test_'
		));
		Tp_Session::set('testConfig2','testConfig');
		$this->assertArrayHasKey('test_testConfig2',$_SESSION);
		Tp_Session::config(array(
			'expire' => 2
		));
		Tp_Session::set('testConfig3','t');
		sleep(1);
		$val = Tp_Session::have('testConfig3');
		$this->assertTrue($val);
		sleep(2);
		$val = Tp_Session::have('testConfig3');
		$this->assertFalse($val);
	}
	public function testRemove() {
		Tp_Session::config(array(
			'prefix' => 't_'
		));
		Tp_Session::set('testRemove','t');
		$this->assertArrayHasKey('t_testRemove',$_SESSION);
		Tp_Session::remove('testRemove');
		$this->assertArrayNotHasKey('t_testRemove',$_SESSION);
	}
	public function testClear() {
		Tp_Session::config(array(
			'prefix' => 't_'
		));
		Tp_Session::set('testClear1','t');
		Tp_Session::set('testClear2','t');
		$this->assertArrayHasKey('t_testClear1',$_SESSION);
		$this->assertArrayHasKey('t_testClear2',$_SESSION);
		Tp_Session::clear();
		$this->assertArrayNotHasKey('t_testClear1',$_SESSION);
		$this->assertArrayNotHasKey('t_testClear2',$_SESSION);
	}
	public function testGet() {
		Tp_Session::set('testGet','t');
		$val = Tp_Session::get('testGet');
		$this->assertEquals('t',$val);
	}
}
