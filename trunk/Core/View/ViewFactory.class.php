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
 * View的工厂(用户透明)
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage View
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */
class Tp_ViewFactory extends Tp {

	/**
	+---------------------------------------------------------------
	* 构造函数
	+---------------------------------------------------------------
	* @access public
	* @static
	* @param void 
	* @return object
	+---------------------------------------------------------------
	*/
	public static function factory() {
		switch(C('view=>type')) {
			case 'tp':
				tp_include(TP_PATH.'/Core/View/View.class.php');
				return new Tp_View();
				break;
			case 'smarty':
				tp_include(TP_PATH.'/Core/View/SmartyView.class.php');
				return new Tp_SmartyView();
				break;
			default:
				tp_include(TP_PATH.'/Core/Exception/ViewException.class.php');
				throw new Tp_ViewException(Tp_ViewException::NOT_FOUND_VIEW);
		}
	}
}