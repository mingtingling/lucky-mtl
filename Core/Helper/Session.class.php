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
 * Toper Session信息的处理
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Helper
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */

class Tp_Session extends Tp {

	/**
	+ --------------------------------------------------
	* 设置某个session的值
	* 支持:Tp_Session::set('tes','testVal');
	+ --------------------------------------------------
	* @access public
	* @static
	* @param string $name
	* @param mixed $value
	* @param int $expire 过期时间
	* @return void
	+ ---------------------------------------------------
	*/
	public static function set($name, $value,$expire = null){
		$now = mktime(0,0,date('s'));
		$expire = (null === $expire) ? C('session=>expire') : intval($expire);
		$_SESSION[C('session=>prefix').$name] = 
			(($now.'=>'.$expire.'=>')
			.((true === C('session=>encode'))?base64_encode($value):$value));
	}

	/**
	+ --------------------------------------------------
	* 获得某个session的值
	* 支持:Tp_Session::get('test');
	+ --------------------------------------------------
	* @access public
	* @static
	* @param string $name
	* @return mixed
	+ --------------------------------------------------
	*/
	public static function get($name){
		if(self::have($name)) {
			$val = $_SESSION[C('session=>prefix').$name];
			$pos = strpos($val,'=>');
			$pos = strpos($val,'=>',$pos+2);
			$val = substr($val,$pos+2);
			return (true === C('session=>encode'))?base64_decode($val):$val;
		} else {
			return false;
		}
	}

	/**
	+ --------------------------------------------------
	* 清除某一个session值
	* 支持:Tp_Session::remove('test');
	+ --------------------------------------------------
	* @access public
	* @static
	* @param string $name
	* @return bool
	+ --------------------------------------------------
	*/
	public static function remove($name) {
		if(isset($_SESSION[C('session=>prefix').$name])) {
			unset($_SESSION[C('session=>prefix').$name]);
			return true;
		} else {
			return false;
		}
	}


	/**
	+ --------------------------------------------------
	* 清除本框架的所有session值
	* 支持:Tp_Session::clear();
	+ --------------------------------------------------
	* @access public
	* @static
	* @param void
	* @return void
	+ --------------------------------------------------
	*/
	public static function clear(){
		if(!isset($_SESSION)) {
			return ;
		} else {
			foreach($_SESSION as $key=>$val) {
				if(0 === stripos($key,C('session=>prefix'))) {
					unset($_SESSION[$key]);
				}
			}
		}
	}

	/**
	+ --------------------------------------------------
	* 是否存在某个session值
	* 支持:Tp_Session::have('test');
	+ --------------------------------------------------
	* @access public
	* @static
	* @param string $name
	* @return bool
	+ --------------------------------------------------
	*/
	public static function have($name) {
		if(!isset($_SESSION[C('session=>prefix').$name])) {
			return false;
		}
		$val = $_SESSION[C('session=>prefix').$name];
		$pos = strpos($val,'=>');
		$setTime = intval(substr($val,0,$pos));
		$expire = substr($val,$pos+2,(strpos($val,'=>',$pos+2)-$pos-2));
		if('0' === $expire) {
			return true;
		}
		$now = mktime(0,0,date('s'));
		if($now <= $setTime+intval($expire)) {
			return true;
		} else {
			self::remove($name);
			return false;
		}
	}

	/**
	+ --------------------------------------------------
	* 修改全局配置
	* 支持:Tp_Session::config(array('prefix'=>'test_'));
	+ --------------------------------------------------
	* @access public
	* @static
	* @param string $config
	* @return void
	+ --------------------------------------------------
	*/
	public static function config($config = array()) {
		if(is_array($config)) {
			foreach($config as $key=>$val) {
				C('session=>'.$key,$val);
			}
			return ;
		} else {
			tp_include(TP_PATH.'/Core/Exception/CommonException.class.php');
			throw new Tp_CommonException(Tp_CommonException::INCORRECT_VAR_TYPE);
		}
	}
}