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
 * View的基类
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage View
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */
 tp_include(TP_PATH.'/Core/View/ViewCompile.class.php');
class Tp_View extends Tp {

	protected $_var = array();
	//模板要输出的变量
	protected $_tpl = '';
	//模板名

	public function __construct() {}

	/**
	 + ----------------------------------------------------------
	 * 模板赋值(控制器中使用)，具体使用请见:Tp_Controller
	 * 支持assign('test','A')为变量test赋值A
	 * 支持assign(array('test'=>'A','test2'=>'B')) 为变量test,test2赋值
	 * 为对象赋值正在重构中，暂不提供使用
	 + ----------------------------------------------------------
	 * @access public
	 * @param mixed $name 要赋值的变量,同时也支持关联数组
	 * @param mixed $value 赋值变量的取值
	 * @return void
	 + ----------------------------------------------------------
	 */
	public function assign($name,$value = '') {
		if(is_array($name)) {
			//添加变量元素,array
			foreach($name as $key=>$val) {
				$this->_var[$key] = $val;
			}
		} else if(is_object($name)) {
			//遍历对象成员
			foreach($name as $key=>$val) {
				$this->_var[$key] = $val;
			}
		} else {
			//添加或者覆盖一个元素
			$this->_var[$name] = $value;
		}
	}

	/**
	 + -----------------------------------------------------------
	 * 模板数据的移除
	 * 使用见:Tp_Controller类 _remove()方法
	 + -----------------------------------------------------------
	 * @access public
	 * @param mixed $name
	 * @return void
	 + -----------------------------------------------------------
	 */
	 public function remove($name = null) {
		 if(null === $name) {
			$this->_var = array();
			return ;
		 }
		$removeArrName = array();
		if(is_array($name)) {
			foreach($name as $tmpStr) {
				$removeArrName[] = $tmpStr;
			}
		} else {
			//当做字符串
			$removeArrName[] = $name;
		}
		foreach($removeArrName as $key=>$outerTmpStr) {
			$tmpArr = explode('.',$outerTmpStr);
			$removeArrName[$key] = 'unset($this->_var';
			foreach($tmpArr as $innerTmpStr) {
				$removeArrName[$key] .= ('[\''.$innerTmpStr.'\']');
			}
			$removeArrName[$key] .= ');';
			eval($removeArrName[$key]);
		}
	 }


	/**
	 + -----------------------------------------------------------
	 * 模板显示(控制器中使用)，具体使用请见:Tp_Controller
	 * 支持:display('Test.index');
	 + -----------------------------------------------------------
	 * @access public
	 * @param string $tpl 模板名
	 * @return void
	 + -----------------------------------------------------------
	 */
	public function display($tpl) {
		if(empty($tpl)) return;
		$cacheFile = MODULES_PATH.'/Views/~Compile/'.$tpl.'.php';
		$tplFile = MODULES_PATH.'/Views/'.$tpl.'.'.C('view=>defaultSuffix');
		$needCompile = (false === C('cache=>viewCacheOn')) || !file_exists($cacheFile) || (filemtime($cacheFile) < filemtime($tplFile)); //是否需要编译
		if($needCompile) {
		//模板编译
			ob_start();
			ob_implicit_flush(false);
			include $tplFile;
			$contents = ob_get_clean();
			
			$compile = new Tp_ViewCompile();
			$contents = $compile->compile($contents,$tplFile);
			$contents = $compile->optimize($contents);
			$contents = $compile->updateCache($contents,$tpl);
		}
		//模板输出
		$_tp_base_url_ = C('url=>protocol').'://'.U('baseUrl');
		$_tp_public_url_ = $_tp_base_url_;
		//此处的命名是为了防止用户定义此变量名
		$pos = strripos($_tp_public_url_,'index.php');
		if(false !== $pos) {
			$_tp_public_url_ = substr($_tp_public_url_,0,$pos-1);
		}
		ob_start();
		ob_implicit_flush(false);	
		extract($this->_var);
		include $cacheFile;
		$contents = ob_get_clean();
		$this->_output($contents);
	}

	/**
	 + -------------------------------------------------------------
	 * 模板输出(辅助)
	 + -------------------------------------------------------------
	 * @access protected
	 * @param string $content 要输出的内容
	 * @return void
	 + -------------------------------------------------------------
	 */
	protected function _output($content) {
		echo $content;
	}
}