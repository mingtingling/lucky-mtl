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
 * Toper 路由
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @author mingtingling
 * @version 1.1
 + ------------------------------------------------
 */

class Tp_Route extends Tp {

	/**
	 + -------------------------------------------------
	 * 路由选择(用户透明)
	 * IIS 会出错，以后会加强兼容性
	 + -------------------------------------------------
	 * @access public
	 * @param void
	 * @return void
	 + -------------------------------------------------
	 */
	public static function run() {
		$url = $_SERVER['REQUEST_URI'];
		if(false !== strpos($url,'.php')) {
			U('baseUrl',$_SERVER['HTTP_HOST'].substr($url,0,(strpos($url,'.php')+4)));
			$url = substr($url,strpos($url,'.php')+4);
		} else if(false === C('url=>rewrite')) {
			//解决没有rewrite,但是没有.php这种情况
			U('baseUrl',$_SERVER['HTTP_HOST'].$url.'index.php');
			$url = '';
			
		} else {
			U('baseUrl',$_SERVER['HTTP_HOST']);
		}
		$dealedUrl = self::_parseUrl($url);
		if(false === $dealedUrl) {
			self::_deal404('没有找到该Group');
			return ;
		}
		if(true === C('url=>group')) {
			$class = (implode('_',explode('/',$dealedUrl['group']))).'_'.$dealedUrl['module'].'_'.$dealedUrl['controller'].'Controller';
			$path = MODULES_PATH.'/Controllers/'.$dealedUrl['group'].'/'.$dealedUrl['module'].'/'.$dealedUrl['controller']."Controller.class.php";
			$cachePath = APP_PATH.C('compile=>fileCompileSavePath').'/Controllers/'.$dealedUrl['group'].'/'.$dealedUrl['module'].'/'.$dealedUrl['controller']."Controller.class.php";
		} else {
			$class = $dealedUrl['module'].'_'.$dealedUrl['controller'].'Controller';
			$path = MODULES_PATH.'/Controllers/'.$dealedUrl['module'].'/'.$dealedUrl['controller']."Controller.class.php";
			$cachePath = APP_PATH.C('compile=>fileCompileSavePath').'/Controllers/'.$dealedUrl['module'].'/'.$dealedUrl['controller']."Controller.class.php";
		}
		$action = $dealedUrl['action'].'Action';
		if((!is_file($path)) && (!is_file($cachePath))) {
			self::_deal404('没有找到该控制器');
		} else {
			tp_include(TP_PATH.'/Core/Controller.class.php');
			import('My.Controllers.'.str_replace('_','/',$class));
			$controller = new $class();
			if(method_exists($class,$action)) {
				U($dealedUrl);
				$controller->$action();
			} else {
				self::_deal404('没有找到该控制器的Action');
			}
		}
	}

	/**
	 + -----------------------------------------------------
	 * 解析URL(用户透明)
	 + -----------------------------------------------------
	 * @access protected
	 * @static
	 * @param string $url URL
	 * @return mixed URL信息
	 + -----------------------------------------------------
	 */
	protected static function _parseUrl($url){
		if(0 === strpos($url,'/')) {
			$url = substr($url,1);
		}
		$urlLen = strlen($url);
		if(($urlLen-1) == strrpos($url,'/')) {
			$url = substr($url,0,($urlLen-1));
		}
		U('extra',''); //默认设置extra信息为空
		if(!$url) {
			$defaultGroup = '';
			if(false !== C('url=>group')) {
				$defaultGroup = implode('/',explode('=>',C('url=>defaultGroup')));
			}
			//没有解析到相应的URL，调用默认URL
			return array(
				'group' => $defaultGroup,
				'module'=>C('url=>defaultModule'),
				'controller'=>C('url=>defaultController'),
				'action'=>C('url=>defaultAction')
			);
		} else {
			return self::_parseRealUrl($url);
		}
	}

	/**
	 + -----------------------------------------------------
	 * 解析一个URL(非空)(用户透明)
	 + -----------------------------------------------------
	 * @access protected
	 * @static
	 * @param string $url URL
	 * @return mixed URL信息
	 + -----------------------------------------------------
	 */
	protected static function _parseRealUrl($url) {
		$groupDepth = C('url=>groupDepth');
		$group = '';
		$savedRegExp = '/^'; //保存的正则,以备后面查找model使用
		if(false !== C('url=>group')) {
			for($tmp = $groupDepth; $tmp >= 1; $tmp --) {
				$pattern = '/^(';
				for($tmpCount = 1; $tmpCount < $tmp; $tmpCount ++) {
					$pattern .= '.*?'.'\\'.C('url=>division');
				}
				$pattern .= '[0-9a-zA-Z_-]{1,})';
				$savedRegExp = $pattern;
				$pattern .= '/';
				if(preg_match($pattern,$url,$groupArr)) {
					if('/' !== C('url=>division')) {
						$groupArr[1] = str_replace(C('url=>division'),'/',$groupArr[1]);
					}
					$path = MODULES_PATH.'/Controllers/'.'/'.$groupArr[1];
					if(file_exists($path)) {
						$group = $groupArr[1];
						break;
					}
				}
			}
			if('' === $group) {
				return false;
			}
		}
		$module = '';
		$controller = '';
		$action = '';
		$savedRegExp .= (true === C('url=>group'))?('\\'.C('url=>division').'([0-9a-zA-Z-_]{1,})'):'([0-9a-zA-Z-_]{1,})';
		$pattern = $savedRegExp.'/';
		if(preg_match($pattern,$url,$moduleArr)) {
			$module = (true === C('url=>group'))?$moduleArr[2]:$moduleArr[1];
			$savedRegExp .= ('\\'.C('url=>division').'([0-9a-zA-Z-_]{1,})');
			$pattern = $savedRegExp.'/';
			if(preg_match($pattern,$url,$controllerArr)) {
				$controller = (true === C('url=>group'))?$controllerArr[3]:$controllerArr[2];
				$savedRegExp .= ('\\'.C('url=>division').'([0-9a-zA-Z-_]{1,})(.*)');
				$pattern = $savedRegExp.'/';
				if(preg_match($pattern,$url,$actionArr)) {
					$action = (true === C('url=>group'))?$actionArr[4]:$actionArr[3];
					$extraInfo = (true === C('url=>group'))?$actionArr[5]:$actionArr[4];
					if($extraInfo) {
						$extraInfo = substr($extraInfo,1);
						U('extra',$extraInfo);
					}
					return array(
						'group' => $group,
						'module' => $module,
						'controller'=> $controller,
						'action'=> $action
					);
				} else {
					return array(
						'group' => $group,
						'module' => $module,
						'controller'=> $controller,
						'action'=>C('url=>defaultAction')
					);
				}
			} else {
				return array(
					'group' => $group,
					'module' => $module,
					'controller'=>C('url=>defaultController'),
					'action'=>C('url=>defaultAction')
				);
			}
		} else {
			return array(
				'group' => $group,
				'module'=>C('url=>defaultModule'),
				'controller'=>C('url=>defaultController'),
				'action'=>C('url=>defaultAction')
			);
		}
	}

	/**
	 + -----------------------------------------------------
	 * 处理404(用户透明)
	 + -----------------------------------------------------
	 * @access private
	 * @static
	 * @param string $errorMsg
	 * @return void
	 + -----------------------------------------------------
	 */
	private static function _deal404($errorMsg) {
		if(true === C('appDebug')) {
			echo "<font color = 'red'>[ERROR]".$errorMsg."</font>";
		} else {
			if(true === C('url=>group')) {
				$error404ControllerPath = MODULES_PATH.'/Controllers/'.(implode('/',explode('=>',C('url=>defaultGroup')))).'/'.C('url=>defaultModule').'/'.C('url=>defaultController').'Controller.class.php';
				$error404Controller = (implode('_',explode('=>',C('url=>defaultGroup')))).'_'.C('url=>defaultModule').'_'.C('url=>defaultController').'Controller';
			} else {
				$error404ControllerPath = MODULES_PATH.'/Controllers/'.C('url=>defaultModule').'/'.C('url=>defaultController').'Controller.class.php';
				$error404Controller = C('url=>defaultModule').'_'.C('url=>defaultController').'Controller';
			}
			if(!is_file($error404ControllerPath)) {
				echo file_get_contents(APP_PATH.C('=error404ErrorPath'));
			} else {
				tp_include($error404ControllerPath);
				$class = new $error404Controller();
				$action = C('url=>error404Action').'Action';
				if(method_exists($class,$action)) {
					$class->$action();
				} else {
					echo file_get_contents(TP_PATH.'/404.html');
				}
			}
		}
	}
}