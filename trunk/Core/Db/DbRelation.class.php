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
 * Toper DbRelation
 * 本框架支持DbRelation和DbTable两种方式
 * 调用DbTable是以表为单位
 * 调用DbRelation是以相关的信息为单位
 +---------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Db
 * @author mingtingling
 * @version 1.1
 + --------------------------------------------------
 */
 
tp_include(TP_PATH.'/Core/Model.class.php');
class Tp_DbRelation extends Tp_Model {

	protected $_key = null;
	//所关联的项
	protected $_tables = array();
	//所关联的表
	protected $_table = null;
	//主表
	
	/**
	+ --------------------------------------------------------
	* 构造函数
	+ --------------------------------------------------------
	* @access public
	* @param mixed $config 配置信息，支持字符串(DSN)和数组(ORD)
	* @param mixed $relation 所关联的项
	* @return void
	+ ---------------------------------------------------------
	*/
	function __construct($config = array(),$relation = null) {
		parent::__construct($config);
		if(empty($relation)) {
			$this->_setKey();
		} else {
			$this->_key = $relation;
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
		if('_' === substr($name,0,1)) {
			$dealedName = substr($name,1);
			if(isset($this->_tables[$dealedName])) {
				//优先查找_tables里面的内容
				return $this->_tables[$dealedName];
			}	
		}
		return parent::__get($name);
	}

	/**
	 + --------------------------------------------
	 * 设置关联的表
	 * 支持 $this->_initTables(
	 * 		'A' => 'testTable',
	 * 		'testTable2',
	 * 		'_table' => 'testTable'
	 * )
	 * 使用这个函数之后就可以直接使用 
	 * 	$this->_A
	 * 	$this->_testTable2
	 * 注意:_table指定之后，您以后就可以使用$this->_table
	 * 它代表默认的一个表
	 + --------------------------------------------
	 * @access protected
	 * @param array $arr
	 * @return void
	 + --------------------------------------------
	 */
	 protected function _initTables($arr = array()) {
	 	foreach($arr as $key => $val) {
	 		if(is_numeric($key)) {
	 			$this->_tables[$val] = $this->_tablePrefix.$val;
	 		} else {
	 			if('_table' === $key) {
					$this->_table = $this->_tablePrefix.$val;
	 			} else {
		 			$this->_tables[$key] = $this->_tablePrefix.$val;	
				}
	 		}
	 	}
	 }

	/**
	 + --------------------------------------------
	 * 设置Key
	 + --------------------------------------------
	 * @access protected
	 * @param void
	 * @return void
	 + --------------------------------------------
	 */
	protected function _setKey() {
		$className = get_class($this);
		$className = strtolower(substr($className,strrpos($className,'_') + 1));
		$this->_key = $className;
	}


	/**
	+ ----------------------------------------------
	* 得到某一个属性值
	+ ----------------------------------------------
	* @access protected
	* @param string $property 属性名
	* @return mixed
	+ ----------------------------------------------
	*/
	protected function _getter($property) {
		$this->_options['field'] = $property;
		return $this->select(Tp_DbConstants::AUTO_FETCH_DATA);
	}
	
	/**
	+ ----------------------------------------------
	* 设置某一个属性值
	+ ----------------------------------------------
	* @access protected
	* @param string $property
	* @param mixed $val 取值
	* @return mixed
	+ ----------------------------------------------
	*/
	protected function _setter($property,$val) {
		return $this->update(array($property => $val));
	}

	/**
	 + ---------------------------------------------
	 * select方法
	 * 使用方法详见:Tp_Model
	 + ---------------------------------------------
	 * @access public
	 * @param mixed $autoGetData 是否自动取得数据
	 * @return mixed
	 + ---------------------------------------------
	 */
	public function select($autoGetData = false) {
		$this->_setOptionTable();
		$tmp = $this->_db->select($this->_options,$autoGetData);
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
			if(null !== $this->_table) {
				$this->_options['table'] = $this->_table;
			}
		}
	}
}