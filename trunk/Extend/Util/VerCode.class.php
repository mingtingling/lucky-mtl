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
 * Toper 验证码
 +--------------------------------------------------
 * @category Toper
 * @package Extend
 * @subpackage Util
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */

class Tp_VerCode extends Tp {

	private $_randomFactor = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	//随机因子
	private $_randomCode = ''; //验证码
	private $_codeLen = 4; //长度
	private $_width = 80; // 验证码区域宽度
	private $_height = 50; //验证码区域高度
	private $_image = null; //图片句柄
	private $_fontColor; //字体颜色
	private $_fontSize = 20; //字体大小
	private $_ttf = null; //字体文件
	
	public function __construct() {
		if(!function_exists('imagecreatetruecolor')) {
			throw new Exception('sorry,did not support GD,if you want to use this class,please change the php.ini');
		}
		$this->_ttf = TP_PATH.'/Useful/FreeMono.ttf';
	}
	
	/**
	+ ------------------------------------------------------
	* 验证码配置项初始化
	+ ------------------------------------------------------
	* @access public
	* @param string $ttf 字体文件路径
	* @param int $codeLen 验证码长度
	* @param int $width 图片长度
	* @param int $height 图片高度
	* @param int $fontSize 字体大小
	* @param string randomFactor 随机因子
	* @return void
	+ ------------------------------------------------------
	*/
	public function init($ttf = null,$codeLen = 4,$width = 80,$height = 50,$fontSize = 20,$randomFactor = null) {
		$this->_ttf = (null === $ttf) ? $this->_ttf : $ttf;
		$this->_randomFactor = (null === $randomFactor) ? $this->_randomFactor : $randomFactor;
		$this->_codeLen = $codeLen;
		$this->_width = $width;
		$this->_height = $height;
		$this->_fontSize = $fontSize;
	}

	/**
	+ ------------------------------------------------------
	* 生成验证码
	+ ------------------------------------------------------
	* @access private
	* @param void
	* @return void
	+ ------------------------------------------------------
	*/
	private function _createRandomCode() {
		$len = (strlen($this->_randomFactor)-1);
		for($count = 0; $count < $this->_codeLen; $count ++) {
			$this->_randomCode .= $this->_randomFactor[mt_rand(0,$len)];
		}
	}
	
	/**
	+ ------------------------------------------------------
	* 生成背景
	+ ------------------------------------------------------
	* @access private
	* @param void
	* @return void
	+ ------------------------------------------------------
	*/
	private function _createBg() {
		$this->_image = imagecreatetruecolor($this->_width,$this->_height);
		$tmpColor = imagecolorallocate($this->_image,mt_rand(170,255),mt_rand(170,255),mt_rand(170,255));
		imagefilledrectangle($this->_image,0,0,$this->_width,$this->_height,$tmpColor);
	}
	
	/**
	+ ------------------------------------------------------
	* 生成文字
	+ ------------------------------------------------------
	* @access private
	* @param void
	* @return void
	+ ------------------------------------------------------
	*/
	private function _createText() {
		$x = $this->_width / $this->_codeLen;
		for($count = 0; $count < $this->_codeLen; $count ++) {
			$this->_fontColor = imagecolorallocate($this->_image,mt_rand(0,149),mt_rand(0,149),mt_rand(0,149));
			imagettftext($this->_image,$this->_fontSize,mt_rand(-60,60),($x * $count+mt_rand(1,5)),$this->_height / 1.4,$this->_fontColor,$this->_ttf,$this->_randomCode[$count]);
		}
	}
	
	/**
	+ ------------------------------------------------------
	* 生成干扰信息
	+ ------------------------------------------------------
	* @access private
	* @param void
	* @return void
	+ ------------------------------------------------------
	*/
	private function _createDisturb() {
		for($count = 0; $count < 6; $count ++) {
			$tmpColor = imagecolorallocate($this->_image,mt_rand(0,149),mt_rand(0,149),mt_rand(0,149));
			imageline($this->_image,mt_rand(0,$this->_width),mt_rand(0,$this->_height),mt_rand(0,$this->_width),mt_rand(0,$this->_height),$tmpColor);
		}
		for($count = 0; $count < 100; $count ++) {
			$tmpColor = imagecolorallocate($this->_image,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
			imagestring($this->_image,mt_rand(1,5),mt_rand(0,$this->_width),mt_rand(0,$this->_height),'*',$tmpColor);
		}
	}
	
	/**
	+ ------------------------------------------------------
	* 输出验证码
	+ ------------------------------------------------------
	* @access private
	* @param void
	* @return void
	+ ------------------------------------------------------
	*/
	private function _output() {
		header('Content-Type:image/png');
		imagepng($this->_image);
		imagedestory($this->_image);
	}

	/**
	+ ------------------------------------------------------
	* 创建验证码
	+ ------------------------------------------------------
	* @access public
	* @param void
	* @return void
	+ ------------------------------------------------------
	*/
	public function create() {
		$this->_createRandomCode();
		$this->_createBg();
		$this->_createText();
		$this->_createDisturb();
		$this->_output();
	}

	/**
	+ ------------------------------------------------------
	* 得到验证码
	+ ------------------------------------------------------
	* @access public
	* @param void
	* @return string
	+ ------------------------------------------------------
	*/
	public function getCode() {
		return $this->_randomCode;
	}
}