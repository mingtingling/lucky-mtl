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
 * 路径存放类
 * 这个类存放了各种需要的路径
 * 注意:只有路由之后才能使用这个类
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Helper
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */
class Tp_Path extends Tp {

	/**
	 + -------------------------------------------------------------
	 * 得到public路径(目录)
	 + -------------------------------------------------------------
	 * @access public
	 * @param void
	 * @return string
	 + -------------------------------------------------------------
	 */
	public static function getPublicPath() {
		$publicPath = C('url=>protocol').'://'.U('baseUrl');
		$pos = strripos($publicPath,'index.php');
		if(false !== $pos) {
			$publicPath = substr($publicPath,0,$pos-1);
		}
		return $publicPath;
	}

	/**
	 + -------------------------------------------------------------
	 * 得到base路径
	 + -------------------------------------------------------------
	 * @access public
	 * @param void
	 * @return string
	 + -------------------------------------------------------------
	 */
	public static function getBasePath() {
		return (C('url=>protocol').'://'.U('baseUrl'));
	}

	/**
	 + -------------------------------------------------------------
	 * 得到JS路径(目录)
	 + -------------------------------------------------------------
	 * @access public
	 * @param void
	 * @return string
	 + -------------------------------------------------------------
	 */
	public static function getJsPath() {
		return self::getPublicPath().'/Js';
	}

	/**
	 + -------------------------------------------------------------
	 * 得到CSS路径(目录)
	 + -------------------------------------------------------------
	 * @access public
	 * @param void
	 * @return string
	 + -------------------------------------------------------------
	 */
	public static function getCssPath() {
		return self::getPublicPath().'/Css';
	}

	/**
	 + -------------------------------------------------------------
	 * 得到当前组的地址
	 + -------------------------------------------------------------
	 * @access public
	 * @param void
	 * @return string
	 + -------------------------------------------------------------
	 */
	public static function getCurrentGroupPath() {
		$group = U('group')?(C('url=>division').U('group')):"";
		return self::getBasePath().$group;
	}

	/**
	 + -------------------------------------------------------------
	 * 得到当前模块的地址
	 + -------------------------------------------------------------
	 * @access public
	 * @param void
	 * @return string
	 + -------------------------------------------------------------
	 */
	public static function getCurrentModulePath() {
		return self::getCurrentGroupPath().C('url=>division').U('module');
	}

	/**
	 + -------------------------------------------------------------
	 * 得到当前控制器的地址
	 + -------------------------------------------------------------
	 * @access public
	 * @param void
	 * @return string
	 + -------------------------------------------------------------
	 */
	public static function getCurrentControllerPath() {
		return self::getCurrentModulePath().C('url=>division').U('controller');
	}

	/**
	 + -------------------------------------------------------------
	 * 得到当前action的地址
	 + -------------------------------------------------------------
	 * @access public
	 * @param void
	 * @return string
	 + -------------------------------------------------------------
	 */
	public static function getCurrentActionPath() {
		return self::getCurrentControllerPath().C('url=>division').U('action');
	}
}