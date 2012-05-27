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
 * Toper PDO驱动文件
 * 由于PHP5开始支持PDO，而且PHP6默认安装PDO
 * 把其他扩展放在pear,并且使用本框架PHP版本最低为5.0.0
 * 而且PDO提供了统一的接口
 * 所以，本框架默认使用pdo驱动作为数据库中间层
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

class Tp_PdoDriver extends Tp_DbBase {

	protected $_preStatement = false;
	//执行了prepare()后的参数

	public function __construct($config = array()) {
		parent::__construct($config);
		if(!class_exists('PDO')) {
			throw new Exception("系统不支持PDO");
		}
	}

	public function __destruct() {
		$this->close();
	}

	public function __call($method,$args) {
		throw new Exception("在PDO中不存在该方法");
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
		//PDO连接数据库
		if(!$this->_db) {
			//没有连接到数据库
			if('DSN' === $this->_config['dbCnnType']) {
				$tmpDsn = $this->_config['dsn'];
			} else {
				switch(strtolower($this->_config['type'])) {
					//目前ORD方式只支持mysql
					case "mysql":
						$tmpDsn = $this->_config['type'].":dbname=".$this->_config['name'].";host=".$this->_config['host'];
						break;
					default:
						$tmpDsn = ''; //出错
						break;
				}
			}
			try {
				if('mysql' === $this->_config['type']) {
					$this->_db = new PDO($tmpDsn,$this->_config['user'],$this->_config['pwd'],array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES '".$this->_config['encoding']."';"));
					$this->_db->setAttribute(PDO::ATTR_CASE,PDO::CASE_NATURAL);
				} else {
					$this->_db = new PDO($tmpDsn,$this->_config['user'],$this->_config['pwd']);
					$this->_db->setAttribute(PDO::ATTR_CASE,PDO::CASE_NATURAL);
				}

			} catch(PDOException $e) {
				echo "pdo连接失败,错误信息为:".$e->getMessage();
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
		$this->_db = null;
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
		//如果$sql为空，那么执行prepare的语句
		$this->_startCal();
		if(null === $this->_db) {
			$this->connect();
		}
		if(!$sql) {
			$this->_log("没有SQL",Tp_DbConstants::ERROR);
			return false;
		}
		if(false !== $this->_preStatement) {
			$this->_free();
		}
		$this->_preStatement = $this->_db->prepare($sql);
		$result = $this->_preStatement->execute();
		if($result) {
			$this->_log("执行了execute()函数,SQL为:".$sql);
		} else {
			$errorInfo = $this->_preStatement->errorInfo();
			$this->_log("执行了execute()函数,SQL为:".$sql.",错误代码为:".$errorInfo[0].",错误信息为:".$errorInfo[2],Tp_DbConstants::ERROR);
		}
		$this->_getAll($operation);
		$this->_count ++;
		$this->_queryStr = $sql;
		$this->_exeSql[] = $sql;
		return $result;
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
		while($obj = $this->_preStatement->fetch(PDO::FETCH_OBJ)) {
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
		$this->_queryResult = $this->_preStatement->fetchAll(PDO::FETCH_ASSOC);
		$this->_queryNum = count($this->_queryResult);
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
		while($arr = $this->_preStatement->fetch(PDO::FETCH_NUM)) {
			$this->_queryResult->insertObject(new Tp_DbArrayObject($arr));
			$tmpLen ++;
		}
		$this->_queryNum = $tmpLen;
	}

	/**
	 + -------------------------------------------------------
	 * 获取execute()执行后的结果(辅助)
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
	 * 释放preStatement
	 + --------------------------------------------------------
	 * @access protected
	 * @param void
	 * @return void
	 + --------------------------------------------------------
	 */
	protected function _free() {
		$this->_preStatement = false;
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
		$this->_db->beginTransaction();
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
		$this->_db->commit();
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
		$this->_db->rollback();
	}
	/**
	 + -------------------------------------------------------
	 * 得到最后插入的ID，需要数据库支持
	 + -------------------------------------------------------
	 * @access public
	 * @param void
	 * @return int
	 + -------------------------------------------------------
	 */
	public function getLastInsertId() {
		//暂不写完
		switch($this->_dbType) {
			case "mysql":
				return $this->_db->lastInsertId();
			default:
				return $this->_db->lastInsertId();
		}
	}
}