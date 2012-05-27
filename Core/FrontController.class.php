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
 * Toper 前端控制器
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @author mingtingling
 * @version 1.1
 + --------------------------------------------------
 */
if(!(defined('APP_PATH'))) exit('没有定义APP_PATH');
if(version_compare(PHP_VERSION, '5.0.0','<')) die('您的PHP版本太低，不能使用本框架');
if(!defined('TP_PATH')) define('TP_PATH',APP_PATH.'/Library/Toper');
if(!defined('PUBLIC_PATH')) define('PUBLIC_PATH',APP_PATH.'/PUBLIC');
if(!defined('MODULES_PATH')) define('MODULES_PATH',APP_PATH.'/UserApps/Modules');
include TP_PATH.'/Core/function.php'; //导入公有函数库
tp_include(TP_PATH.'/Core/Tp.class.php'); //导入系统基类
tp_include(TP_PATH.'/Core/App.class.php'); //导入应用

class Tp_FrontController extends Tp {

	private static $_instance = null; //类的实例

	private function __construct($useDefaultConfig = true) {
		if(true === $useDefaultConfig) {
			$config = include TP_PATH.'/config.php';
			C($config); //将配置信息记录
		}
	}
	private function __clone() {}

	public function __destruct() {
		if(true === C('appDebug')) {
			Tp_Debug::end();
		}
	}

	/**
	+ --------------------------------------------------------
	* 得到前端控制器的实例
	* 支持:$test = Tp_FrontController::getInstance();
	* 支持:$test = Tp_FrontController::getInstance(false);
	+ --------------------------------------------------------
	* @access public
	* @static
	* @param bool $useDefaultConfig 是否使用默认的配置信息
	* @return object
	+ -------------------------------------------------------
	*/
	public static function getInstance($useDefaultConfig = true) {
		if(!(self::$_instance instanceof self)) {
			self::$_instance = new self($useDefaultConfig);
		}
		return self::$_instance;
	}
	/**
	+ --------------------------------------------------------
	* 初始化信息
	* 支持:$test->init(array('appDebug' => true));
	* 支持:$config = Tp_ConfigFactory::factory('test.xml');
	*			$test->init($config->get());
	* 			或 $test->init($config->get('appDebug'));
	* 			或 $test->init($config->getAppDebug()); 此方法不建议使用
	+ --------------------------------------------------------
	* @access public
	* @param array $arr 配置信息
	* @return void
	+ -------------------------------------------------------
	*/
	public function init($arr = array()) {
		if(is_array($arr)) {
			C($arr);
		} else {
			throw new Exception('初始化参数非法');
		}
	}

	/**
	+ --------------------------------------------------------
	* 框架配置信息的预处理(用户透明)
	+ --------------------------------------------------------
	* @access private
	* @param void
	* @return void
	+ -------------------------------------------------------
	*/
	private function _preProcessConfig() {
		if(true === C('appDebug')) {
			tp_include(TP_PATH.'/Core/Debug.class.php');
			Tp_Debug::start();
		}
		if(true === C('sessionAutoStart')) {
			session_start();
		}
		if(function_exists('date_default_timezone_set')) {
			date_default_timezone_set(C('timeZone'));
		}
		if(true === C('autoloadRegister')) {
			Tp_Loader::load();
		}
		error_reporting(C('errorReporting'));
	}

	/**
	+ --------------------------------------------------------
	* 框架的运行
	* 支持:$test->run();
	* 注意:run()必须在getInstance()和init()之后调用
	+ --------------------------------------------------------
	* @access public
	* @param void
	* @return void
	+ -------------------------------------------------------
	*/
	public function run() {
		$this->_preProcessConfig();
		Tp_App::run();
	}
}