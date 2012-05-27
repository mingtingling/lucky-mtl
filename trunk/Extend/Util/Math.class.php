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
 * Toper 数学函数的封装
 +--------------------------------------------------
 * @category Toper
 * @package Extend
 * @subpackage Util
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */
class Tp_Math extends Tp {
	const PI = M_PI;
	const E = M_E;

	/**
	+--------------------------------------------------------
	* 产生随机数
	* 支持Tp_Math::random() 它将产生0-1的随机数(带小数)
	* 支持Tp_Math::random(1,2) 它将产生1-2的随机数(带小数)
	* 支持Tp_Math::random(1,20,true) 它将产生1-20的随机整数
	+--------------------------------------------------------
	* @access public
	* @static
	* @param int $min 最小值(包括它)
	* @param int $max 最大值(包括它)
	* @param bool $isInt 是否产生整型的随机数
	* @return mixed
	+--------------------------------------------------------
	*/
	public static function random($min = null,$max = null,$isInt = false) {
		if((null == $max) && (null === $min)) {
			return (false === $isInt) ? floatval(mt_rand(0,1000) / 1000.0) : 0;
		}
		$min = (null === $min) ? 0: $min;
		$max = (null === $max) ? mt_getrandmax() : $max;
		return (false === $isInt) ? floatval($min + floatval(mt_rand(0,1000) / 1000.0) * ($max - $min)) : mt_rand($min,$max);
	}

	/**
	+--------------------------------------------------------
	* 得到一个数的绝对值
	+--------------------------------------------------------
	* @access public
	* @static
	* @param mixed $val
	* @return mixed
	+--------------------------------------------------------
	*/
	public static function abs($val) {
		return abs($val);
	}

	/**
	+--------------------------------------------------------
	* 得到一个数的反余弦
	+--------------------------------------------------------
	* @access public
	* @static
	* @param float $val(弧度)
	* @return float
	+--------------------------------------------------------
	*/
	public static function acos($val) {
		return acos($val);
	}

	/**
	+--------------------------------------------------------
	* 得到一个数的余弦
	+--------------------------------------------------------
	* @access public
	* @static
	* @param float $val
	* @return float
	+--------------------------------------------------------
	*/
	public static function cos($val) {
		return cos($val);
	}

	/**
	+--------------------------------------------------------
	* 得到一个数的反正弦
	+--------------------------------------------------------
	* @access public
	* @static
	* @param float $val(弧度)
	* @return float
	+--------------------------------------------------------
	*/
	public static function asin($val) {
		return asin($val);
	}

	/**
	+--------------------------------------------------------
	* 得到一个数的正弦
	+--------------------------------------------------------
	* @access public
	* @static
	* @param float $val
	* @return float
	+--------------------------------------------------------
	*/
	public static function sin($val) {
		return sin($val);
	}

	/**
	+--------------------------------------------------------
	* 得到两个数的反正切
	+--------------------------------------------------------
	* @access public
	* @static
	* @param float $y
	* @param float $x
	* @return float(弧度)
	+--------------------------------------------------------
	*/
	public static function atan2($y,$x) {
		return atan2($y,$x);
	}

	/**
	+--------------------------------------------------------
	* 反正切
	+--------------------------------------------------------
	* @access public
	* @static
	* @param float $val(弧度)
	* @return float
	+--------------------------------------------------------
	*/
	public static function atan($val) {
		return atan($val);
	}

	/**
	+--------------------------------------------------------
	* 正切
	+--------------------------------------------------------
	* @access public
	* @static
	* @param float $val
	* @return float
	+--------------------------------------------------------
	*/
	public static function tan($val) {
		return tan($val);
	}

	/**
	+--------------------------------------------------------
	* 指数
	+--------------------------------------------------------
	* @access public
	* @static
	* @param number $base
	* @param number $exp
	* @return mixed
	+--------------------------------------------------------
	*/
	public static function pow($base,$exp) {
		return pow($base,$exp);
	}

	/**
	+--------------------------------------------------------
	* 平方根
	+--------------------------------------------------------
	* @access public
	* @static
	* @param float $val
	* @return float
	+--------------------------------------------------------
	*/
	public static function sqrt($val) {
		return sqrt($val);
	}

	/**
	+--------------------------------------------------------
	* 指数
	+--------------------------------------------------------
	* @access public
	* @static
	* @param float $val
	* @return float
	+--------------------------------------------------------
	*/
	public static function exp($val) {
		return exp($val);
	}

	/**
	+--------------------------------------------------------
	* 上取整
	+--------------------------------------------------------
	* @access public
	* @static
	* @param float $val
	* @return float
	+--------------------------------------------------------
	*/
	public static function ceil($val) {
		return ceil($val);
	}

	/**
	+--------------------------------------------------------
	* 下取整
	+--------------------------------------------------------
	* @access public
	* @static
	* @param float $val
	* @return float
	+--------------------------------------------------------
	*/
	public static function floor($val) {
		return floor($val);
	}

	/**
	+--------------------------------------------------------
	* 四舍五入
	+--------------------------------------------------------
	* @access public
	* @static
	* @param float $val
	* @param int precision 精度
	* @return float
	+--------------------------------------------------------
	*/
	public static function round($val,$precision = 0) {
		return round($val,$precision);
	}

	/**
	+--------------------------------------------------------
	* 对数
	+--------------------------------------------------------
	* @access public
	* @static
	* @param float $val
	* @param float $base 基数，默认为E
	* @return float
	+--------------------------------------------------------
	*/
	public static function log($val,$base = Tp_Math::E) {
		return log($val,$base);
	}

	/**
	+--------------------------------------------------------
	* 角度转弧度
	+--------------------------------------------------------
	* @access public
	* @static
	* @param float $val
	* @return float
	+--------------------------------------------------------
	*/
	public static function deg2rad($val) {
		return deg2rad($val);
	}

	/**
	+--------------------------------------------------------
	* 弧度转角度
	+--------------------------------------------------------
	* @access public
	* @static
	* @param float $val
	* @return float
	+--------------------------------------------------------
	*/
	public static function rad2deg($val) {
		return rad2deg($val);
	}

	/**
	+--------------------------------------------------------
	* 最小值
	+--------------------------------------------------------
	* @access public
	* @static
	* @param float $val 参数个数可变，数量从1个到N个
	* @return float
	+--------------------------------------------------------
	*/
	public static function min() {
		$args = func_get_args();

	}
}