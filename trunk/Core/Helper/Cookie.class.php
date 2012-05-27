<?php
// +------------------------------------------------
// | Version:Toper 1.1
// +------------------------------------------------
// | Author:sunlin
// | Author:mingtingling 717547858@qq.com
// +------------------------------------------------
// | Copyright www.qingyueit.com
// +------------------------------------------------

/**
 +--------------------------------------------------
 * Toper Cookie信息的处理
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Helper
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */

class Tp_Cookie extends Tp {

	private static $_canUseCookie = true;
	//是否可以使用cookie
	private static $_isTested = false;
	//是否已经测试是否可以使用cookie


	/**
	+ --------------------------------------------------
	* 是否可以使用cookie
	+ --------------------------------------------------
	* @access public
	* @static
	* @param void
	* @return bool
	+ --------------------------------------------------
	*/
	public static function canUse() {
		if(true === self::$_isTested) {
			return self::$_canUseCookie;
		} else {
			setcookie(C('cookie=>prefix').'testCanUse','test',time()+2);
			if(isset($_COOKIE[C('cookie=>prefix').'testCanUse'])) {
				self::$_canUseCookie = true;
				self::$_isTested = true;
				setcookie(C('cookie=>prefix').'testCanUse','test',time()-1);
				unset($_COOKIE[C('cookie=>prefix').'testCanUse']);
				return true;
			} else {
				self::$_canUseCookie = false;
				self::$_isTested = true;
				return false;
			}
		}
	}


	/**
	+ --------------------------------------------------
	* 设置某个cookie的值
	+ --------------------------------------------------
	* @access public
	* @static
	* @param string $name
	* @param mixed $value
	* @param int $expire 过期时间
	* @param string $path 有效路径
	* @param string $domain
	* @return bool
	+ --------------------------------------------------
	*/
	public static function set($name, $value,$expire = null,$path = null,$domain = null){
		$expire = empty($expire)?C('cookie=>expire'):$expire;
		$path = empty($path)?C('cookie=>path'):$path;
		$domain = empty($domain)?C('cookie=>domain'):$domain;
		$value = (true === C('cookie=>encode')) ? base64_encode($value) : $value;
		return setcookie(C('cookie=>prefix').$name,$value,time()+$expire,$path,$domain);
	}

	/**
	+ --------------------------------------------------
	* 清除某个cookie值
	+ --------------------------------------------------
	* @access public
	* @static
	* @param string $name
	* @return void
	+ --------------------------------------------------
	*/
	public static function remove($name){
		if(isset($_COOKIE[C('cookie=>prefix').$name])) {
			setcookie(C('cookie=>prefix'),'',time()-1);
			unset($_COOKIE[C('cookie=>prefix').$name]);
		} else {
			return ;
		}
	}


	/**
	+ --------------------------------------------------
	* 获得某个cookie的值
	+ --------------------------------------------------
	* @access public
	* @static
	* @param string $name
	* @return mixed
	+ --------------------------------------------------
	*/
	public static function get($name){
		if(isset($_COOKIE[C('cookie=>prefix').$name])) {
			return (true === C('cookie=>encode'))?base64_decode($_COOKIE[C('cookie=>prefix').$name]):$_COOKIE[C('cookie=>prefix').$name];
		} else {
			return false;
		}
	}

	/**
	+ --------------------------------------------------
	* 清除所有cookie值
	+ --------------------------------------------------
	* @access public
	* @static
	* @param void
	* @return void
	+ --------------------------------------------------
	*/
	public static function clear(){
		if(!isset($_COOKIE)) {
			return ;
		} else {
			foreach($_COOKIE as $key=>$val) {
				if(0 === stripos($key,C('cookie=>prefix'))) {
					//删除以prefix开头的所有Cookie
					setcookie($key,$val,time()-1);
					unset($_COOKIE[$key]);
				}
			}
		}
	}

	/**
	+ --------------------------------------------------
	* 是否存在某个cookie值
	+ --------------------------------------------------
	* @access public
	* @static
	* @param string $name
	* @return bool
	+ --------------------------------------------------
	*/
	public static function have($name) {
		return isset($_COOKIE[C('cookie=>prefix').$name]);
	}

	/**
	+ --------------------------------------------------
	* 修改全局配置
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
				C('cookie=>'.$key,$val);
			}
			return ;
		} else {
			tp_include(TP_PATH.'/Core/Exception/CommonException.class.php');
			throw new Tp_CommonException(Tp_CommonException::INCORRECT_VAR_TYPE);
		}
	}
}