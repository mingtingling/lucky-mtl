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
 * Toper 文件缓存,辅助类
 * 外部不应该直接调用，而是调用Tp_CacheFactory
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Cache
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */

tp_include(TP_PATH.'/Core/Helper/Encode.class.php');
tp_include(TP_PATH.'/Core/Helper/Decode.class.php');

class Tp_FileCache extends Tp_CacheAbstract {
	
	private $_prefix = null; //文件缓存的前缀
	private $_savePath = null; //文件缓存保存路径
	private $_isEncode = false; //是否加密
	
	public function __construct() {
		if(!$this->isConnected()) {
			$this->_init();
		}
	}

	/**
	+---------------------------------------------------------------
	* 缓存初始化
	+---------------------------------------------------------------
	* @access private
	* @param void
	* @return void
	+---------------------------------------------------------------
	*/
	private function _init() {
		$this->_prefix = C('cache=>fileCachePrefix');
		$this->_savePath = APP_PATH.C('cache=>fileCacheSavePath');
		$this->_isEncode = C('cache=>fileCacheEncode');
		if(!is_dir($this->_savePath)) {
			mkdir($this->_savePath);
		}
		$this->connect();
	}

	/**
	+---------------------------------------------------------------
	* 得到文件路径(绝对路径)
	+---------------------------------------------------------------
	* @access private
	* @param string $fileName
	* @return string
	+---------------------------------------------------------------
	*/
	private function _getFilePath($fileName) {
		return $this->_savePath.'/'.$this->_prefix.$fileName;
	}

	/**
	+---------------------------------------------------------------
	* 处理数据(存储前)
	+---------------------------------------------------------------
	* @access private
	* @param mixed $data
	* @param int $expire
	* @return string
	+---------------------------------------------------------------
	*/
	private function _dealDataBeforeSave($data,$expire = null) {
		$operation = array();
		if(is_array($data)) {
			$operation['type'] = 'array';
			$operation['expire'] = $expire ? $expire : 0;
			$data = json_encode($data);
			if($this->_isEncode) {
				$operation['encode'] = true;
				$data = Tp_Encode::tp($data);
			} else {
				$operation['encode'] = false;
			}
			$operation = json_encode($operation);
			return $operation.'=>'.$data;
		} else if(is_string($data)) {
			$operation['type'] = 'string';
			$operation['expire'] = $expire ? $expire : 0;
			if($this->_isEncode) {
				$operation['encode'] = true;
				$data = Tp_Encode::tp($data);
			} else {
				$operation['encode'] = false;
			}
			$operation = json_encode($operation);
			return $operation.'=>'.$data;
		} else {
			tp_include(TP_PATH.'/Core/Exception/CommonException.class.php');
			throw new Tp_CommonException(Tp_CommonException::INCORRECT_VAR_TYPE);
		}
	}
	
	/**
	+---------------------------------------------------------------
	* 处理数据(得到前)
	+---------------------------------------------------------------
	* @access private
	* @param string $str
	* @param string $filePath
	* @return mixed
	+---------------------------------------------------------------
	*/
	private function _dealDataBeforeGet($str,$filePath) {
		$fileData = explode('=>',$str);
		$operation = json_decode($fileData[0],true);
		$data = $fileData[1];
		if(!$operation['expire']) {
			if('array' === $operation['type']) {
				if($operation['encode']) {
					return json_decode(Tp_Decode::tp($data),true);
				} else {
					return json_decode($data,true);
				}
			} else if('string' === $operation['type']) {
				if($operation['encode']) {
					return Tp_Decode::tp($data);
				} else {
					return $data;
				}
			} else {
				return ;
			}
		} else {
			if(time() > filemtime($filePath)+$operation['expire']) {
				unlink($filePath);
				return false;
			} else {
				if('array' === $operation['type']) {
					if($operation['encode']) {
						return json_decode(Tp_Decode::tp($data),true);
					} else {
						return json_decode::tp($data,true);
					}
				} else if('string' === $operation['type']) {
					if($operation['encode']) {
						return Tp_Decode::tp($data);
					} else {
						return $data;
					}
				} else {
					return ;
				}
			}
		}
	}
	
	/**
	+---------------------------------------------------------------
	* 连接到缓存
	+---------------------------------------------------------------
	* @access public
	* @param void
	* @return void
	+---------------------------------------------------------------
	*/
	public function connect() {
		$this->_cache = true;
	}
	
	
	/**
	+---------------------------------------------------------------
	* 得到某一个缓存变量的值
	+---------------------------------------------------------------
	* @access public
	* @param string $name
	* @return mixed
	+---------------------------------------------------------------
	*/
	public function get($name) {
		$filePath = $this->_getFilePath($name);
		if(!is_file($filePath)) {
			return false;
		}
		try {
			$fileData = file_get_contents($filePath);
			return $this->_dealDataBeforeGet($fileData,$filePath);
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}
	
	/**
	+---------------------------------------------------------------
	* 设置某一个缓存变量的值
	+---------------------------------------------------------------
	* @access public
	* @param string $name
	* @param mixed $val
	* @param int $expire 过期时间
	* @return bool
	+---------------------------------------------------------------
	*/
	public function set($name,$val,$expire = null) {
		return file_put_contents($this->_getFilePath($name),$this->_dealDataBeforeSave($val,$expire));
	}
	
	/**
	+---------------------------------------------------------------
	* 是否存在某一个缓存变量的值
	+---------------------------------------------------------------
	* @access public
	* @param string $name
	* @return bool
	+---------------------------------------------------------------
	*/
	public function have($name) {
		$filePath = $this->_getFilePath($name);
		if(!is_file($filePath)) {
			return false;
		}
		try {
			$data = file_get_contents($filePath);
			$operation = substr($data,0,strpos($data,'=>'));
			$operation = json_decode($operation,true);
			if($operation['expire']) {
				if(time() > filemtime($filePath) + $operation['expire']) {
					unlink($filePath);
					return false;
				} else {
					return true;
				}
			} else {
				return true;
			}
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}
	
	/**
	+---------------------------------------------------------------
	* 移除某一个缓存
	+---------------------------------------------------------------
	* @access public
	* @param string $name
	* @return void
	+---------------------------------------------------------------
	*/
	public function remove($name) {
		unlink($this->_getFilePath($name));
	}
	
	/**
	+---------------------------------------------------------------
	* 清除所有缓存
	+---------------------------------------------------------------
	* @access public
	* @param void
	* @return void
	+---------------------------------------------------------------
	*/
	public function clear() {
		if($dir = opendir($this->_savePath)) {
			while($file = readdir($dir)) {
				if(("." !== $file) && (".." != $file)) {
					if(false !== strpos($file,$this->_prefix)) {
						unlink($this->_savePath.'/'.$file);
					}
				}
			}
			closedir($dir);
		}
	}
}