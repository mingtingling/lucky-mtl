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
 * Toper基类
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */
class Tp {

	private static $_instance = array();
	//静态加载类

	/**
	 + -------------------------------------------------------------
	 * 设置变量值，魔术方法
	 + -------------------------------------------------------------
	 * @access public
	 + -------------------------------------------------------------
	 * @param string $name 参数名
	 * @param string $value 参数值
	 * @return void
	 + -------------------------------------------------------------
	 */
	public function __set($name,$value) {
		if(property_exists($this,$name)) {
			$this->$name = $value;
		} else {
			tp_include(TP_PATH.'/Core/Exception/ClassException.class.php');
			throw new Tp_ClassException(Tp_ClassException::NONE_EXISTS_VALUE);
		}
	}

	/**
	 + -------------------------------------------------------------
	 * 得到变量值，魔术方法
	 + -------------------------------------------------------------
	 * @access public
	 + -------------------------------------------------------------
	 * @param string $name
	 * @return string $vlaue 某一个属性的值
	 + -------------------------------------------------------------
	 */
	public function __get($name) {
		return isset($this->$name) ? $this->name : null;
	}

	/**
	 + ------------------------------------------------
	 * 没找到方法时调用，魔术方法
	 + ------------------------------------------------
	 * @access public
	 * @param string $name 函数名
	 * @param string $arguments 参数值
	 * @return void
	 + ------------------------------------------------
	 */
	public function __call($name,$arguments) {
		//暂定为没找到public方法即抛出异常
		//还可以通过这个调用私有方法即过载
		tp_include(TP_PATH.'/Core/Exception/ClassException.class.php');
		throw new Tp_ClassException(Tp_ClassException::NONE_EXISTS_METHOD);
	}

	/**
	 + ------------------------------------------------
	 * 没找到静态方法时调用，魔术方法
	 + ------------------------------------------------
	 * @access public
	 * @static
	 * @param string $name 函数名
	 * @param string $arguments 参数值
	 * @return void
	 + ------------------------------------------------
	 */
	public static function __callStatic($name,$arguments) {
		tp_include(TP_PATH.'/Core/Exception/ClassException.class.php');
		throw new Tp_ClassException(Tp_ClassException::NONE_EXISTS_STATIC_METHOD);
	}


	/**
	 + -------------------------------------------------
	 * 静态加载类和方法,静默者模式，可能未来会删除
	 * 借鉴了thinkphp的get_instance_of
	 * 支持Tp::instance('testClass','testMethod')
	 * 等价于$test = new testClass();$test->testMethod()
	 + -------------------------------------------------
	 * @access public
	 * @static
	 * @param string $class
	 * @param string $method
	 * @return object
	 + -------------------------------------------------
	 */
	public static function instance($class,$method = '') {
		if(!empty($method)) {
			//这个是为了区分比如:Test_test(类)和Test(类)的test()方法
			$identify = $class."->".$method;
		} else {
			$identify = $class;
		}
		if(isset(self::$_instance[$identify])) {
			//已经存在，即之前调用过该方法或者该类
			return self::$_instance[$identify];
		} else {
			if(class_exists($class)) {
				$tmpClass = new $class();
				if(!empty($method)) {
					if(method_exists($tmpClass,$method)) {
						self::$_instance[$identify] = call_user_func_array(array(&$tmpClass, $method));
						//call_user_func_array()调用某一个方法
					} else {
						tp_include(TP_PATH.'/Core/Exception/ClassException.class.php');
						throw new Tp_ClassException(Tp_ClassException::NONE_EXISTS_METHOD);
					}
				} else {
					self::$_instance[$identify] = $tmpClass;
				}
				return self::$_instance[$identify];
			} else {
				tp_include(TP_PATH.'/Core/Exception/ClassException.class.php');
				throw new Tp_ClassException(Tp_ClassException::NONE_EXISTS_CLASS);
			}
		}
	}
}