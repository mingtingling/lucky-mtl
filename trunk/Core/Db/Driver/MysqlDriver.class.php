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
 * Toper Mysql驱动文件
 * 注意: 此驱动文件只支持ORD方式
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Db
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */

tp_include(TP_PATH.'/Core/Db/DbBase.class.php');
tp_include(TP_PATH.'/Core/Db/DbArrayObjectList.class.php');
tp_include(TP_PATH.'/Core/Db/DbArrayObject.class.php');

class Tp_MysqlDriver extends Tp_DbBase {
	
	private $_query = null;
	//执行查询之后的连接
	
	public function __construct($config = array()) {
		parent::__construct($config);
	}

	public function __destruct() {
		$this->close();
	}

	public function __call($method,$args) {
		throw new Exception("在mysql驱动中不存在该方法");
	}

	/**
	 + --------------------------------------------------------
	 * 连接数据库
	 + --------------------------------------------------------
	 * @access public
	 * @param void
	 * @return void
	 + --------------------------------------------------------
	 */
	public function connect() {
		if(!$this->_db) {
			//没有连接到数据库
			if(isset($this->_config['port']) && is_int($this->_config['port']) && $t(his->_config['port'] > 0)) {
				$server = $this->_config['host'].':'.$this->_config['port'];
			} else {
				$server = $this->_config['host'];
			}
			$this->_db = mysql_connect($server,$this->_config['user'],$this->_config['pwd']);
			if(!$this->_db) {
				echo 'can not connect to the database server by mysql drive,the error is :'.mysql_error();
				return ;
			}
			if(!mysql_select_db($this->_config['name'],$this->_db)) {
				echo 'fail to select the db,the error is:'.mysql_error();
				return ;
			}
			if(isset($this->_config['encoding']) && $this->_config['encoding']) {
				mysql_query('set names '.$this->_config['encoding']);
			}
		}
	}

	/**
	 + -----------------------------------------------------------------
	 * 断开数据库
	 + -----------------------------------------------------------------
	 * @access public
	 * @param void
	 * @return void
	 + -----------------------------------------------------------------
	 */
	public function close() {
		mysql_close($this->_db);
	}

	/**
	 + --------------------------------------------------------
	 * 执行SQL语句,原生态的SQL执行
	 + --------------------------------------------------------
	 * @access public
	 * @param string $sql
	 *@param int $operation
	 * @return bool
	 + --------------------------------------------------------
	 */
	public function exec($sql,$operation = null) {
		if($this->execute($sql,$operation)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 + --------------------------------------------------------
	 * 执行SQL语句
	 + --------------------------------------------------------
	 * @access public
	 * @param string $sql
	 * @param int $operation 要执行的操作
	 * @return mixed
	 + --------------------------------------------------------
	 */
	public function execute($sql = '',$operation = null) {
		$this->_startCal();
		if(null === $this->_db) {
			$this->connect();
		}
		if(!$sql) {
			$this->_log("没有SQL",Tp_DbConstants::ERROR);
			return false;
		}
		if(null !== $this->_query) {
			$this->_free();
		}
		$this->_query = mysql_query($sql,$this->_db);
		if($this->_query) {
			$this->_log("执行了execute()函数,SQL为:".$sql);
		} else {
			$this->_log("执行了execute()函数,SQL为:".$sql.",错误代码为:".mysql_errno().",错误信息为:".mysql_error(),Tp_DbConstants::ERROR);
		}
		$this->_getAll($operation);
		$this->_count ++;
		$this->_queryStr = $sql;
		$this->_exeSql[] = $sql;
		return $this->_query;
	}

	/**
	 + -------------------------------------------------------
	 * 通过对象返回数据的辅助方法
	 + -------------------------------------------------------
	 * @access private
	 * @param void
	 * @return void
	 + -------------------------------------------------------
	 */
	private function _getAllByObj() {
		$tmpLen = 0;
		$this->_queryResult = new Tp_DbArrayObjectList();
		while($obj = mysql_fetch_object($this->_query)) {
			$this->_queryResult->insertObject($obj);
			$tmpLen ++;
		}
		$this->_queryNum = $tmpLen;
	}

	/**
	 + -------------------------------------------------------
	 * 通过关联数组返回数据的辅助方法
	 + -------------------------------------------------------
	 * @access private
	 * @param void
	 * @return void
	 + -------------------------------------------------------
	 */
	private function _getAllByAssocArr() {
		$tmpLen = 0;
		$this->_queryResult = array();
		while($arr = mysql_fetch_array($this->_query,MYSQL_ASSOC)) {
			$this->_queryResult[] = $arr;
			$tmpLen ++;
		}
		$this->_queryNum = $tmpLen;
	}

	/**
	 + -------------------------------------------------------
	 * 通过默认方法返回数据的辅助方法
	 + -------------------------------------------------------
	 * @access private
	 * @param void
	 * @return void
	 + -------------------------------------------------------
	 */
	private function _getAllByDefault() {
		$tmpLen = 0;
		$this->_queryResult = new Tp_DbArrayObjectList();
		while($row = mysql_fetch_row($this->_query)) {
			$this->_queryResult->insertObject(new Tp_DbArrayObject($row));
			$tmpLen ++;
		}
		$this->_queryNum = $tmpLen;
	}


	/**
	 + -------------------------------------------------------
	 * 获取execute()执行后的结果
	 + -------------------------------------------------------
	 * @access private
	 * @param int $operation 要执行的操作
	 * @return mixed
	 + -------------------------------------------------------
	 */
	private function _getAll($operation = null) {
		if(is_int($operation)) {
			if(Tp_DbConstants::FETCH_OBJ === $operation) {
				$this->_getAllByObj();
			} else if(Tp_DbConstants::FETCH_ASSOC_ARR === $operation) {
				$this->_getAllByAssocArr();
			} else {
				//默认方式
				$this->_getAllByDefault();
			}
		} else if(is_array($operation)) {
			if(in_array(Tp_DbConstants::FETCH_OBJ,$operation)) {
				$this->_getAllByObj();
			} else if(in_array(Tp_DbConstants::FETCH_ASSOC_ARR,$operation)) {
				$this->_getAllByAssocArr();
			} else {
				//默认方式
				$this->_getAllByDefault();
			}
		} else {
			$this->_getAllByDefault();
		}
		return $this->_queryResult;
	}

	/**
	 + --------------------------------------------------------
	 * 释放查询
	 + --------------------------------------------------------
	 * @access protected
	 * @param void
	 * @return void
	 + --------------------------------------------------------
	 */
	protected function _free() {
		mysql_free_result($this->_query);
		$this->_query = null;
	}

	/**
	 + -------------------------------------------------------
	 * 开始事务
	 + -------------------------------------------------------
	 * @access public
	 * @param void
	 * @return void
	 + -------------------------------------------------------
	 */
	public function beginTrans() {
		$this->connect();
		mysql_query('START TRANSACTION',$this->_db);
	}

	/**
	 + -------------------------------------------------------
	 * 事务commit
	 + -------------------------------------------------------
	 * @access public
	 * @param void
	 * @return void
	 + -------------------------------------------------------
	 */
	public function commit() {
		mysql_query('COMMIT',$this->_db);
	}

	/**
	 + -------------------------------------------------------
	 * 事务rollback
	 + -------------------------------------------------------
	 * @access public
	 * @param void
	 * @return void
	 + -------------------------------------------------------
	 */
	public function rollback() {
		mysql_query('ROLL BACK',$this->_db);
	}

	/**
	 + -------------------------------------------------------
	 * 得到最后插入的ID
	 + -------------------------------------------------------
	 * @access public
	 * @param void
	 * @return int
	 + -------------------------------------------------------
	 */
	public function getLastInsertId() {
		return mysql_insert_id();
	}
}