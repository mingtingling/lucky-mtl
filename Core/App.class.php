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
 * Toper 构建
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */
 
tp_include(TP_PATH.'/Core/Route.class.php');

class Tp_App extends Tp {

	/**
	 + -----------------------------------------------------
	 * 应用初始化(用户透明)
	 + -----------------------------------------------------
	 * @access protected
	 * @static
	 * @param void
	 * @return void
	 + -----------------------------------------------------
	 */
	static protected function _init() {
		if((true === C('cache=>importFilesCacheOn')) && (!file_exists(TP_PATH.'/~Cache/~app.php'))) {
			self::_bulidCache();
		}
		if(true === C('cache=>importFilesCacheOn')) {
			tp_include(TP_PATH.'/~Cache/~app.php');
		}
		Tp_Route::run();
	}

	/**
	 + ------------------------------------------------------
	 * 创建缓存(用户透明)
	 + ------------------------------------------------------
	 * @access protected
	 * @static
	 * @param void
	 * @return void
	 + ------------------------------------------------------
	 */
	static protected function _bulidCache() {
		$oriContents = file_get_contents(TP_PATH.'/Core/Route.class.php');
		$contents = file_get_contents(TP_PATH.'/Core/Controller.class.php');
		$contents .= file_get_contents(TP_PATH.'/Core/Model.class.php');
		$contents .= file_get_contents(TP_PATH.'/View/View.class.php');
		$contents .= file_get_contents(TP_PATH.'/View/ViewFactory.class.php');
		$contents .= file_get_contents(TP_PATH.'/Db/DbBase.class.php');
		$contents .= file_get_contents(TP_PATH.'/Db/PdoDrive.class.php');
		$contents .= file_get_contents(TP_PATH.'/Db/DbTable.class.php');
		$contents .= file_get_contents(TP_PATH.'/Db/DbRelation.class.php');
		$contents .= file_get_contents(TP_PATH.'/Db/DbFactory.class.php');
		$contents = preg_replace('/<\?php/','',$contents);
		$contents = ($oriContents.$contents);
		file_put_contents(TP_PATH.'/~Cache/~app.php',$contents);
		Tp_FileCompile::shortFiles(TP_PATH.'/~Cache/~app.php',TP_PATH.'/~Cache/~app.php');
	}


	/**
	 + -----------------------------------------------------
	 * 应用的启动(用户透明)
	 + -----------------------------------------------------
	 * @access static public
	 * @static
	 * @param void
	 * @return void
	 + -----------------------------------------------------
	 */
	static public function run() {
		self::_init();
	}
}