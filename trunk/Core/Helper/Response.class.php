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
 * Toper 响应的处理
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Helper
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */

class Tp_Response extends Tp {

	/**
	+ ------------------------------------------------------
	* 设置URL参数的含义(即URL的映射)
	* 如url:http://localhost/M/C/A/1/2/3
	* 假如上面的1代表month,2代表day,3代表hour
	* 那么可以使用Tp_Response::setParam('month/day/hour')
	* 也可以使用Tp_Response::setParam(array('month','day','hour'))
	* 此方法已经被封装到Tp_Controller,如果在控制器中使用
	* 支持:$this->_setParam();具体参数和本方法一致
	+ ------------------------------------------------------
	* @access public
	* @static
	* @param string $name
	* @return void
	+--------------------------------------------------------
	*/
	public static function setParam($name) {
		if(is_array($name)) {
			$name = implode('/',$name);
		}
		U('meaningOfExtra',$name);
	}
	
	/**
	 + ------------------------------------------------------
	 * URL重定向
	 * 支持Tp_Response::redirect('http://localhost/test',10,'要显示的信息');
	 * 此方法已经被封装到Tp_Controller,如果在控制器中使用
	 * 支持:$this->_redirect();具体参数同本方法一致，但是$url参数略有不同
	 * 在控制器_redirect()方法已经对$url进行了处理，而本方法未进行任何处理，详情请察看:Tp_FrontController
	 + ------------------------------------------------------
	 * @param string $url 将要重定向的URL
	 * @param int $time 跳转延时的秒数
	 * @param string $msg 显示的信息
	 * @param bool $showMsg 是否显示msg的内容
	 * @return void
	 + ------------------------------------------------------
	 */
	public static function redirect($url,$time = 0,$msg = '',$showMsg = false) {
		$url = str_replace(array('\n','\r'),'',$url);
		if(empty($msg)) {
			$msg = "系统将在{$time}秒后跳转到{$url}";
		}
		if(!headers_sent()) {
			if(0 === $time) {
				header("Location:".$url);
			} else {
				header("refresh:{$time};url={$url}");
				if($showMsg) {
					echo $msg;
				}
			}
			exit();
		} else {
			$str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
			if ($time != 0) {
				$str .= $msg;
			}
		}
		exit();
	}
}