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
 * Toper 文件处理类
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Helper
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */
class Tp_File extends Tp {

	/**
	 + ------------------------------------------------------------
	 * 搜索目录（多级），如果不存在则建立目录，并且将内容写入到该文件
	 + ------------------------------------------------------------
	 * @access public
	 * @param string $base 基路径
	 * @param string $path 搜索路径
	 * @return void
	 + ------------------------------------------------------------
	 */
	public static function write($base,$path) {
		mkdir($base,$path);
		file_put_contents($base.$path);
	}

	/**
	 + ------------------------------------------------------------
	 * 搜索目录（多级），如果不存在则建立目录
	 + ------------------------------------------------------------
	 * @access public
	 * @param string $base 基路径,最后没有/
	 * @param string $path 搜索路径,最前面可能存在/
	 * @return void
	 + ------------------------------------------------------------
	 */
	public static function mkdir($base,$path) {
		$path = substr($path,0,strrpos($path,'/'));
		//搜索路径
		if(file_exists($base.$path)) {
			return '';
		} else {
			if('/' === substr($path,0,1)) {
				//路径中存在/,去掉
				$path = substr($path,1);
			}
			$path = explode('/',$path);
			$tmpPath = $base;
			foreach($path as $tmp) {
				$tmpPath .= ("/".$tmp);
				if(!file_exists($tmpPath)) {
					mkdir($tmpPath,0777);
					//以最大权限建立文件夹
				}
			}
		}
	}

	/**
	 + ------------------------------------------------------------
	 * 搜索目录（多级），查看文件是否存在
	 * 即从$base目录开始搜索，查看本目录或其子目录是否存在某一个文件
	 * 如果存在，则返回其相对于$base的路径，否则，返回false
	 + ------------------------------------------------------------
	 * @access public
	 * @param string $base 基路径,最后没有/
	 * @param string $path 搜索路径,最前面可能存在/
	 * @return mixed
	 + ------------------------------------------------------------
	 */
	public static function fileExists($base,$file) {

	}
	/**
	 + ------------------------------------------------------------
	 * 删除某一个文件夹下面所有的文件和文件夹
	 + ------------------------------------------------------------
	 * @access public
	 * @param string $folder
	 * @param bool $isEcho 是否输出调试信息
	 * @return mixed
	 + ------------------------------------------------------------
	 */
	public static function rmAllFiles($folder,$isEcho = false) {
		$files = self::listAllFiles($folder);
		foreach($files as $tmp) {
			if(is_dir($folder.'/'.$tmp)) {
				//如果是目录，则递归查询
				self::rmAllFiles($folder.'/'.$tmp);
				rmdir($folder.'/'.$tmp);
				if($isEcho) {
					echo("<br/>删除目录:".$folder.'/'.$tmp);
				}
			} else {
				//是文件，则删除
				unlink($folder.'/'.$tmp);
				echo("<br/>删除文件:".$folder.'/'.$tmp);
			}
		}
	}

	/**
	 + -------------------------------------------------------------
	 * 得到某一个目录的所有文件，不包括. ..
	 + -------------------------------------------------------------
	 * @access public
	 * @param string $path
	 * @return array
	 + --------------------------------------------------------------
	 */
	public static function listAllFiles($path) {
		$tmpArr = array();
		if ($handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if (("." !== $file) && (".." != $file)) {
					$tmpArr[] = $file;
				}
			}
			closedir($handle);
		} else {
			throw new Exception('文件夹路径出错或者权限限制');
		}
		return $tmpArr;
	}
}
