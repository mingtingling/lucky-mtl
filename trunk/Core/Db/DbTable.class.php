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
 * Toper DbTable 对应数据库的表
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Db
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */
tp_include(TP_PATH.'/Core/Model.class.php');
class Tp_DbTable extends Tp_Model {

	protected $_isGetTableInfo = true;
	//是否得到表结构的信息，如果需要查看表结构，可以将此设为true
	protected $_pKey = "";
	//主键
	protected $_tableInfo = array();
	//数据库表的信息
	protected $_tableName = "";
	//表名（不包含前缀）
	protected $_tablePrefix = "";
	//表前缀
	protected $_table = "";
	//表的全称
	
	/**
	+ --------------------------------------------------------
	* 构造函数
	+ --------------------------------------------------------
	* @access public
	* @param mixed $config 配置信息，支持字符串(DSN)和数组(ORD)
	* @param string $tableName 数据库表名（不含前缀）
	* @return void
	+ --------------------------------------------------------
	*/
	function __construct($config = array(),$tableName = '') {
		parent::__construct($config);
		if(!empty($tableName)) {
			$this->_tableName = $tableName;
			$this->_table = $this->_tablePrefix.$this->_tableName;
		}
		$this->_setTable();
	}

	/**
	 + ---------------------------------------------
	 * 设置数据库表
	 + ---------------------------------------------
	 * @access protected
	 * @param void
	 * @return void
	 + ---------------------------------------------
	 */
	protected function _setTable() {
		if(!$this->_tableName) {
			//用户没有在子类自己实现tableName赋值
			$className = get_class($this);
			$className = strtolower(substr($className,strrpos($className,'_') + 1));
			$this->_tableName = $className;
		}
		$this->_table = $this->_tablePrefix.$this->_tableName;
	}

	/**
	 + ---------------------------------------------
	 * 得到表的名字（含表前缀)
	 + ---------------------------------------------
	 * @access public
	 * @param void
	 * @return string
	 + ---------------------------------------------
	 */
	public function getTableFullName() {
		return $this->_table;
	}


	/**
	+ ----------------------------------------------
	* 得到表的某一个属性值
	+ ----------------------------------------------
	* @access protected
	* @param string $property 属性名
	* @return mixed
	+ ----------------------------------------------
	*/
	protected function _getter($property) {
		if(in_array($property,$this->_tableInfo)) {
			$this->_options['field'] = $property;
			$this->select();
			return $this->getAll();
		} else {
			tp_include(TP_PATH.'/Core/Exception/DbException.class.php');
			throw new Tp_DbException(Tp_DbException::NONE_EXISTS_GETTER);
			return ;
		}
	}
	
	/**
	+ ----------------------------------------------
	* 设置表的某一个属性值
	+ ----------------------------------------------
	* @access protected
	* @param string $property
	* @param mixed $val 取值
	* @return mixed
	+ ----------------------------------------------
	*/
	protected function _setter($property,$val) {
		if(in_array($property,$this->_tableInfo)) {
			return $this->update(array($property => $val));
		} else {
			tp_include(TP_PATH.'/Core/Exception/DbException.class.php');
			throw new Tp_DbException(Tp_DbException::NONE_EXISTS_SETTER);
			return ;
		}
	}
	
	/**
	 + ---------------------------------------------
	 * select方法
	 * 使用方法详见:Tp_Model
	 + ---------------------------------------------
	 * @access public
	 * @param int $operation
	 * @return mixed
	 + ---------------------------------------------
	 */
	public function select($operation = null) {
		$this->_setOptionTable();
		$tmp = $this->_db->select($this->_options,$operation);
		//清除options操作，防止下次干扰
		$this->_clearOptions();
		return $tmp;
	}

	/**
	 + ----------------------------------------------
	 * insert
	 * 使用方法详见:Tp_Model
	 + ----------------------------------------------
	 * @access public
	 * @param array $data
	 * @return bool
	 + ----------------------------------------------
	 */
	public function insert($data) {
		$this->_setOptionTable();
		$tmp = $this->_db->insert($data,$this->_options);
		$this->_clearOptions();
		return $tmp;
	}

	/**
	 + ----------------------------------------------
	 * update
	 * 使用方法详见:Tp_Model
	 + ----------------------------------------------
	 * @access public
	 * @param array $data
	 * @return bool
	 + ----------------------------------------------
	 */
	public function update($data) {
		$this->_setOptionTable();
		$tmp = $this->_db->update($data,$this->_options);
		$this->_clearOptions();
		return $tmp;
	}

	/**
	 + ----------------------------------------------
	 * delete
	 * 使用方法详见:Tp_Model
	 + ----------------------------------------------
	 * @access public
	 * @param void
	 * @return bool
	 + ----------------------------------------------
	 */
	public function delete() {
		$this->_setOptionTable();
		$tmp = $this->_db->delete($this->_options);
		$this->_clearOptions();
		return $tmp;
	}

	/**
	 + -----------------------------------------------
	 * save 保存数据 包括insert和update
	 * 这个函数仅仅是对insert和update的封装
	 + -----------------------------------------------
	 * @access public
	 * @param array $arr 要保存的数据
	 * @param string $command 命令
	 * @return bool
	 + -----------------------------------------------
	 */
	public function save($arr) {
		$this->_setOptionTable();
		if(isset($this->_options['where'])) {
			$tmp = $this->_db->update($arr,$this->_options);
		} else {
			$tmp = $this->_db->insert($arr,$this->_options);
		}
		$this->_clearOptions();
		return $tmp;
	}


	/**
	 + -----------------------------------------------
	 * 设置option的table
	 * 针对select,insert,update,delete的时候
	 * $option['table']没有设置的情况
	 + -----------------------------------------------
	 * @access protected
	 * @param void
	 * @return void
	 + -----------------------------------------------
	 */
	protected function _setOptionTable() {
		if(!isset($this->_options['table'])) {
			$this->_options['table'] = $this->_table;
		}
	}
}