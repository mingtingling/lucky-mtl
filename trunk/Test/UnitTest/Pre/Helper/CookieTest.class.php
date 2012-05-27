<?php
require_once(dirname(__FILE__).'/../../../Core/PHPUnit.class.php');
class CookieTest extends Tp_PHPUnit {
	public function __construct() {
		parent::__construct();
		$config = include (APP_PATH.'/UserApps/Configs/config.php');
		$this->setConfig($config);
		require_once(dirname(__FILE__).'/../../../Core/Helper/Cookie.class.php');
		$_COOKIE = array();
	}
	public function testCanUse() {
		/*
		$canUse = Tp_Cookie::canUse();
		$this->assertTrue($canUse);
		*/
	}
	public function testSet() {
		
		Tp_Cookie::config(array(
			'prefix' => 't_'
		));
		$this->assertArrayNotHasKey('t_testSet',$_COOKIE);
		Tp_Cookie::set('testSet','test');
		$this->assertArrayHasKey('t_testSet',$_COOKIE);
	
	}
}
