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
 * View的Smarty(为兼容smarty而编写的视图类)
 * 具体使用方法请查询smarty的使用方法
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage View
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */
 
tp_include(APP_PATH.C('view=>smartyPath').'/Smarty.class.php'); //导入smarty

class Tp_SmartyView extends Tp {

	private $_smarty = null;
	function __construct() {
		$this->_smarty = new Smarty();
		$this->_smarty->caching = C('cache=>viewCacheOn');
		$this->_smarty->template_dir = MODULES_PATH.'/Views';
		$this->_smarty->compile_dir = APP_PATH.C('cache=>path');
		$this->_smarty->cache_dir = APP_PATH.C('cache=>path');
		$this->_smarty->left_delimiter = C('view=>leftDelimiter');
		$this->_smarty->right_delimiter = C('view=>rightDelimiter');
	}

	/**
	+------------------------------------------------
	* assign
	+------------------------------------------------
	* @access public
	* @param string $name
	* @param mixed $val
	* @return void
	+------------------------------------------------
	*/
	public function assign($name,$val) {
		$this->_smarty->assign($name,$val);
	}

	/**
	+------------------------------------------------
	* display
	+------------------------------------------------
	* @access public
	* @param string $tpl
	* @return void
	+------------------------------------------------
	*/
	public function display($tpl) {
		$this->_smarty->display($tpl);
	}
}