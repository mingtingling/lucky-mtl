<?php
// +------------------------------------------------
// | Version:Toper 1.1
// +------------------------------------------------
// | Author:mingtingling 717547858@qq.com
// +------------------------------------------------
// | Copyright www.qingyueit.com
// +------------------------------------------------

/**
 +---------------------------------------------------
 * Toper 将array转化为object
 * 本类在从数据库返回的数据是一个非关联数组(已转化为对象)的时候使用
 +---------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Db
 * @author mingtingling
 * @version 1.1
 + --------------------------------------------------
 */

class Tp_DbArrayObject extends Tp {
	
	private $_arr = array();

	function __construct($arr = array()) {
		$this->_arr = $arr;
	}

	/**
	 + -------------------------------------------------------------------------
	 * 通过字符串方式返回
	 + -------------------------------------------------------------------------
	 * @access public
	 * @param int $pos
	 * @return string
	 + -------------------------------------------------------------------------
	 */
	public function getString($pos) {
		if(isset($this->_arr[$pos-1])) {
			return (string)$this->_arr[$pos-1];
		} else {
			throw new Exception("非法的下标");
		}
	}

	/**
	 + -------------------------------------------------------------------------
	 * 以整型数字返回
	 + -------------------------------------------------------------------------
	 * @access public
	 * @param int $pos
	 * @return int
	 + -------------------------------------------------------------------------
	 */
	public function getInt($pos) {
		if(isset($this->_arr[$pos-1])) {
			return intval($this->_arr[$pos-1]);
		} else {
			throw new Exception("非法的下标");
		}
	}

	/**
	 + -------------------------------------------------------------------------
	 * 以浮点型数字返回
	 + -------------------------------------------------------------------------
	 * @access public
	 * @param int $pos
	 * @return int
	 + -------------------------------------------------------------------------
	 */
	public function getFloat($pos) {
		if(isset($this->_arr[$pos-1])) {
			return floatval($this->_arr[$pos-1]);
		} else {
			throw new Exception("非法的下标");
		}
	}

	/**
	 + -------------------------------------------------------------------------
	 * 以日期返回
	 + -------------------------------------------------------------------------
	 * @access public
	 * @param int $pos
	 * @return string
	 + -------------------------------------------------------------------------
	 */
	public function getDate($pos) {
		if(isset($this->_arr[$pos-1])) {
			return date('Y-m-d',strtotime($this->_arr[$pos-1]));
		} else {
			throw new Exception("非法的下标");
		}
	}

	/**
	 + -------------------------------------------------------------------------
	 * 以时间返回
	 + -------------------------------------------------------------------------
	 * @access public
	 * @param int $pos
	 * @return string
	 + -------------------------------------------------------------------------
	 */
	public function getTime($pos) {
		if(isset($this->_arr[$pos-1])) {
			return date('H:i:s',strtotime($this->_arr[$pos-1]));
		} else {
			throw new Exception("非法的下标");
		}
	}

	/**
	 + -------------------------------------------------------------------------
	 * 以日期时间返回
	 + -------------------------------------------------------------------------
	 * @access public
	 * @param int $pos
	 * @return string
	 + -------------------------------------------------------------------------
	 */
	public function getDateTime($pos) {
		if(isset($this->_arr[$pos-1])) {
			return date('Y-m-d H:i:s',strtotime($this->_arr[$pos-1]));
		} else {
			throw new Exception("非法的下标");
		}
	}
}