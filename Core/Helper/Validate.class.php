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
 * Toper 验证信息
 * 本类大量借鉴或者使用的initphp的validate.init.php
 * initphp 网址:www.initphp.com
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Helper
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */

class Tp_Validate extends Tp {

	/**
	 + ---------------------------------------------------------
	 * 验证是否是有效邮箱
	 + ---------------------------------------------------------
	 * @access public
	 * @param string $mail 邮箱
	 * @return bool
	 + ---------------------------------------------------------
	 */
	public static function isMail($mail) {
		return (0 === preg_match('/^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z]{2,4}$/',$mail)) ? false : true;
	}

	/**
	 + ---------------------------------------------------------
	 * 验证是否是数字串
	 + ---------------------------------------------------------
	 * @access public
	 * @param string $val
	 * @return bool
	 + ---------------------------------------------------------
	 */
	public static function isNumber($val) {
		return (0 === preg_match('/^[0-9]+$/',trim($val))) ? false : true;
	}
	
	/**
	 + ---------------------------------------------------------
	 * 验证是否是IP,此函数版权属于initphp
	 + ---------------------------------------------------------
	 * @access public
	 * @param string $val
	 * @return bool
	 + ---------------------------------------------------------
	 */
	public static function isIp($val) {
		return (0 === preg_match('/^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/', trim($val))) ? false : true;
	}
	
	/**
	 + ---------------------------------------------------------
	 * 验证是否是QQ号码
	 + ---------------------------------------------------------
	 * @access public
	 * @param string $val
	 * @return bool
	 + ---------------------------------------------------------
	 */
	 public static function isQQ($val) {
	 	return (0 === preg_match('/^[1-9][0-9]{4,12}$/',trim($val))) ? false : true;
	 }

	/**
	 + ---------------------------------------------------------
	 * 验证是否是英语单词(即是否是二十六个英文字母)
	 + ---------------------------------------------------------
	 * @access public
	 * @param string $val
	 * @return bool
	 + ---------------------------------------------------------
	 */
	public static function isEnglishWord($val) {
		return (0 === preg_match('/^[a-zA-Z]+$/',trim($val))) ? false : true;
	}
	 
	/**
	 + ---------------------------------------------------------
	 * 验证是否是汉语单词,此函数版权属于initphp
	 + ---------------------------------------------------------
	 * @access public
	 * @param string $val
	 * @return bool
	 + ---------------------------------------------------------
	 */
	 public static function isChinese($val) {
		return (0 === preg_match("/^([\xE4-\xE9][\x80-\xBF][\x80-\xBF])+$/", trim($val))) ? false : true;	
	 }
	 
	/**
	 + ---------------------------------------------------------
	 * 验证是否是安全的密码,此函数版权属于initphp
	 + ---------------------------------------------------------
	 * @access public
	 * @param string $str
	 * @return bool
	 + ---------------------------------------------------------
	 */
	public static function isSafePassword($str) {
		if (preg_match('/[\x80-\xff]./', $str) || preg_match('/\'|"|\"/', $str) || strlen($str) < 6 || strlen($str) > 20 ) {
			return false;
		}
		return true;
	}
	
	/**
	 + ---------------------------------------------------------
	 * 验证是否是安全的帐号,此函数版权属于initphp
	 + ---------------------------------------------------------
	 * @access public
	 * @param string $val
	 * @return bool
	 + ---------------------------------------------------------
	 */
	public function isSafeAccount($val) {
		return (0 === preg_match ("/^[a-zA-Z]{1}[a-zA-Z0-9_\.]{3,31}$/", $val)) ? false : true;
	}
	
	/**
	 + ---------------------------------------------------------
	 * 验证格式是否是身份证，不能确认是否是合法的身份证,此函数版权属于initphp
	 + ---------------------------------------------------------
	 * @access public
	 * @param string $val
	 * @return bool
	 + ---------------------------------------------------------
	 */
	public function isSfzNum($val){
		return (0 === preg_match("/^(\d{15}|\d{17}[\dx])$/i", $val)) ? false : true;
	}
	
	/*
	 + ---------------------------------------------------------
	 * 检查身份证号码,可以确认是否是合法的身份证号码
	 * 此函数为我在互联网上找到，经过我修改而成，原作者未知
	 + ---------------------------------------------------------
	 * @access public
	 * @param string $sfzNum 15位或18位身份证号码
	 * @param mixed $len 长度，可选，默认为both，如果不为both,那么15位号码无效
	 * @return mixed 如果失败，返回false,成功，则返回一个18位的身份证号码
	 + ---------------------------------------------------------
	 */
	public static function checkSfzNum ($sfzNum,$len = 'both') {
		if((15 === strlen($sfzNum)) && ('both' === $len)) {
			$trueNum = substr($sfzNum,0,6).'19'.substr($sfzNum,6);
			//为返回18位号码作准备。
			$preg = "/^[\d]{8}((0[1-9])|(1[0-2]))((0[1-9])|([12][\d])|(3[01]))[\d]{3}$/";
		} elseif(18 === strlen($sfzNum)) {
			$trueNum = substr($sfzNum,0,17);
			$preg = "/^[\d]{6}((19[\d]{2})|(200[0-8]))((0[1-9])|(1[0-2]))((0[1-9])|([12][\d])|(3[01]))[\d]{3}[0-9xX]$/";
		} else {
			return false;
		}
		if(!preg_match($preg,$sfzNum)) {
			return false;
		}
		//完成正则表达式检测
		//以下计算第18位验证码
		$nsum = substr($trueNum, 0,1) * 7;
		$nsum = $nsum + substr($trueNum, 1,1) * 9;
		$nsum = $nsum + substr($trueNum, 2,1) * 10;
		$nsum = $nsum + substr($trueNum, 3,1) * 5;
		$nsum = $nsum + substr($trueNum, 4,1) * 8;
		$nsum = $nsum + substr($trueNum, 5,1) * 4;
		$nsum = $nsum + substr($trueNum, 6,1) * 2;
		$nsum = $nsum + substr($trueNum, 7,1) * 1;
		$nsum = $nsum + substr($trueNum, 8,1) * 6;
		$nsum = $nsum + substr($trueNum, 9,1) * 3;
		$nsum = $nsum + substr($trueNum,10,1) * 7;
		$nsum = $nsum + substr($trueNum,11,1) * 9;
		$nsum = $nsum + substr($trueNum,12,1) * 10;
		$nsum = $nsum + substr($trueNum,13,1) * 5;
		$nsum = $nsum + substr($trueNum,14,1) * 8;
		$nsum = $nsum + substr($trueNum,15,1) * 4;
		$nsum = $nsum + substr($trueNum,16,1) * 2;
		$yzm = 12 - $nsum % 11;
		if(10 === $yzm) {
			$yzm = 'x';
		} elseif(12 === $yzm) {
			$yzm = '1';
		} elseif(11 === $yzm) {
			$yzm = '0';
		}
 		//18位验证码计算完成
		if(18 === strlen($sfzNum)){
			if($yzm !== substr($sfzNum,17,1))
				return false;
		}
		return $trueNum.$yzm;
	}
	
	/*
	 + ---------------------------------------------------------
	 * 检查是否是一个日期(核对格式)
	 + ---------------------------------------------------------
	 * @access public
	 * @param string $ymd 时间字符串
	 * @param string $sep 时间分隔符，默认为-
	 * @return bool
	 + ---------------------------------------------------------
	 */
    public static function isDate($ymd,$sep = '-'){ 
		$parts = explode($sep,$ymd);
		$num = count($parts);
		if(3 === $num){
			$year = (int)$parts[0]; 
			$month = (int)$parts[1]; 
			$day = (int)$parts[2];
		} else if(2 === $num){
			$year = (int)$parts[0]; 
			$month = (int)$parts[1]; 
			$day = 1;
		} else {
			return false;
		}
		if(true === checkdate($month,$day,$year)) {
			return true;
		} else {
			return false;
		} 
	}
	
	/*
	 + ---------------------------------------------------------
	 * 检查是否是电话,此函数版权属于initphp
	 + ---------------------------------------------------------
	 * @access public
	 * @param string $val
	 * @return bool
	 + ---------------------------------------------------------
	 */
	public static function isMobile($val) {
		return (0 === preg_match('/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/', trim($val))) ? false : true;
	}
	
	/*
	 + ---------------------------------------------------------
	 * 检查是否是一个移动电话,此函数版权属于initphp
	 + ---------------------------------------------------------
	 * @access public
	 * @param string $val
	 * @return bool
	 + ---------------------------------------------------------
	 */
	public static function isPhone($val) {
		return (0 === preg_match('/^((\(\d{2,3}\))|(\d{3}\-))?(13|15)\d{9}$/', trim($val))) ? false : true;
	}

	/*
	 + ---------------------------------------------------------
	 * 检查是否是一个合法的URL,此函数版权属于initphp
	 + ---------------------------------------------------------
	 * @access public
	 * @param string $val
	 * @return bool
	 + ---------------------------------------------------------
	 */
	public static function isUrl($val) {
		return (0 === preg_match('/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/', trim($val))) ? false : true;
	}

	/*
	 + ---------------------------------------------------------
	 * 检查是否是邮政编码,此函数版权属于initphp
	 + ---------------------------------------------------------
	 * @access public
	 * @param string $val
	 * @return bool
	 + ---------------------------------------------------------
	 */
	public static function isZip($val) {
		return (0 === preg_match('/^[1-9]\d{5}$/', trim($val))) ? false : true;
	}
}