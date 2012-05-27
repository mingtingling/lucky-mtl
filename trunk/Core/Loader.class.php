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
 * Toper 载入
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */

class Tp_Loader extends Tp {

	private static $_instance = null;

	private function __construct() {
		if(version_compare(PHP_VERSION,'5.1.2', '<')) {
		 	throw new Exception('your version of php not support spl_autoload');
		}
		spl_autoload_register('__autoload');
		spl_autoload_register(array($this,'loadToperInterface'));
		$userDefinedAutoload = C('autoload');
		if($userDefinedAutoload) {
			foreach($userDefinedAutoload as $key => $val) {
				if(!is_numeric($key)) {
					spl_autoload_register(array($key,$val));
				} else {
					spl_autoload_register($val);
				}
			}
		}
	}
	
	private function __clone() {}
	
	/**
	+----------------------------------------------------------
	* 实现载入的静态函数
	* 支持用户自定义导入方法，如果用户在TestClass里面定义了TestMethod
	* 方法来实现自己的自动导入
	* 那么可以使用C('autoload',array('TestClass'=>'TestMethod'))来完成
	* 也支持函数来实现自动导入，如TestFunction来实现导入
	* 可以使用C('autoload',array('TestFunction'))来实现导入
	* 当然，也可以在入口文件index.php中调用前端控制器的init方法来实现
	+----------------------------------------------------------
	* @access public
	* @static
	* @param void
	* @return object
	+----------------------------------------------------------
	*/
	public static function load() {
		if(!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	+----------------------------------------------------------
	* 实现载入Toper Interface
	+----------------------------------------------------------
	* @access public
	* @static
	* @param string $interface
	* @return bool
	+----------------------------------------------------------
	*/
	public static function loadToperInterface($interface) {
		$interfacePath = TP_PATH.'/Interface/'.substr($interface,1).'.interface.php';
		if(is_file($interfacePath)) {
			tp_include($interfacePath);
			return true;
		} else {
			return false;
		}
	}
}