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
 * Controller的基类
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */
class Tp_Controller extends Tp{

	protected $_view = null; //视图
	protected $_cache = array();
	
	public function __construct() {
		$this->_init();
	}

	/**
	 + --------------------------------------------------
	 * 控制器初始化(用户透明)
	 + --------------------------------------------------
	 * @access protected
	 * @param void
	 * @return void
	 + --------------------------------------------------
	 */
	protected function _init() {
		$this->_preDispatch();
	}

	/**
	 + --------------------------------------------------
	 * URL跳转,详情请见Tp_Response
	 * 支持 $this->_redirect('/Modules/Controller/Action',true,10)
	 * 支持 $this->_redirect('http://www.qingyueit.com',false,10)
	 + --------------------------------------------------
	 * @access protected
	 + --------------------------------------------------
	 * @param string $url 跳转的路径
	 * @param bool $dealed 是否处理URL,处理后的URL更友好,处理后的URL会自动解析URL
	 * @param int $delay 延迟时间
	 * @param string $msg 显示的信息
	 * @param bool $showMsg 是否显示信息
	 * @return void
	 + --------------------------------------------------
	 */
	protected function _redirect($url,$dealed = true,$delay = 0,$msg = '',$showMsg = false) {
		if(true === $dealed) {
			$url = (C('url=>protocol')."://".U('baseUrl').$url);
		}
		tp_include(TP_PATH.'/Core/Helper/Response.class.php');
		Tp_Response::redirect($url,$delay,$msg,$showMsg);
	}
	/**
	 + ------------------------------------------------------------------
	 * 判断一个请求是否是ajax请求
	 * 支持: $this->_isAjax();
	 + ------------------------------------------------------------------
	 * @access protected
	 * @param void
	 * @return bool
	 + ------------------------------------------------------------------
	 */
	protected function _isAjax() {
	 	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
	 		if('xmlhttprequest' === strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) {
				return true;
	 		}
	 	}
		if(isset($_SESSION['__tp__ajax__'])) {
			$status = false;
			if(true === $_SESSION['__tp__ajax__']) {
				$status = true;
			}
			unset($_SESSION['__tp__ajax__']);
			return $status;
		}
		return false;
	}

	/**
	 + --------------------------------------------------------------------
	 * 设置为Ajax请求(如果使用了jquery,那么不需要使用这个)
	 * 支持:$this->_setAjax()
	 + --------------------------------------------------------------------
	 * @access protected
	 * @return void
	 + --------------------------------------------------------------------
	 */
	protected function _setAjax() {
		$_SESSION['__tp__ajax__'] = true;
	}

	/**
	 + --------------------------------------------------------------------
	 * 显示模版
	 * 注意:$tpl无文件后缀名，还有路径之间通过.分隔，而不是/
	 * 支持:$this->_display('Test.test')，详情请见Tp_View和Tp_SmartyView
	 + --------------------------------------------------------------------
	 * @access protected
	 * @param string $tpl 模板的名称
	 * @return void
	 + --------------------------------------------------------------------
	 */
	protected function _display($tpl) {
		if(null === $this->_view) {
			tp_include(TP_PATH.'/Core/View/ViewFactory.class.php');
			$this->_view = Tp_ViewFactory::factory();
			if(('tp' === C('view=>type')) && isset($this->_cache) && $this->_cache) {
				//只有tp的模板才能使用
				$this->_view->assign($this->_cache);
				unset($this->_cache);
			}
		}
		$tpl = str_replace('.','/',$tpl);
		$this->_view->display($tpl);
	}

	/**
	 + ---------------------------------------------------------------------
	 * 为模版赋值,详情请见:Tp_View和Tp_SmartyView
	 * 如果需要查询Toper支持的标签，请见:Tp_TagLib
	 * 支持:$this->_assign(array('test'=>'test','testArr'=>array('test')));
	 * 支持:$this->_assign('test','testVal');
	 + ---------------------------------------------------------------------
	 * @access protected
	 * @param mixed $name 要赋值的变量
	 * @param mixed $value 要赋值的值
	 * @return void
	 + ---------------------------------------------------------------------
	 */
	protected function _assign($name,$value = '') {
		if(null === $this->_view) {
			tp_include(TP_PATH.'/Core/View/ViewFactory.class.php');
			$this->_view = Tp_ViewFactory::factory();
			if('tp' === C('view=>type')) {
				//只有tp的模板才能使用
				$this->_view->assign($this->_cache);
				unset($this->_cache);
			}
		} 
		$this->_view->assign($name,$value);
	}

	/**
	 + ---------------------------------------------------------------------
	 * 将模板的数据移除(只支持Toper的视图)
	 * 支持:$this->_remove('test'); 删除单个变量
	 * 支持:$this->_remove('test.test'); 删除多维数组中的某个变量
	 * 支持:$this->_remove(array('test','test2')); 删除多个变量
	 * 支持:$this->_remove() 删除所有数据
	 + ---------------------------------------------------------------------
	 * @access protected
	 * @param mixed $name 要移除的数据变量名
	 * @return bool
	 + ---------------------------------------------------------------------
	 */
	protected function _remove($name = null) {
		if(null === $this->_view) {
			tp_include(TP_PATH.'/Core/View/ViewFactory.class.php');
			$this->_view = Tp_ViewFactory::factory();
			if('tp' === C('view=>type')) {
				//只有tp的模板才能使用
				$this->_view->assign($this->_cache);
				unset($this->_cache);
			}
		} 
		$this->_view->remove($name);
	}

	/**
	 + ---------------------------------------------------------------------
	 * 预载入数据
	 * 此方法必须在_preDispatch()中调用
	 * 使用方法同$this->_assign()基本相同，只是这个函数会将数据缓存
	 * 直到调用$this->_assign()才会同这个方法将数据一起写入
	 + ---------------------------------------------------------------------
	 * @access protected
	 * @param mixed $name
	 * @param mixed $value
	 * @return bool
	 + ---------------------------------------------------------------------
	 */
	protected function _preLoad($name,$value = '') {
		if(is_array($name)) {
			foreach($name as $key => $val) {
				$this->_cache[$key] = $val;
			}
		} else {
			$this->_cache[$name] = $value;
		}
	}
	

	/**
	 + --------------------------------------------------
	 * 在载入控制器之前需要载入的视图数据
	 * $this->_preLoad()方法必须写在这个函数里面
	 + --------------------------------------------------
	 * @access protected
	 * @param void
	 * @return void
	 + --------------------------------------------------
	 */
	protected function _preDispatch() {
	}

	/**
	 + --------------------------------------------------
	 * 得到变量值,支持得到get,post,cookie,session中的值
	 * 支持:$this->_getParam('testGet');
	 * 详情请见:Tp_Request
	 + --------------------------------------------------
	 * @access protected
	 * @param string $var
	 * @return mixed
	 + --------------------------------------------------
	 */
	protected function _getParam($var) {
		tp_include(TP_PATH.'/Core/Helper/Request.class.php');
		return Tp_Request::getParam($var);
	}
	
	/**
	 + --------------------------------------------------
	 * 设置变量值,实际上是为URL上面的数据写入意义
	 * 如:www.toper.com/TestModule/TestController/test/2
	 * $this->_setParam(array('month'));
	 * 调用这个函数之后您就可以通过$this->_getParam('month')来取得2了
	 * 详情请见:Tp_Response
	 + --------------------------------------------------
	 * @access protected
	 * @param mixed $var
	 * @return void
	 + --------------------------------------------------
	 */
	protected function _setParam($var) {
		tp_include(TP_PATH.'/Core/Helper/Response.class.php');
		return Tp_Response::setParam($var);
	}
}