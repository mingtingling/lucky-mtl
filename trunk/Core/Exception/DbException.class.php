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
 * Toper 数据库信息的异常
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Exception
 * @author mingtingling
 * @version 1.1
 + --------------------------------------------------
 */
 
tp_include(TP_PATH.'/Core/Exception/Exception.class.php');
class Tp_DbException extends Tp_Exception {

	const CONNECTION_FAILED = 1;
	const LOGIN_FAILED = 2;
	const PERMISSION_DENIED = 3;
	const INVALID_TYPE_DRIVER = 4;
	const NOT_FOUNDED_PROPERTY = 5;
	const MORE_THAN_ONE_ROW = 6;
	const NONE_EXISTS_SETTER = 7;
	const NONE_EXISTS_GETTER = 8;
	const NO_SUPPORT_ONLY_ONE_COL = 9;
	public function __construct($code = 0) {
		switch($code) {
			case Tp_DbException::CONNECTION_FAILED:
				$msg = "the database connection failed";
				break;
			case Tp_DbException::LOGIN_FAILED:
				$msg = "logging to database failed";
				break;
			case Tp_DbException::PERMISSION_DENIED:
				$msg = "permission denied";
				break;
			case Tp_DbException::INVALID_TYPE_DRIVER:
				$msg = "invalid type of dirver";
				break;
			case Tp_DbException::NOT_FOUNDED_PROPERTY:
				$msg = "not founded this property";
				break;
			case Tp_DbException::MORE_THAN_ONE_ROW:
				$msg = "this function no support fetched more than row";
				break;
			case Tp_DbException::NONE_EXISTS_SETTER:
				$msg = "did not exists this setter";
				break;
			case Tp_DbException::NONE_EXISTS_GETTER:
				$msg = "did not exists this getter";
				break;
			case Tp_DbException::NO_SUPPORT_ONLY_ONE_COL:
				$msg = "this function no support data only one col";
				break;
			default:
				$msg = "unknown error";
				break;
		}
		parent::__construct($msg,$code);
	}
}