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
 * Toper 类信息的异常
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Exception
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */
 
tp_include(TP_PATH.'/Core/Exception/Exception.class.php');
class Tp_ClassException extends Tp_Exception {
	const NONE_EXISTS_METHOD = 11;
	const NONE_EXISTS_CLASS = 12;
	const NONE_EXISTS_VALUE = 13;
	const NONE_EXISTS_STATIC_METHOD = 14;

	public function __construct($code = 0) {
		switch($code) {
			case Tp_ClassException::NONE_EXISTS_METHOD:
				$msg = "the method does not exists";
				break;
			case Tp_ClassException::NONE_EXISTS_CLASS:
				$msg = "the class does not exists";
				break;
			case Tp_ClassException::NONE_EXISTS_VALUE:
				$msg = "the value not exists";
				break;
			case Tp_ClassException::NONE_EXISTS_STATIC_METHOD:
				$msg = "the static method does not exists";
				break;
			default:
				$msg = "unknown error";
				break;
		}
		parent::__construct($msg,$code);
	}
}