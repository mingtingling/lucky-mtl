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
 * Toper 将array转化为object的列表
 * 本类使用了迭代器模式
 +---------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Db
 * @author mingtingling
 * @version 1.1
 + --------------------------------------------------
 */

class Tp_DbArrayObjectList extends Tp implements Iterator,Countable {
	
	private $_objs = array();
	private $_valid = null;
	private $_isHeadOfArr = true;
	

	/**
	 + -------------------------------------------------------------------------
	 * 插入对象，必须在迭代之前进行,本函数对用户透明
	 + -------------------------------------------------------------------------
	 * @access public
	 * @param object $obj
	 * @return void
	 + -------------------------------------------------------------------------
	 */
	public function insertObject($obj) {
		$this->_objs[] = $obj;
	}

	public function next() {
		$this->_valid = (false === next($this->_objs)) ? false : true;
	}

	public function rewind() {
		$this->_valid = (false === reset($this->_objs)) ? false : true;
	}
	public function valid() {
		if(true === $this->_isHeadOfArr) {
			$this->_isHeadOfArr = false;
			return (isset($this->_objs[0])) ? true : false;
		} else {
			return $this->_valid;
		}
	}
	public function current() {
		return current($this->_objs);
	}
	public function key() {
		return key($this->_objs);
	}
	public function count() {
		return count($this->_objs);
	}
}