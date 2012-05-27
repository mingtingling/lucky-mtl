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
 * Toper 异常捕获,并且记录到日志文件中
 * 比较严重的错误才记录
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Exception
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */

class Tp_LogException extends Exception {

	public function __construct($msg = null,$code = 0,$file = TP_PATH.'~Cache/Log/log.log') {
		$this->_log($file);
		parent::__construct($msg,$code);
	}

	/**
	+ ----------------------------------------------------------
	* 记录到错误日志
	+ ----------------------------------------------------------
	* @access protected
	* @param string $file 记录日志的文件路径
	* @return void
	+ ----------------------------------------------------------
	*/

	protected function _log($file) {
		//异常日志文件
		file_put_contents($file,$this->_toString(),FILE_APPEND);
	}
}