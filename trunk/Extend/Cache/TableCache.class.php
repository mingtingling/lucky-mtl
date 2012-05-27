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
 * Toper 缓存表,辅助类
 * 外部不应该直接调用，而是调用Tp_CacheFactory
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Cache
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */

tp_include(TP_PATH.'/Core/Model.class.php');
class Tp_TableCache extends Tp_CacheAbstract {

	private $_table = null; //缓存表
	
	/**
	+-----------------------------------------------------------------
	* 此缓存是为了存储多表查询的结果而建立的
	* 用户在使用此缓存之前需要建立这个表,表里面的项如下
	* | 属性名 数据类型  含义  其他
	* | name   varchar  变量  主键
	* | val   text    值
	* | expire int		过期时间
	* | time datetime 缓存记录的时间
	* | type varchar 记录的数据的类型
	+-----------------------------------------------------------------
	*/
	public function __construct() {
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
		if(!$this->isConnected()) {
			$this->_cache = Tp_Model::driverFactory(C('db'));
			$this->_table = C('db=>prefix').C('db=>cacheTable');
		}
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
		$this->_cache->execute("select val,expire,time,type from $this->_table where name = '{$name}'");
		$data = $this->_cache->getAll();
		if(!$data) {
			return false;
		} else {
			if($data['expire']) {
				if(time() > $data['time'] + $data['expire']) {
					$this->_cache->execute("delete from $this->_table where name = '{$name}'");
					return false;
				} else {
					if('array' === $data['type']) {
						return json_decode($data['val'],true);
					} else {
						return $data['val'];
					}
				}
			} else {
				if('array' === $data['type']) {
					return json_decode($data['val'],true);
				} else {
					return $data['val'];
				}
			}
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
		$now = date("Y-m-d H:i:s");
		$type = 'string';
		if(is_array($val)) {
			$type = 'array';
			$val = json_encode($val);
		}
		return $this->_cache->execute("insert into $this->_table (name,val,expire,time,type) values ('{$name}','{$val}','{$expire}','{$now}','{$type}')");
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
		$this->_cache->execute("select time,expire from $this->_table where name = '".$name."'");
		$data = $this->_cache->getAll();
		if($data) {
			if($data['expire']) {
				if(time() > $data['time'] + $data['expire']) {
					$this->_cache->execute("delete from $this->_table where name = '{$name}'");
				} else {
					return true;
				}
			} else {
				return true;
			}		
		} else {
			return false;
		}
	}
	
	/**
	+---------------------------------------------------------------
	* 移除某一个缓存变量的值
	+---------------------------------------------------------------
	* @access public
	* @param string $name
	* @return void
	+---------------------------------------------------------------
	*/
	public function remove($name) {
		$this->_cache->execute("delete from $this->_table where name = '".$name."'");
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
		$this->_cache->execute("delete from $this->_table");
	}
}