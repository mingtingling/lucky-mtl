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
 * Toper 异常捕获的基类
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Exception
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */

class Tp_Exception extends Exception {

	/**
	 + ----------------------------------------------
	 * 将所有的异常打印出来
	 + ----------------------------------------------
	 * @access public
	 * @param void
	 * @return void
	 + ----------------------------------------------
	 */
	public function __toString() {
		return parent::__toString();
		/*
		return parent::__toString();
		$trace = $this->getTrace();
		//print_r ($trace);
		echo $this->getTraceAsString();
		//$error = array();
		//echo $this->code;
		//echo $this->message;
		return "\n".parent::__toString();
		*/
		
	}
}