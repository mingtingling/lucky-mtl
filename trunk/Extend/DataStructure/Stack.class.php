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
 * 堆栈,支持不同数据类型的元素入同一栈
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage DataStrucutre
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */
class Tp_Stack extends Tp implements Countable {
	protected $_stack = array();
	//存储堆栈元素的数组
	protected $_len = 0;
	//堆栈长度

	/**
	 + ----------------------------------------------------------
	 * 得到堆栈的长度
	 + ----------------------------------------------------------
	 * @access public
	 * @param void
	 * @return int
	 + ----------------------------------------------------------
	 */
	public function length() {
		return $this->_len;
	}

	/**
	 + ----------------------------------------------------------
	 * 计算栈的长度，通过countable接口
	 + ----------------------------------------------------------
	 * @access public
	 * @param void
	 * @return int
	 + ----------------------------------------------------------
	 */
	 public function count() {
	 	return $this->_len;
	 }

	/**
	 + ----------------------------------------------------------
	 * 判断堆栈是否为空
	 + ----------------------------------------------------------
	 * @access public
	 * @param void
	 * @return bool
	 + ----------------------------------------------------------
	 */
	 public function isEmpty() {
	 	return (0 === $this->_len) ? true : false;
	 }


	/**
	 + ----------------------------------------------------------
	 * 清空堆栈
	 + ----------------------------------------------------------
	 * @access public
	 * @param void
	 * @return void
	 + ----------------------------------------------------------
	 */
	public function clear() {
		foreach($this->_stack as $key => $val) {
			unset($this->_stack[$key]);
		}
		$this->_stack = array();
		$this->_len = 0;
	}

	/**
	 + ----------------------------------------------------------
	 * 将元素入栈
	 + ----------------------------------------------------------
	 * @access public
	 * @param mixed 可以将一个或者多个元素入栈
	 * @return void
	 + ----------------------------------------------------------
	 */
	public function push($var) {
		if(is_array($var)) {
			foreach($var as $tmp) {
				$this->_len ++;
				$this->_stack[] = $tmp;
			}
		} else {
			$this->_len ++;
			$this->_stack[] = $var;
		}
	}

	/**
	 + ----------------------------------------------------------
	 * 将元素出栈
	 + ----------------------------------------------------------
	 * @access public
	 * @param int 出栈的元素个数
	 * @return mixed
	 + ----------------------------------------------------------
	 */
	public function pop($len = 1) {
		if(!is_int($len) || $len < 1 || ($len > $this->length())) {
			throw new Exception("参数不是整数或者范围不正确");
		}
		if(1 === $len) {
			$this->_len --;
			return array_pop($this->_stack);
		}
		$tmpSaveArr = array();
		for($count = 0;$count < $len;$count ++) {
			$this->_len --;
			$tmpSaveArr[] = array_pop($this->_stack);
		}
		return $tmpSaveArr;
	}
}
