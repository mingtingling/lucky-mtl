<?php

define('APP_PATH',dirname(__FILE__).'/../../../../..');
define('MODULES_PATH',APP_PATH.'/Modules');
define('TP_PATH',APP_PATH.'/Library/Toper');
define('PUBLIC_PATH',APP_PATH.'/Public');
require_once(dirname(__FILE__).'/../../../Common/function.php');
require_once(dirname(__FILE__).'/../../../Core/Tp.class.php');
require_once(dirname(__FILE__).'/../../../Cache/Cache.class.php');
//require_once(dirname(__FILE__).'/../../../Core/Debug.class.php');
//require_once(dirname(__FILE__).'/../../../Core/App.class.php');
//require_once(dirname(__FILE__).'/../../../Core/Route.class.php');
require_once(dirname(__FILE__).'/../../../Cache/FileCache.class.php');

class FileCacheTest extends PHPUnit_Framework_TestCase {
	protected $_fileCache = null;
	public function __construct() {
		//$frontController = Tp_FrontController::getInstance();
		//$frontController->run();
	}
	public function setUp() {
		$this->_fileCache = new Tp_FileCache();
	}
	public function testSet() {
		$status = $this->_fileCache->set('test','test');
		//$this->assertEquals(true,$status);
	}
	public function testA() {
		$this->assertEquals(true,false);
	}
}