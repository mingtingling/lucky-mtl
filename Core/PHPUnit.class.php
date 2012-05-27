<?php
// +------------------------------------------------
// | Version:Toper 1.1
// +------------------------------------------------
// | Author:mingtingling 717547858@qq.com
// +------------------------------------------------
// | Copyright www.qingyueit.com
// +------------------------------------------------

/**
 +--------------------------------------------------
 * 单元测试的基类
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */

define('APP_PATH',dirname(__FILE__).'/../../..');
define('MODULES_PATH',APP_PATH.'/Modules');
define('TP_PATH',APP_PATH.'/Library/Toper');
define('PUBLIC_PATH',APP_PATH.'/Public');
class Tp_PHPUnit extends PHPUnit_Framework_TestCase{
	function __construct() {
		require_once TP_PATH.'/Core/function.php';
		tp_include(TP_PATH.'/Core/Tp.class.php');
	}

	/**
	 + -------------------------------------------------
	 * 设置配置信息
	 + -------------------------------------------------
	 * @access public
	 * @param array $config
	 * @return void
	 + -------------------------------------------------
	 */
	public function setConfig($config) {
		C($config);
		if(true === C('sessionAutoStart')) {
			session_start();
		}
		if(C('timeZone') && function_exists('date_default_timezone_set')) {
			date_default_timezone_set(C('timeZone'));
		}
	}
}