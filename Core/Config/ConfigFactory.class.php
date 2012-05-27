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
 * Toper 读取配置信息的工厂
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Config
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */
class Tp_ConfigFactory extends Tp {

	private static $_instance = array();
	//类的实例
	private $_config = array();
	//配置信息，从xmlConfig,iniConfig中取得

	private function __construct() {}
	private function __clone() {}

	/**
	 + --------------------------------------------
	 * 重载__call()
	 * 假设配置项为
	 * 		array(
	 * 			'a'=>array(
	 * 				'b'=>'b',
	 * 				'c'=>'c'
	 * 			)
	 * 		)
	 * 支持:getA()返回数组
	 * 支持:getA('b')返回字符串
	 + --------------------------------------------
	 * @access public
	 * @param string $method 调用的方法名
	 * @param array $args 调用的参数
	 * @return mixed
	 + --------------------------------------------
	 */
	public function __call($method,$args) {
		$get = substr($method,0,3);
		$config = tp_lcfirst(substr($method,3));
		if(isset($args[0])) {

			if(isset($this->_config[$config][$args[0]])) {
				return $this->_config[$config][$args[0]];
			} else {
				tp_include(TP_PATH.'/Core/Exception/ClassException.class.php');
				throw new Tp_ClassException(Tp_ClassException::NONE_EXISTS_VALUE);
			}
		} else {
			if(isset($this->_config[$config])) {
				return $this->_config[$config];
			} else {
				tp_include(TP_PATH.'/Core/Exception/ClassException.class.php');
				throw new Tp_ClassException(Tp_ClassException::NONE_EXISTS_VALUE);
			}
		}
	}

	/**
	+ --------------------------------------------------
	* 工厂
	* $config = Tp_ConfigFactory::factory(APP_PATH.'test.xml');
	* 或者
	* $config = Tp_CofigFactory::factory(APP_PATH.'test.ini');
	* 支持 $config->get() 获取所有配置项
	* 支持 $config->getA() 得到变量A(和$config->get('A')相同)
	* 支持 $config->getA('B') 得到变量A下面的变量B的值(和$config->get('A=>B')相同)
	* 如果需要得到的配置项不全为string,请参照Tp_IniConfig和Tp_XmlConfig的具体内容
	+ --------------------------------------------------
	* @access public
	* @static
	* @param string $path 请使用绝对路径
	* @return object
	+ --------------------------------------------------
	*/
	public static function factory($path) {
		if(isset(self::$_instance[$path])) {
			return self::$_instance[$path];
		} else {
			self::$_instance[$path] = new Tp_ConfigFactory();
			$suffix = strtolower(substr($path,strrpos($path,'.')+1));
			switch($suffix) {
				case "xml":
					tp_include(TP_PATH.'/Core/Config/XmlConfig.class.php');
					self::$_instance[$path]->_config = Tp_XmlConfig::parse($path);
					break;
				case "ini":
					tp_include(TP_PATH.'/Core/Config/IniConfig.class.php');
					self::$_instance[$path]->_config = Tp_IniConfig::parse($path);
					break;
				default:
					throw new Exception("不支持这种格式的配置文件");
					break;
			}
			return self::$_instance[$path];
		}
	}

	/**
	+ --------------------------------------------------
	* 得到配置值,暂时只支持一维和二维
	* 如果不填写参数，那么得到所有的配置项
	* 支持get('test') get('test=>test2')
	+ --------------------------------------------------
	* @access public
	* @param string $name
	* @return mixed
	+ --------------------------------------------------
	*/
	public function get($name = null) {
		if(null === $name) {
			return $this->_config;
		} else {
			if(false !== stripos($name,'=>')) {
				//二维
				$name = explode('=>',$name);
				return isset($this->_config[$name[0]][$name[1]]) ? $this->_config[$name[0]][$name[1]] : null;
			} else {
				//一维
				return isset($this->_config[$name]) ? $this->_config[$name] : null;
			}
		}
	}
}