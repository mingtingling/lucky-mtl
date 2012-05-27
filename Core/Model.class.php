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
 * Model的基类
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */

class Tp_Model extends Tp {
	protected $_options = array();
	//查询条件
	protected $_dbType = 'mysql';
	//数据库类型
	protected $_db = null;
	protected $_tablePrefix = "";
	//表前缀
	protected $_config = array();
	
	/**
	+ --------------------------------------------------------
	* 构造函数
	+ --------------------------------------------------------
	* @access public
	* @param mixed $config 配置信息，支持字符串(DSN)和数组(ORD)
	* @param mixed $relation 所关联的项
	* @return void
	+ --------------------------------------------------------
	*/
	function __construct($config = array()) {
		foreach(C('db') as $key=>$val) {
			if('type' === $key) {
				$this->_dbType = $val;
			} else if('prefix' === $key) {
				$this->_tablePrefix = $val;
			} else {
				$this->_config[$key] = $val;
			}		
		}
		if(is_string($config)) {
			//如果是字符串，默认为DSN
			$this->_config['dsn'] = $config;
			$this->_config['dbCnnType'] = 'DSN';
		} else if(is_array($config)) {
			foreach($config as $key => $val) {
				if('type' === $key) {
					$this->_dbType = $val;
				} else if('prefix' === $key) {
					$this->_tablePrefix = $val;
				} else {
					$this->_config[$key] = $val;
				}
			}
		} else {
			throw new Tp_CommonException(Tp_CommonException::INCORRECT_VAR_TYPE);
		}
		$this->_db = Tp_Model::driverFactory(
			('ORD' === $this->_config['dbCnnType'])?
			array(
				'driver' => $this->_config['driver'],
				'type' => $this->_dbType,
				'host' => $this->_config['host'],
				'user' => $this->_config['user'],
				'pwd' => $this->_config['pwd'],
				'name' => $this->_config['name'],
				'encoding' => $this->_config['encoding'],
				'dbCnnType' => 'ORD'
			):
			array
			(
				'dbCnnType' => 'DSN',
				'driver' => $this->_config['driver'],
				'user' => $this->_config['user'],
				'pwd' => $this->_config['pwd'],
				'dsn' => $this->_config['dsn'],
				'type' => $this->_dbType,
				'encoding' => $this->_config['encoding']
			)
		);
	}


	/**
	 + --------------------------------------------
	 * 重载__call()
	 * 支持的方法有:
	 * field,table,where,order,limit,join,page,having,group,lock,distinct(select,insert,update,delete方法中使用) 代表查插删改的条件,详见:Tp_DbBase中的相应方法，比如:where，查询_parseWhere()的注释
	 * showDebug,setDebug,setLog,setDb,getAllInfo，beginTrans,commit,rollback,getLastInsertId(外部可以直接调用，如$this->showDebug()),具体实现及使用方法请见相应的驱动类，如Tp_PdoDrive
	 * fetch,比如之前已经查询了test这个项，那么可以调用$this->fetchTest()来取得它的值，详情请见相应的驱动类，如Tp_PdoDrive的getProperty方法
	 * set,即setter,支持比如$this->where(array('test'=>array('eq',2)))->table('test')->setA('test')将满足条件的记录的A这一项全部修改为test
	 * get,即getter,支持比如$this->where(array('test'=>array('eq',2)))->table('test')->getA()将满足条件的记录的A这一项全部查询出来
	 + --------------------------------------------
	 * @access public
	 * @param string $method 调用的方法名
	 * @param array $args 调用的参数
	 * @return mixed
	 + --------------------------------------------
	 */
	public function __call($method,$args) {
		if(in_array(strtolower($method),array(
			'field',
			'table',
			'where',
			'order',
			'limit',
			'join',
			'page',
			'having',
			'group',
			'lock',
			'distinct'
		),true)) {
			//第三个参数是检查参数类型
			//连贯操作实现
			$this->_options[strtolower($method)] = $args[0];
			return $this;
			//这里的关键在于返回对象本身
		} else if(in_array($method,array(
			//允许dbBase的一些public方法直接在DbTable及其子类中直接调用
			'showDebug',
			'setDebug',
			'setLog',
			'setDb',
			'getAllInfo'
		))){
			$this->_db->$method(isset($args[0]) ? $args[0] : "");
		} else if(in_array($method,array(
			//允许相应的驱动类的一些public方法直接在DbTable极其子类中直接调用
			'beginTrans',
			'commit',
			'rollback',
			'getLastInsertId'
		))) {
			$this->_db->$method();
		} else {
			$tmpStr = substr($method,0,3);
			$property = tp_lcfirst(substr($method,3));	
			if('get' === $tmpStr) {
				//getter
				return $this->_getter($property);
			} else if('set' === $tmpStr) {
				//setter
				if(!isset($args[0])) {
					throw new Tp_CommonException(Tp_CommonException::NO_PARAMETER);
				} else {
					return $this->_setter($property,$args[0]);
				}
				return $this->_getter($property);
			} else {
				tp_include(TP_PATH.'/Core/Exception/ClassException.class.php');
				throw new Tp_ClassException(Tp_ClassException::NONE_EXISTS_METHOD);
			}
		}
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
	protected function _getter($property) {}
	
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
	protected function _setter($property,$val) {}
	
	/**
	 + ---------------------------------------------
	 * select方法
	 * 支持:$this->where(array('test'=>array('eq',2)))->table('test')->select() 代表查询test表中满足test=2的记录
	 * 支持的方法请见：__call,具体使用方法请见:Tp_DbBase
	 * 比如您想查询where()方法的使用，那么请察看Tp_DbBase中_parseWhere()方法的注释
	 + ---------------------------------------------
	 * @access public
	 * @param int $operation 额外操作
	 * @return mixed
	 + ---------------------------------------------
	 */
	public function select($operation = null) {
		$tmp = $this->_db->select($this->_options,$operation);
		//清除options操作，防止下次干扰
		$this->_clearOptions();
		return $tmp;
	}

	/**
	 + ---------------------------------------------
	 * fetch方法
	 * 支持:$this->fetch("select * from test");
	 * 注意:不允许出现update,delete,insert ;这些单词或者字母
	 * 注意:如果出现上述单词或字母，则自动删除
	 * 它会直接取得所查找出来的数据
	 + ---------------------------------------------
	 * @access public
	 * @param string $sql
	 * @param int $operation
	 * @return mixed
	 + ---------------------------------------------
	 */
	public function fetch($sql,$operation = null) {
		if(empty($sql)) {
			$this->_db->setLog("您没有输入SQL语句",Tp_DbConstants::ERROR);
		}
		if(preg_match("/SELECT/i",$sql)) {
			$pattern = array(
						'/UPDATE/i',
						'/INSERT/i',
						'/DELETE/i',
						'/;/'
						);
			$replace = array(
						'',
						'',
						'',
						''
						);
			$sql = preg_replace($pattern,$replace,$sql);
			return $this->_db->select($sql,$operation);
		} else {
			$this->_db->setLog("您输入的SQL不是select语句",Tp_DbConstants::ERROR);
		}
	}

	/**
	 + ----------------------------------------------
	 * insert
	 * 支持:$this->where(array('test'=>array('eq',2)))->table('test')->insert(array('A'=>'testA','B'=>'testB'))
	 * 支持的方法请见：__call,具体使用方法请见:Tp_DbBase
	 * 不过有些方法对insert无效，比如group
	 + ----------------------------------------------
	 * @access public
	 * @param array $data
	 * @return bool
	 + ----------------------------------------------
	 */
	public function insert($data) {
		$tmp = $this->_db->insert($data,$this->_options);
		$this->_clearOptions();
		return $tmp;
	}

	/**
	 + ----------------------------------------------
	 * update
	 * 支持:$this->where(array('test'=>array('eq',2)))->table('test')->update(array('A'=>'testA','B'=>'testB'))
	 * 支持的方法请见：__call,具体使用方法请见:Tp_DbBase
	 * 不过有些方法对update无效，比如group
	 + ----------------------------------------------
	 * @access public
	 * @param array $data
	 * @return bool
	 + ----------------------------------------------
	 */
	public function update($data) {
		$tmp = $this->_db->update($data,$this->_options);
		$this->_clearOptions();
		return $tmp;
	}

	/**
	 + ----------------------------------------------
	 * delete
	 * 支持:$this->where(array('test'=>array('eq',2)))->table('test')->delete()
	 * 支持的方法请见：__call,具体使用方法请见:Tp_DbBase
	 * 不过有些方法对delete无效，比如group
	 + ----------------------------------------------
	 * @access public
	 * @param void
	 * @return bool
	 + ----------------------------------------------
	 */
	public function delete() {
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
		if(isset($this->_options['where'])) {
			$tmp = $this->_db->update($arr,$this->_options);
		} else {
			$tmp = $this->_db->insert($arr,$this->_options);
		}
		$this->_clearOptions();
		return $tmp;
	}

	/**
	 + ----------------------------------------------
	 * 清除options操作
	 + ----------------------------------------------
	 * @access protected
	 * @param void
	 * @return void
	 + ----------------------------------------------
	 */
	protected function _clearOptions() {
		$this->_options = array();
	}

	/**
	 + -----------------------------------------------
	 * 使用某一个表的表名的函数
	 * 假设表名为test,表前缀为web_,那么输入useTable('test')
	 * 返回web_test
	 * 不能直接使用web_test,这样不容易移植程序
	 + -----------------------------------------------
	 * @access public
	 * @param string $table 表名（不含前缀）
	 * @return string
	 + -----------------------------------------------
	 */
	public function useTable($table) {
		return $this->_tablePrefix.$table;
	}

	/**
	 + -----------------------------------------------
	 * 得到上次查询的所有记录
	 * 可以多次抓取
	 * 支持$this->getAll()
	 + -----------------------------------------------
	 * @access public
	 * @param void
	 * @return mixed
	 + -----------------------------------------------
	 */
	public function getAll() {
		return $this->_db->getAll();
	}

	/**
	 + -----------------------------------------------
	 * 获取execute()执行后结果的某些列,针对N条记录
	 * 可以多次抓取
	 * 支持:$this->getCols()，具体使用请见相应的驱动类，比如Tp_PdoDrive
	 + -----------------------------------------------
	 * @access public
	 * @param mixed $property 属性名 默认抓取所有的列
	 * @param int $num 抓取的条数 默认全部抓取
	 * @return mixed
	 + -----------------------------------------------
	 */
	public function getCols($property = Tp_DbConstants::GET_ALL_COLS,$num = Tp_DbConstants::GET_ALL_ROWS) {
		return $this->_db->getCols($property,$num);
	}

	/**
	 + --------------------------------------------------------
	 * 执行SQL语句,原生态的SQL执行(系统不进行任何的处理)
	 * 不建议用户使用此函数，因为安全性得不到保证
	 * 支持:$this->exec()
	 + --------------------------------------------------------
	 * @access public
	 * @param string $sql
	 * @param int $operation 执行的操作
	 * @return mixed
	 + --------------------------------------------------------
	 */
	public function exec($sql,$operation = null) {
		$isOk = $this->_db->exec($sql,$operation);
		if(Tp_DbConstants::AUTO_FETCH_DATA === $operation) {
			return $this->_db->getAll();
		} else {
			return $isOk;
		}
	}

	/**
	+ ------------------------------------------------------
	* 驱动层工厂
	+ ------------------------------------------------------
	* @access public
	* @static
	* @param array $config 配置信息 
	* @return object
	*/
	public static function driverFactory($config = array()) {
		$drive = 'Tp_'.ucfirst($config['driver']).'Driver';
		$driveFile = ucfirst($config['driver']).'Driver.class.php';
		tp_include(TP_PATH.'/Core/Db/Driver/'.$driveFile);
		return new $drive($config);
	}
}