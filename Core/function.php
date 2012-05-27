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
 * Toper 公共函数库
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */

if(!defined('TP_PATH')) exit('您没有定义TP_PATH');
if(!defined('MODULES_PATH')) exit('您没有定义MODULES_PATH');

/**
+ -------------------------------------------------
* 导入类文件，同Java
* 注意:包路径之间通过.分隔，已经定义Tp代表框架,My代表UserApps下面的Modules
* 支持:import('Tp.Core.FrontController')
* 支持import('My.Test.IndexController')
* 如果一个文件中存在.,那么用#替换
* 如:import('Tp.Core.Front#Controller')
* 代表导入Core包下面的Front.Controller类
* 如果想自定义包路径，请首先定义一个常量
* 比如您想使用import('Test.Path');那么定义define('Test','您自己定义的路径')
* 那么次import导入的文件就是:您自己定义的路径下面的Path.class.php
+ -------------------------------------------------
* @param string $class 类库名
* @param string $ext 导入的文件扩展名,Tp框架类不受这个参数影响
* @return bool 是否导入成功
+ -------------------------------------------------
*/

function import($class,$ext = '.class.php') {
	$pos = strpos($class,'.');
	$name = str_replace(array('.','#'),array('/','.'),$class);
	$whosePackage = substr($class,0,$pos);
	$name = substr($name,$pos);
	if('Tp' === $whosePackage) {
		//导入Toper框架类
		$baseUrl = TP_PATH;
	} elseif('My' === $whosePackage) {
		//导入用户类
		$baseUrl = MODULES_PATH;
	} else {
		$baseUrlConstants = strtoupper($whosePackage).'_IMPORT_BASEURL';
		if(defined($baseUrlConstants)) {
			eval('$baseUrl = '.$baseUrlConstants.';');
		} else {
			echo "<br/><font color = 'red'>[ERROR]您没有定义常量".$baseUrlConstants."</font>";
			return false;
		}
	}
	$classFile = $baseUrl.$name.$ext;
	if(is_file($classFile)) {
		return tp_include($classFile);
	} else {
		//不正常的文件
		echo "<br/><font color = 'red'>[ERROR]导入文件".$classFile."失败</font>";
		return false;
	}
}


/**
 + --------------------------------------------------
 * 自定义导入函数,调用此函数时请一定要使用绝对路径
 * 支持tp_include(dirname(__FILE__).'/test.class.php')
 * 本框架本来有两种方法来定义tp_include()
 * 方法1:用class_exists()来判定
 * 方法2:定义方法类的static变量来存储导入的类文件的路径
 * 方法1我决定弃用，因为在import中允许自定义，那么使用class_exists()就有局限性了
 + --------------------------------------------------
 * @param string $url 文件路径
 * @return mixed
 + --------------------------------------------------
 */
function tp_include($url) {
	static $_config = array();
	if(!isset($_config[$url])) {
		$_config[$url] = '';
		return require $url;
	} else {
		return false;
	}
}


/**
 + --------------------------------------------------
 * 判断是否是windows操作系统
 + --------------------------------------------------
 * @param void
 * @return bool 是否是Windows
 + --------------------------------------------------
 */
function is_win() {
	return '\\' === DIRECTORY_SEPARATOR;
}

/**
 + --------------------------------------------------
 * 判断是否是linux操作系统
 + --------------------------------------------------
 * @param void
 * @return bool 是否是Linux
 + --------------------------------------------------
 */
function is_linux() {
	return ':' === PATH_SEPARATOR;
}

/**
 + --------------------------------------------------------
 * 自动载入类,对用户透明
 * 支持导入所有系统类和Models,Controllers,Helper下面的类
 * 如果您定义的类不在这几个目录中，有两种方法解决这个问题
 * 方法1:在配置文件中将autoloadRegister修改为true,然后在入口文件处定义$frontController->init(array('autoload' => array('MyAutoloadClass' => 'MyAutoloadClassMethod'))) 自定义方法来实现导入
 * 方法2:使用import()
 * 在对效率要求不高时，推荐使用方法1;如果对效率要求较高，建议注释掉__autoload()，然后所有类全部使用import()导入
 + --------------------------------------------------------
 * @param string $className 类名
 * @return bool
 + --------------------------------------------------------
 */
 /*
function __autoload($className) {
	static $_config = array();
	$name = str_replace('_','/',$className);
	$name.= '.class.php';
	if('Tp' === (substr($className,0,2))) {
		//Toper系统类
		if(!file_exists(TP_PATH.'/~Cache/~autoloadCache.php')) {
			//没有缓存，那么遍历本框架的库文件
			autoload_cache();
		}
		if(empty($_config)) {
			$_config = require (TP_PATH.'/~Cache/~autoloadCache.php');
		}
		if($_config[$className]) {
			tp_include(TP_PATH.'/'.$_config[$className]);
		} else {
			throw new Exception('在~autoloadCache.php中没有找到对应项,请删除缓存文件后重试');
		}
	} else {
		//用户类
		$name = implode('/',explode('_',$name));
		if('Model' === (substr($className,-5))){
			tp_include(MODULES_PATH.'/Models/'.$name);
		} elseif ('Controller' === (substr($className,-10))) {
			tp_include(MODULES_PATH.'/Controllers/'.$name);
		} elseif ('Helper' === (substr($className,-6))) {
			//在Helpers下面搜索
			tp_include(MODULES_PATH.'/Helpers/'.$name);
		} elseif (is_file(MODULES_PATH.'/Models/'.$name)){
			//在Model下面搜索
			tp_include(MODULES_PATH.'/Models/'.$name);
		} else {
			//可扩展
			return false;
		}
	}
	if(class_exists($className)) {
		return true;
	} else {
		echo "<br/><font color = 'red'>[ERROR]类".$className."不存在或未找到</font>";
		return false;
	}
}
*/

/**
 + --------------------------------------------------------------------
 * 编写自动包含文件的缓存，加快autoload的速度,该函数对用户透明
 * 采用非递归遍历文件夹,广度优先搜索
 * 这个函数在整个框架中只执行一次
 + --------------------------------------------------------------------
 * @param void
 * @return void
 + --------------------------------------------------------------------
 */
function autoload_cache() {
	//思路:
	//建立一个队列，将TP_PATH入队列，也就是初始路径
	//然后遍历该文件夹，如果是文件夹，那么入队列，然后继续遍历
	//遍历完成之后出队列，继续遍历,直到队列为空
	$queue = array();
	array_push($queue,TP_PATH);
	$files = array();
	while(count($queue) > 0) {
		//即队列不为空
		$tmpDir = array_shift($queue);
		$handle = opendir($tmpDir);
		while(false !== ($file = readdir($handle))) {
			if(('.' !== $file) && ('..' !== $file)) {
				if(is_dir($tmpDir.'/'.$file)) {
					if('.' !== substr($file,0,1)) {
					//消除svn隐藏文件夹.svn的影响
						array_push($queue,$tmpDir.'/'.$file);
					}
				} else {
					$pos = strrpos($file,".class.php");
					if(false !== $pos) {
						//是类文件，则可以保存
						if('Tp.class.php' === $file) {
							$files["Tp"] = substr($tmpDir,strlen(TP_PATH)+1)."/".$file;
						} else {
							$files["Tp_".substr($file,0,$pos)] = substr($tmpDir,strlen(TP_PATH)+1)."/".$file;
						}

					}
				}
			}
		}
		closedir($handle);
	}
	ob_start();
	ob_implicit_flush(false);
	echo "<?php return array(";
	$len = count($files);
	$count = 0;
	$tmpKey = "";
	$tmpVal = "";
	foreach($files as $key => $val) {
		if($count >= $len -1) {
			$tmpKey = $key;
			$tmpVal = $val;
			break;
		} else {
			echo "'".$key."'=>'".$val."',";
			$count ++;
		}
	}
	echo "'".$tmpKey."'=>'".$tmpVal."'";
	echo "); ?>";
	$contents = ob_get_clean();
	file_put_contents(TP_PATH.'/~Cache/~autoloadCache.php',$contents);
}


/**
 + --------------------------------------------------------------------
 * slashes的处理
 + --------------------------------------------------------------------
 * @param string $str 待处理字符串
 * @param bool $isAddSlashed 是否是增加slashes
 * @return string 处理后的字符串
 + --------------------------------------------------------------------
 */
function tp_slashes($str,$isAddSlashes = true) {
	if($isAddSlashes) {
		if(get_magic_quotes_gpc()){
			return addslashes($str);
		} else {
			return $str;
		}
	} else {
		return stripslashes($str);
	}
}


/**
 + --------------------------------------------------------------------
 * 格式化输出
 * 感谢吕广奕的Debug类给我灵感
 + --------------------------------------------------------------------
 * @param mixed $var 变量名
 * @param int $type 输出的类型
 * @return void
 + --------------------------------------------------------------------
 */
function tp_echo($var,$type = 1) {
	echo '<pre>';
	if(1 === $type) {
		print_r($var);
	} else if(2 === $type){
		var_dump($var);
	}else if(3 === $type) {
		//原生态的echo
		echo $var;
	} else {
		throw new Exception("未知输出的格式");
	}
	echo '</pre>';
}


/**
 + ---------------------------------------------------------------------
 * 得到某一个变量的类型
 + ---------------------------------------------------------------------
 * @param string $var
 * @return string
 + ---------------------------------------------------------------------
 */
function tp_get_type($var) {
	if(is_int($var)) {
		return "int";
	} else if(is_float($var)) {
		return "float";
	} else if(is_string($var)) {
		return "string";
	} else if(is_array($var)) {
		return "array";
	} else if(is_object($var)) {
		return "object";
	} else if(is_bool($var)) {
		return "bool";
	} else {
		throw new Exception("未知类型的变量");
	}
}

/**
 + ---------------------------------------------------------------------
 * 兼容的lcfirst,将一个字符串的第一个字母变成小写(lowercase first)
 * 支持tp_lcfirst('TEST') 得到tEST
 + ---------------------------------------------------------------------
 * @param string $str
 * @return string
 + ---------------------------------------------------------------------
 */
function tp_lcfirst($str) {
	if(!function_exists('lcfirst')) {
		$tmpStr = ord($str[0]);
		if($tmpStr > 64 && $tmpStr < 90) {
			$str[0] = chr($tmpStr+32);
		}
		return $str;
	} else {
		return lcfirst($str);
	}
}

/**
 + --------------------------------------------------------------------
 * 产生一个匿名对象
 + --------------------------------------------------------------------
 * @param string $object 对象
 * @param bool $isSave 是否保存这个对象，默认为true
 * @return object
 + --------------------------------------------------------------------
 */
function get_object($object,$isSave = true) {
	static $_object = array();
	if(true === $isSave) {
		if(!isset($_object[$object])) {
			$_object[$object] = new $object();
		}
		return $_object[$object]; 
	} else {
		return new $object();
	}
}

/**
 + ---------------------------------------------------------------------
 * 显示一个字符串，并结束执行代码
 + ---------------------------------------------------------------------
 * @param string $str
 * @return void
 + ---------------------------------------------------------------------
 */
function alert($str = '') {
	echo $str;
	exit();
}


/**
 + --------------------------------------------------------------------
 * 截断字符串(支持汉语字符串截断)
 * 注意:必须为utf-8编码的串
 * 此函数是从网上查找得到的,非原创，作者未知
 + --------------------------------------------------------------------
 * @param string $str 待处理的字符串
 * @param int $len 字符串长度
 * @return string
 + --------------------------------------------------------------------
 */
function tp_sub_str($str,$len) {
	$mbChars = array();
	for( $tmp = 0, $strLen = strlen($str), $extLen = 0; $extLen < $len && $tmp < $strLen;) {
		$char = substr($str, $tmp, 1);
		switch ( true ) {
			case ( ord($char) >= 252 ) :
				$mbChars[] = substr($str, $tmp, 6); $extLen++; $tmp += 6; break;
			case ( ord($char) >= 248 ) :
				$mbChars[] = substr($str, $tmp, 5); $extLen++; $tmp += 5; break;
			case ( ord($char) >= 240 ) :
				$mbChars[] = substr($str, $tmp, 4); $extLen++; $tmp += 4; break;
			case ( ord($char) >= 224 ) :
				$mbChars[] = substr($str, $tmp, 3); $extLen++; $tmp += 3; break;
			case ( ord($char) >= 192 ) :
				$mbChars[] = substr($str, $tmp, 2); $extLen++; $tmp += 2; break;
			default :
				$mbChars[] = substr($str, $tmp, 1); $extLen++; $tmp++; break;
		}
	}
	return implode('',$mbChars);
}

/**
 + ----------------------------------------------------------------------
 * 一下是对一些函数取的别名或者一些特殊的函数
 * 函数名称部分借鉴了thinkphp
 + ----------------------------------------------------------------------
 */


/**
 + --------------------------------------------------------------------
 * tp_echo的别名函数
 + --------------------------------------------------------------------
 * @param mixed $var 变量名
 * @param int $type 输出的类型
 * @return void
 + --------------------------------------------------------------------
 */
function P($var,$type = 1) {
	tp_echo($var,$type);
}

/**
 + --------------------------------------------------------------------
 * 配置文件的处理
 * 支持写入配置C('test','test') C(array('testKey'=>'testVal'))
 * 支持写入配置C('testKey=>testKey2','test')
 * 支持读取配置C('test') C('test=>test')
 * 目前不支持C('test','')赋值为空
 + --------------------------------------------------------------------
 * @param mixed $name 变量
 * @param mixed $val 取值
 * @return mixed
 + --------------------------------------------------------------------
 */
function C($name = null,$val = null) {
	static $_config = array();
	if(empty($name)) {
		return $_config;
	} else if(is_string($name)) {
		if(empty($val)) {
			if(!strpos($name,'=>')) {
				//一维
				return isset($_config[$name])?$_config[$name]:null;
			} else {
				//目前只支持二维
				$name = explode('=>',$name);
				return isset($_config[$name[0]][$name[1]])?$_config[$name[0]][$name[1]]:null;
			}
		} else {
			if(!strpos($name,'=>')) {
				//直接设置
				$_config[$name] = $val;
			} else {
				//设置二维
				$name = explode('=>',$name);
				$_config[$name[0]][$name[1]] = $val;
			}
		}
	} else if(is_array($name)) {
		foreach($name as $key=>$value) {
			$_config[$key] = $value;
		}
		return ;
	} else {
		throw new Exception("调用出错");
		return ;
	}
}

/**

 + --------------------------------------------------------------------
 * 路由信息的处理
 * 支持写入路由信息U('test','test') U(array('testKey'=>'testVal'))
 * 支持写入路由信息U('testKey=>testKey2','test')
 * 支持读取路由信息U('test') U('test=>test')
 * 可以U('test','')
 + --------------------------------------------------------------------
 * @param mixed $name 变量
 * @param mixed $val 取值
 * @return mixed
 + --------------------------------------------------------------------
 */
function U($name = null,$val = null) {
	static $_config = array();
	if(empty($name)) {
		return $_config;
	} else if(is_string($name)) {
		if(null === $val) {
			if(!strpos($name,'=>')) {
				//一维
				return isset($_config[$name])?$_config[$name]:null;
			} else {
				//目前只支持二维
				$name = explode('=>',$name);
				return isset($_config[$name[0]][$name[1]])?$_config[$name[0]][$name[1]]:null;
			}
		} else {
			if(!strpos($name,'=>')) {
				//直接设置
				$_config[$name] = $val;
			} else {
				//设置二维
				$name = explode('=>',$name);
				$_config[$name[0]][$name[1]] = $val;
			}
		}
	} else if(is_array($name)) {
		foreach($name as $key=>$value) {
			$_config[$key] = $value;
		}
		return ;
	} else {
		throw new Exception("调用出错");
		return ;
	}
 }

/**
 + --------------------------------------------------------------------
 * 产生一个匿名对象(get_object()函数的别名)
 + --------------------------------------------------------------------
 * @param string $object 对象
 * @param bool $isSave 是否保存这个对象,默认为true
 * @return object
 + --------------------------------------------------------------------
 */
function O($object,$isSave = true) {
	return get_object($object,$isSave);
}
