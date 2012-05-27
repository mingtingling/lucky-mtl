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
 * Toper 请求信息的处理
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Helper
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */

class Tp_Request extends Tp {
	
	private static $_isUrlParsed = false;
	//URL信息是否已经被解析
	private static $_urlExtra = array();
	//URL额外的信息，即用户可以自定义的信息
	const COOKIE = 1; //仅搜索cookie
	const SESSION = 2; //仅搜索session
	const GET = 3; //仅搜索get方法的参数
	const POST = 4; //仅搜索post方法的参数
	const EXTEND_GET = 5; //仅搜索扩展的Get
	const ALL = 6; //所有的参数均搜索

	/**
	 + -----------------------------------------------------
	 * 输出的安全HTML
	 + -----------------------------------------------------
	 * @access public
	 * @static
	 * @param string $html
	 * @return string
	 + -----------------------------------------------------
	 */
	public static function safeHtml($html) {
		$pattern = array();
		$replace = array();
		return preg_replace($pattern,$replace,$html);
	}
	/**
	 + ------------------------------------------------------
	 * 封装系统的GET方法,并且可以得到系统的GET方法无法取得的变量值
	 * 这个方法是取得没有经过处理的数据
	 * 如url:http://localhost?m=2
	 * 如url:http://localhost/test/m=2/test3
	 * 注意:此处URL信息需要是字母和数字，所以建议对中文字符加密后传输
	 * 支持Tp_Request::get('m')
	 + ------------------------------------------------------
	 * @access public
	 * @static
	 * @param string $var 要取出的变量
	 * @return mixed
	 + ------------------------------------------------------
	 */
	public static function get($var) {
		if(isset($_GET[$var])) {
			return $_GET[$var];
		}
		$url = $_SERVER['REQUEST_URI'];
		$pattern = "/\/".$var."=([a-zA-Z0-9]*)/";
		//此处GET方法得到的数据值仅仅为数字和26个字母
		if(preg_match($pattern,$url,$value)) {
			return $value[1];
		} else {
			//未找到,后面还可以扩展
			return false;
		}
	}


	/**
	 + ------------------------------------------------------
	 * 此方法专门用来得到不能通过get方法解析的URL传递的信息
	 * 如:http://localhost/toper2/Modules/Controller/Action/p1/p2
	 * 如a1的值为p1(即a1映射到p1,映射方法详见Tp_Response)
	 * 使用Tp_Request::extendGet('a1') 取得p1
	 + ------------------------------------------------------
	 * @access public
	 * @static
	 * @param string $var 要取出的变量
	 * @return mixed
	 + ------------------------------------------------------
	 */
	public static function extendGet($var) {
		if(false === self::$_isUrlParsed) {
			$extra = explode('/',U('extra'));
			$meaningOfExtra = explode('/',U('meaningOfExtra'));
			foreach($meaningOfExtra as $tmpStr) {
				self::$_urlExtra[$tmpStr] = (empty($extra) ? null : (array_shift($extra)));
			}
		}
		if(isset(self::$_urlExtra[$var])) {
			return self::$_urlExtra[$var];
		} else {
			return false;
		}
	}


	/**
	 + ---------------------------------------------------------
	 * 封装系统的POST方法，所有通过POST传递的变量均可以取得
	 * 这个方法取得的值没有经过处理
	 * 支持: Tp_Request::post('test');
	 + ---------------------------------------------------------
	 * @access public
	 * @param string $var 要取出的变量
	 * @return mixed
	 + ---------------------------------------------------------
	 */
	public static function post($var) {
		if(isset($_POST[$var])) {
			 //可以取得表单的输入框，单选，复选，列表项
			return $_POST[$var];
		}
		if(isset($_FILES[$var])) {
			return $_FILES[$var];
		} else {
			return false;
		}
	}

	/**
	 + ----------------------------------------------
	 * 得到主机域名
	 + ----------------------------------------------
	 * @access public
	 * @param string $url
	 * @return string
	 + ----------------------------------------------
	 */
	public static function getDomainName($url) {
		preg_match("/^(http:\/\/)?([^\/]+)/i",$url,$domainName);
		return $domainName;
	}

	/**
	 + -----------------------------------------------
	 * 得到当前文件的绝对路径
	 + -----------------------------------------------
	 * @access public
	 * @param string $url
	 * @return string
	 + -----------------------------------------------
	 */
	public static function getAbsPath() {
		$tmp = "http://".$_SERVER ['HTTP_HOST'].$_SERVER['PHP_SELF'];
		return substr($tmp,0,strrpos($tmp,'/'));
	}
	/**
	+ ------------------------------------------------
	* 取得参数的值
	* 支持获取get,extendGet,post,cookie,session的值
	* 搜索优先级:extendGet>get>post>session>cookie
	* 按照优先级，如果在优先级高的位置(如get)找到，那么不再继续搜索
	* 如:Tp_Request::getParam('test');
	* 如果不想按照优先级，而是寻找特定方式的值，那么使用这个里面的常量来标志您的获取方式
	* 如:Tp_Request::getParam('test',Tp_Request::Cookie) 代表得到cookie中变量为test的值
	* 这个方法已经被封装到Tp_Controller,如果在控制器中使用，那么
	* 支持:$this->_getParam()，具体参数和本函数一致
	+ ------------------------------------------------
	* @access public
	* @static
	* @param string $var
	* @param int $searchType 按照某种搜索类型搜索,默认搜索所有情况
	* @return string
	+--------------------------------------------------
	*/
	public static function getParam($var,$searchType = Tp_Request::ALL) {
		if($searchType === Tp_Request::ALL) {
			$value = self::extendGet($var);
			if(false !== $value) {
				return $value;
			}
			$value = self::get($var);
			if(false !== $value) {
				return $value;
			}
			$value = self::post($var);
			if(false !== $value) {
				return $value;
			}
			tp_include(TP_PATH.'/Core/Helper/Session.class.php');
			$value = Tp_Session::get($var);
			if(false !== $value) {
				return $value;
			}
			tp_include(TP_PATH.'/Core/Helper/Cookie.class.php');
			$value = Tp_Cookie::get($var);
			return $value;
		} else if($searchType === Tp_Request::COOKIE) {
			return Tp_Cookie::get($var);
		} else if($searchType === Tp_Request::SESSION) {
			return Tp_Session::get($var);
		} else if($searchType === Tp_Request::GET) {
			return self::get($var);
		} else if($searchType === Tp_Request::POST) {
			return self::post($var);
		} else if($searchType === Tp_Request::EXTEND_GET) {
			return self::extendGet($var);
		} else {
			tp_include(TP_PATH.'/Core/Exception/CommonException.class.php');
			throw new Tp_CommonException(Tp_CommonException::INCORRECT_VAR_TYPE);
		}
	}
}