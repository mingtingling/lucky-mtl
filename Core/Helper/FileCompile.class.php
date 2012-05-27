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
 * Toper 编译某一个文件的类
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Helper
 * @author mingtingling
 * @version 1.1
 +---------------------------------------------------
 */
class Tp_FileCompile extends Tp{

	/**
	 + -----------------------------------------------------
	 * 编译
	 * 暂时和shortFile函数一样
	 + -----------------------------------------------------
	 * @access public
	 * @param string $file
	 * @param string $savePath
	 * @param bool $isEcho 是否输出编译的文件信息
	 * @return void
	 + ----------------------------------------------------
	 */
	public static function compile($file,$saveFile,$isEcho = false) {
		if(true !== is_readable($file)) {
			if($isEcho) {
				echo "<br/>文件不可读，可能没有读的权限或者该文件不存在";
			}
		} else {
			$buffer = file_get_contents($file);
			$suffix = substr($file,strrpos($file,'.')+1);
			$fileCompiled = self::_parse($buffer,$suffix,$isEcho);
			//编译后的文件
			file_put_contents($saveFile,$fileCompiled);
			if($isEcho) {
				echo "<br/>文件:".$file."被编译,保存该文件到:".$saveFile;
			}
		}
	}

	/**
	 + -----------------------------------------------------
	 * 去掉空白和注释,减小文件大小
	 + -----------------------------------------------------
	 * @access public
	 * @param string $file
	 * @param string $savePath
	 * @param bool $isEcho 是否输出替换后的文件信息
	 * @return void
	 + ----------------------------------------------------
	 */
	public static function shortFiles($file,$saveFile,$isEcho = false) {
		if(true !== is_readable($file)) {
			if($isEcho) {
				echo "<br/>文件不可读，可能没有读的权限或者该文件不存在";
			}
		} else {
			$buffer = file_get_contents($file);
			$suffix = substr($file,strrpos($file,'.')+1);
			$fileCompiled = self::_parse($buffer,$suffix,$isEcho);
			//编译后的文件
			//$fileCompiled = self::_optimizePhpFile($fileCompiled);
			file_put_contents($saveFile,$fileCompiled);
			if($isEcho) {
				echo "<br/>文件:".$file."被编译,保存该文件到:".$saveFile;
			}
		}
	}

	/**
	 + -----------------------------------------------------
	 * 解析字符串(处理打开的文件)
	 + -----------------------------------------------------
	 * @access protected
	 * @param string $str
	 * @param string $suffix 文件后缀名
	 * @param bool $isEcho
	 * @return string
	 + ----------------------------------------------------
	 */
	protected static function _parse($str,$suffix,$isEcho = false) {
		if('php' === strtolower($suffix)) {
			return self::_parsePhp($str);
		} else {
			//后面再写
			if($isEcho) {
				echo "<br/>文件类型出错";
			}
		}
	}

	/**
	 + -----------------------------------------------------
	 * 解析PHP文件
	 + -----------------------------------------------------
	 * @access protected
	 * @param string $str
	 * @return string
	 + ----------------------------------------------------
	 */
	protected static function _parsePhp($str) {
		$pattern = array(
		"/\/\*(.*?)\*\//s", //查找/* */注释,多行匹配
		"/\s\/\/.*/", //查找//注释,此处稍微有点问题，因为在FrontController中_redirect函数使用到了//
						//为了防止它误操作，所以在//前加了一个空格作为判断依据
		"/\s{2,}/" //查找多个空格
		);
		$replace = array(
		"",
		"",
		" "
		);
		return preg_replace($pattern,$replace,$str);
	}
	/**
	 + -----------------------------------------------------
	 * 优化PHP文件
	 + -----------------------------------------------------
	 * @access protected
	 * @param string $str
	 * @return string
	 + ----------------------------------------------------
	 */
	protected static function _optimizePhpFile($str) {
		$pattern = array(
		"/\?>\s*?<\?php/",
		"/\s{2,}/"
		);
		$replace = array(
		"",
		" "
		);
		return preg_replace($pattern,$replace,$str);
	}
}