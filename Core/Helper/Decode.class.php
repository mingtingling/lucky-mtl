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
 * Toper 解密
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Helper
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */

class Tp_Decode extends Tp {
	
	/**
	+ --------------------------------------------------------
	* 对本系统定义的加密方法解密
	+ --------------------------------------------------------
	* @access public
	* @static
	* @param string $string 已加密字符串
	* @return string
	+ --------------------------------------------------------
	*/
	public static function tp($string) {
		$key = C('encode=>key');//解密密匙，必须与加密钥匙相同
		$dynKey = substr($string, 0, 40);
		$fixedKey = hash('sha1', $key);
		$dynKeyPart1 = substr($dynKey, 0, 20);
		$dynKeyPart2 = substr($dynKey, 20);
		$fixedKeyPart1 = substr($fixedKey, 0, 20);
		$fixedKeyPart2 = substr($fixedKey, 20);
		$key = hash('sha1', $dynKeyPart1 . $fixedKeyPart1 . $dynKeyPart2 . $fixedKeyPart2);
		$string = isset($string{339}) ? gzuncompress(base64_decode(substr($string, 40))) : base64_decode(substr($string, 40));
		$n = 0;
		$result = '';
		$len = strlen($string);
		for($n = 0; $n < $len; $n++){
			$result .= chr(ord($string{$n}) ^ ord($key{$n % 40}));
		}
		return substr($result, 20, -20);	
	}
	
	/**
	+ --------------------------------------------------------
	* 对URL加密信息的解密
	+ --------------------------------------------------------
	* @access public
	* @static
	* @param string $string 已加密字符串
	* @return string
	+ --------------------------------------------------------
	*/
	public static function url($string) {
		return self::tp(base64_decode($string));
	}
}