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
require_once(dirname(__FILE__).'/../../../Helper/Encode.class.php');
require_once(dirname(__FILE__).'/../../../Helper/Decode.class.php');
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

//这里由于encode,decode是相逆的，所以两个就一起测试了
class EncodeTest extends PHPUnit_Framework_TestCase {
	public function testTp() {
		$encode = Tp_Encode::tp('it is a test');
		$this->assertNotEquals('it is a test',$encode);
		$decode = Tp_Decode::tp($encode);
		$this->assertEquals('it is a test',$decode);
	}
	public function testUrl() {
		$encode = Tp_Encode::url('it is a test');
		$this->assertNotEquals('it is a test',$encode);
		$decode = Tp_Decode::url($encode);
		$this->assertEquals('it is a test',$decode);
	}
	public function testPassword() {
		$encode = Tp_Encode::password('it is a test');
		$encode2 = Tp_Encode::password('it is a test2');
		$this->assertNotEquals($encode,$encode2);
		
	}

}
