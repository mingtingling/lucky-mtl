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
 * Toper MemCache,辅助类
 * 外部不应该直接调用，而是调用Tp_CacheFactory
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Cache
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */

class Tp_MemCache extends Tp_CacheAbstract {

	public function __construct() {
		if(!class_exists('Memcached')) {
			throw new Exception('系统不支持Memcache,请修改php.ini');
		}
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
		$this->connect();
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
		$this->_cache = new Memcached();
	}

	/**
	+---------------------------------------------------------------
	* 设置server
	* 支持$cache->setServer('127.0.0.1',11211);
	+---------------------------------------------------------------
	* @access public
	* @param string $host
	* @param int $port
	* @return bool
	+---------------------------------------------------------------
	*/
	public function setServer($host = 'localhost',$port = 11211) {
		return $this->_cache->addServer($host,$port);
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
		return $this->_cache->get($name);
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
		if(null == $expire) {
			return $this->_cache->set($name,$val);
		} else {
			return $this->_cache->set($name,$val,time() + $expire);
		}
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
		if(false === $this->_cache->get($name)) {
			return false;
		}
		return true;
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
		$this->_cache->delete($name);
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
		$this->_cache->flush();
	}
}