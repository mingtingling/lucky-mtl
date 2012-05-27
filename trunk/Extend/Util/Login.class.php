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
 * Toper 登录信息的处理
 +--------------------------------------------------
 * @category Toper
 * @package Extend
 * @subpackage Util
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */

tp_include(TP_PATH.'/Core/Helper/Session.class.php');
tp_include(TP_PATH.'/Core/Helper/Encode.class.php');
tp_include(TP_PATH.'/Core/Helper/Decode.class.php');
class Tp_Login extends Tp {

	/**
	+--------------------------------------------------------
	* 是否已经登录
	+--------------------------------------------------------
	* @access public
	* @static
	* @param void
	* @return bool
	+--------------------------------------------------------
	*/
	public static function isLogin() {
		return Tp_Session::have('_user');
	}

	/**
	+--------------------------------------------------------
	* 注销
	+--------------------------------------------------------
	* @access public
	* @static
	* @param void
	* @return void
	+--------------------------------------------------------
	*/
	public static function logout() {
		Tp_Session::remove('_user');
	}
	/**
	+--------------------------------------------------------
	* 登录
	+--------------------------------------------------------
	* @access public
	* @static
	* @param $val
	* @return bool,如果返回false代表已经登录
	+--------------------------------------------------------
	*/
	public static function setLogin($val) {
		if(Tp_Session::have('_user')) {
			return false;
		} else {
			Tp_Session::set('_user',Tp_Encode::tp($val));
			return true;
		}
	}

	/**
	+--------------------------------------------------------
	* 得到登录信息
	+--------------------------------------------------------
	* @access public
	* @static
	* @param void
	* @return string
	+--------------------------------------------------------
	*/
	public static function getLogin() {
		return Tp_Decode::tp(Tp_Session::get('_user'));
	}
}