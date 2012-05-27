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
 * Toper 调试类
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */

class Tp_Debug extends Tp {

	protected static $_startTime = null;
	//开始时间
	protected static $_endTime = null;
	//结束时间
	protected static $_time = null;
	//计时器的时间

	private function __construct() {}
	private function __clone() {}
	/**
	 + ------------------------------------------------
	 * 调试开始(用户透明)
	 + ------------------------------------------------
	 * @access public
	 * @static
	 * @param void
	 * @return void
	 + ------------------------------------------------
	 */
	public static function start() {
		//调试开始
		self::$_startTime = microtime();
	}

	/**
	 + -------------------------------------------------
	 * 计时器开始
	 * 支持:Tp_Debug::counterStart();
	 + -------------------------------------------------
	 * @access public
	 * @static
	 * @param void
	 * @return void
	 + -------------------------------------------------
	 */
	 public static function counterStart() {
		self::$_time = microtime();
	 }

	/**
	 + -------------------------------------------------
	 * 重置计时器
	 * 支持:Tp_Debug::counterReset();
	 + -------------------------------------------------
	 * @access public
	 * @static
	 * @param void
	 * @return void
	 + -------------------------------------------------
	 */
	 public function counterReset() {
	 	self::$_time = null;
	 }

	/**
	 + -------------------------------------------------
	 * 计时器结束，并显示计时器的数值
	 * 支持: Tp_Debug::counterEnd();
	 + -------------------------------------------------
	 * @access public
	 * @static
	 * @param void
	 * @return void
	 + -------------------------------------------------
	 */
	 public static function counterEnd() {
	 	if(self::$_time) {
		 	$end = microtime();
		 	echo "<br/>此次计时器数值为:".($end-self::$_time)."秒";
	 	} else {
	 		echo "<font color = 'red'>计时器没有开始,不能结束!</font>";
	 	}
	 }

	/**
	 + -------------------------------------------------
	 * 结束并显示所有调试信息(用户透明)
	 + -------------------------------------------------
	 * @access public
	 * @static
	 * @param void
	 * @return void
	 + -------------------------------------------------
	 */
	public static function end() {
		self::$_endTime = microtime();
		echo("<br/>系统执行时间为:".(self::$_endTime - self::$_startTime)."秒.");
		self::getIncludedFiles();
	}

	/**
	 + ---------------------------------------------------
	 * 显示包含的文件(用户透明)
	 + ---------------------------------------------------
	 * @access public
	 * @static
	 * @param void
	 * @return void
	 + ---------------------------------------------------
	 */
	public static function getIncludedFiles() {
		$includedFiles = get_included_files();
		echo("<br/>系统载入的文件有:");
		$count = 1;
		foreach($includedFiles as $tmp) {
			echo("<br/>  ".$count.".  ".$tmp);
			$count ++;
		}
	}
}