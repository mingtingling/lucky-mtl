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
 * Toper 常用异常
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Exception
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */
 
tp_include(TP_PATH.'/Core/Exception/Exception.class.php');
class Tp_CommonException extends Tp_Exception {

	const INCORRECT_VAR_TYPE = 51;
	const RANGE_ERROR = 52;
	const NO_PARAMETER = 53;
	public function __construct($code = 0) {
		switch($code) {
			case Tp_CommonException::INCORRECT_VAR_TYPE:
				$msg = "incorrect type of var";
				break;
			case Tp_CommonException::RANGE_ERROR:
				$msg = "range error";
				break;
			case Tp_CommonException::NO_PARAMETER:
				$msg = "did not have parameter";
				break;
			default:
				$msg = "unknown error";
				break;
		}
		parent::__construct($msg,$code);
	}
}