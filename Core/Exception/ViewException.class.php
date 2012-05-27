<?php
// +-------------------------------------------------
// | Version:Toper 1.1
// +-------------------------------------------------
// | Author:mingtingling 717547858@qq.com
// +-------------------------------------------------
// | Copyright www.qingyueit.com
// +-------------------------------------------------

/**
 +---------------------------------------------------
 * Toper 视图数据的异常
 +---------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Exception
 * @author mingtingling
 * @version 1.1
 + --------------------------------------------------
 */

tp_include(TP_PATH.'/Core/Exception/Exception.class.php');
class Tp_ViewException extends Tp_Exception {

	const NOT_FOUND_VIEW = 21;
	public function __construct($code = 0) {
		switch($code) {
			case Tp_ViewException::NOT_FOUND_VIEW:
				$msg = "did not find the view in you config file";
				break;
			default:
				$msg = "unknown error";
				break;
		}
		parent::__construct($msg,$code);
	}
}