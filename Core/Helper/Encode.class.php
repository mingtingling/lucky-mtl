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
 * Toper 加密
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Helper
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */

class Tp_Encode extends Tp {
	
	/**
	+ --------------------------------------------------------
	* 以本系统定义的加密方法加密
	+ --------------------------------------------------------
	* @access public
	* @static
	* @param string $string 加密字符串
	* @return string
	+ --------------------------------------------------------
	*/
	
	public static  function tp($string) {
		$key = C('encode=>key');//加密 密匙
		$dynKey = hash('sha1', microtime(true));
		$fixedKey = hash('sha1', $key);
		$dynKeyPart1 = substr($dynKey, 0, 20);
		$dynKeyPart2 = substr($dynKey, 20);
		$fixedKeyPart1 = substr($fixedKey, 0, 20);
		$fixedKeyPart2 = substr($fixedKey, 20);
		$key = hash('sha1', $dynKeyPart1 . $fixedKeyPart1 . $dynKeyPart2 . $fixedKeyPart2);
		$string = $fixedKeyPart1 . $string . $dynKeyPart2;
		$n = 0;
		$result = '';
		$len = strlen($string);
		for($n = 0; $n < $len; $n++){
			$result .= chr(ord($string{$n}) ^ ord($key{$n % 40})); 
		}
		return ($dynKey . str_replace('=', '', base64_encode($n > 299 ? gzcompress($result) : $result)));
	}
	
	/**
	+ --------------------------------------------------------
	* 对即将通过URL传递的参数进行加密
	* 如:http://localhost/M/C/A/it is a test
	* 加密后:http://localhost/M/C/A/加密后的字符串
	+ --------------------------------------------------------
	* @access public
	* @static
	* @param string $string 加密字符串
	* @return string
	+ --------------------------------------------------------
	*/
	public static function url($string) {
		return base64_encode(self::tp($string));
	}
	
	/**
	+ --------------------------------------------------------
	* 对密码信息的加密,此方法不可逆(即不可解密)
	+ --------------------------------------------------------
	* @access public
	* @static
	* @param string $string 加密字符串
	* @return string
	+ --------------------------------------------------------
	*/
	public static function password($string) {
		return md5(base64_encode($string));
	}
}