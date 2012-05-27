<?php
// +------------------------------------------------
// | Version:Toper 1.1
// +------------------------------------------------
// | Author:mingtingling  717547858@qq.com
// +------------------------------------------------
// | Copyright www.qingyueit.com
// +------------------------------------------------

/**
 +--------------------------------------------------
 * Toper Tp_IniConfig 处理ini配置信息,此类为辅助类
 * 外部不应该直接调用，而是调用Tp_ConfigFactory
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Config
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */
class Tp_IniConfig extends Tp {

	/**
	 + --------------------------------------------
	 * parse ini files
	 * 注意:本框架对数据类型要求比较严格
	 * ini解析出来的默认为字符串
	 * 如果需要解析成int,float,bool
	 * 那么需要使用如下格式:
	 * test = int:2
	 * test = float:3.2
	 * test = bool:true
	 + --------------------------------------------
	 * @access public
	 * @static
	 * @param string $path
	 * @return array
	 + --------------------------------------------
	 */
	public static function parse($path) {
		return self::_parseArr(parse_ini_file($path,true));
	}
	/**
	 + --------------------------------------------
	 * 解析ini文件的数据类型
	 + --------------------------------------------
	 * @access private
	 * @static
	 * @param array $arr
	 * @return array
	 + --------------------------------------------
	 */
	private static function _parseArr($arr) {
		foreach($arr as $key => $data) {
			if(is_array($data)) {
				$arr[$key] = self::_parseArr($data);
			} else {
				//字符串,需要解析它的数据类型
				if(0 === stripos($data,'int:')) {
					$arr[$key] = intval(substr($data,4));
				} else if(0 === stripos($data,'float:')) {
					$arr[$key] = floatval(substr($data,6));
				} else if(0 === stripos($data,'bool:')) {
					$arr[$key] = (($data == '') || ($data == 'false')) ? false : true;
				} else {
				}
			}
		}
		return $arr;
	}
}