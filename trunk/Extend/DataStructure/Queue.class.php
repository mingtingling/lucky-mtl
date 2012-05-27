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
 * 队列,支持不同数据类型的元素入同一队列
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage DataStrucutre
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */
class Tp_Queue extends Tp implements Countable {
	protected $_queue = array();
	//存储队列元素的数组
	protected $_len = 0;
	//队列长度

	/**
	 + ----------------------------------------------------------
	 * 得到队列的长度
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
	 * 得到队列的长度(实现countable接口)
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
	 * 清空队列
	 + ----------------------------------------------------------
	 * @access public
	 * @param void
	 * @return void
	 + ----------------------------------------------------------
	 */
	public function clear() {
		foreach($this->_queue as $key => $val) {
			//释放内存空间
			unset($this->_queue[$key]);
		}
		$this->_queue = array();
		$this->_len = 0;
	}

	/**
	 + ----------------------------------------------------------
	 * 将元素入队列
	 + ----------------------------------------------------------
	 * @access public
	 * @param mixed 可以将一个或者多个元素入队列
	 * @return void
	 + ----------------------------------------------------------
	 */
	public function enqueue($var) {
		if(is_array($var)) {
			foreach($var as $tmp) {
				$this->_queue[] = $tmp;
				$this->_len ++;
			}
		} else {
			$this->_queue[] = $var;
			$this->_len ++;
		}
	}

	/**
	 + ----------------------------------------------------------
	 * 查看队列是否为空
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
	 * 将元素出队列
	 + ----------------------------------------------------------
	 * @access public
	 * @param int 出队列的元素个数
	 * @return mixed
	 + ----------------------------------------------------------
	 */
	public function dequeue($len = 1) {
		if(!is_int($len) || $len < 1 || ($len > $this->length())) {
			throw new Exception("参数不是整数或者范围不正确");
		}
		if(1 === $len) {
			$this->_len --;
			return array_shift($this->_queue);
		}
		$tmpSaveArr = array();
		for($count = 0;$count < $len;$count ++) {
			$this->_len --;
			$tmpSaveArr[] = array_shift($this->_queue);
		}
		return $tmpSaveArr;
	}
}
