<?php
// +------------------------------------------------
// | Version:Toper 1.1
// +------------------------------------------------
// | Author:mingtingling 717547858@qq.com
// +------------------------------------------------
// | Copyright www.qingyueit.com
// +------------------------------------------------
// | 感谢thinkphp如此优雅的代码,让我这个初学者受益良多
// | 正因为这些代码，我才能写出Tp_DbBase这个数据库底层
// +-------------------------------------------------

/**
 +---------------------------------------------------
 * Toper Db的基类
 +---------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Db
 * @author mingtingling
 * @version 1.1
 + --------------------------------------------------
 */
tp_include(TP_PATH.'/Core/Db/DbConstants.class.php');

class Tp_DbBase extends Tp {

	//如果自动抓取数据，那么就调用这个函数
	protected $_autoConnect = true;
	//是否自动连接数据库
	protected $_autoFree = false;
	//是否自动释放查询的结果
	protected $_debug = false;
	//是否处于调试状态
	protected $_connected = false;
	//是否处于连接状态
	protected $_log = array();
	//Log日志
	protected $_config = array();
	//配置信息
	protected $_data = array();
	//从数据库抓取的数据
	protected $_queryResult = null;
	//当前抓取的数据或者该数据的对象
	protected $_queryNum = 0;
	//当前抓取的数据的条数
	protected $_queryStr = "";
	//当前执行的SQL
	protected $_exeSql = array();
	//已经执行的SQL
	protected $_exeTime = "";
	//执行SQL的时候的时间
	protected $_db = null;
	//数据库连接参数
	protected $_count = 0;
	//已经执行的SQL数量
	protected $_compExp = array(
		'neq' => '!=',
		'gte' => '>=',
		'lte' => '<=',
		'notlike' => 'NOT LIKE',
		'gt' => '>',
		'lt' => '<',
		'eq' => '=',
		'like' => 'LIKE'
	);
	//比较的表达式
	protected $_sql = "SELECT %DISTINCT% %FIELD% FROM %TABLE% %JOIN% %WHERE% %GROUP% %HAVING% %ORDER% %LIMIT%";
	//要执行的SQL

	/**
	 + ----------------------------------------------------------
	 * 构造函数
	 + ----------------------------------------------------------
	 * @param mixed $config 配置信息
	 + -----------------------------------------------------------
	 */

	public function __construct($config = array()) {
		foreach($config as $key => $val) {
			switch($key) {
				case 'debug':
					$this->_debug = $val;
					break;
				case 'autoConnect':
					$this->_autoConnect = $val;
					break;
				case 'autoFree':
					$this->_autoFree = $val;
					break;
				default:
					$this->_config[$key] = $val;
					break;
			}
		}
		if(true === $this->_autoConnect) {
			//如果是自动连接，那么则连接到数据库
			$this->connect();
		}
	}

	public function __destruct() {}

	/**
	 + -----------------------------------------------------------------
	 * 连接数据库
	 + -----------------------------------------------------------------
	 * @access public
	 * @param void
	 * @return void
	 + -----------------------------------------------------------------
	 */
	public function connect() {}

	/**
	 + -----------------------------------------------------------------
	 * 断开数据库
	 + -----------------------------------------------------------------
	 * @access public
	 * @param void
	 * @return void
	 + -----------------------------------------------------------------
	 */
	public function close() {}

	/**
	 + -----------------------------------------------------------------
	 * 重新设置配置信息，并重连数据库
	 + -----------------------------------------------------------------
	 * @access public
	 * @param $config mixed 支持DSN和普通方式连接
	 * @return void
	 + -----------------------------------------------------------------
	 */
	public function setDb($config) {
		if(is_array($config)) {
			foreach($config as $key => $val) {
				if('autoConnect' === $key) {
					$this->_autoConnect = $val;
				} else if('autoFree' === $key) {
					$this->_autoFree = $val;
				} else if('debug' === $key) {
					$this->_debug = $val;
				} else {
					$this->_config[$key] = $val;
				}
			}
		} else if(is_string($config)) {
			$this->_config['dsn'] = $config;
		} else {
			tp_include(TP_PATH.'/Core/Exception/CommonException.class.php');
			throw new Tp_CommonException(Tp_CommonException::INCORRECT_VAR_TYPE);
		}
		$this->_db = null;
		$this->_log = array();
		$this->_data = array();
		$this->_exeSql = array();
		if($this->_autoConnect) {
			$this->connect();
		}
	}


	/**
	 + -------------------------------------------------------------
	 * 设置Log
	 + -------------------------------------------------------------
	 * @access protected
	 * @param string $log 操作日志
	 * @param int $type 日志文件类型 有Tp_DbConstants::NOTICE和Tp_DbConstants::ERROR两种
	 * @return void
	 + -------------------------------------------------------------
	 */
	protected function _log($log,$type = Tp_DbConstants::NOTICE) {
		if($this->_debug) {
			$time = $this->_calExeTime();
			if(Tp_DbConstants::NOTICE === $type) {
				$this->_log[] = "[NOTICE]:执行时间:{$time}秒,日志:{$log}";
			} else if(Tp_DbConstants::ERROR === $type) {
				$this->_log[] = "<font color = 'red'>[ERROR]:执行时间:{$time}秒,日志:{$log}</font>";
			} else {
				//以后还可扩展
				return ;
			}
		}
	}

	/**
	 + -------------------------------------------------------------
	 * 方便dbTable类记录日志信息
	 + -------------------------------------------------------------
	 * @access public
	 * @param string $log 操作日志
	 * @param int $type 日志文件类型 日志文件类型 有Tp_DbConstants::NOTICE和Tp_DbConstants::ERROR两种
	 * @return void
	 + --------------------------------------------------------------
	 */
	public function setLog($log,$type = Tp_DbConstants::NOTICE) {
		if(Tp_DbConstants::NOTICE === $type) {
			$this->_log[] = "[NOTICE]:日志:{$log}";
		} else if(Tp_DbConstants::ERROR === $type) {
			$this->_log[] = "<font color = 'red'>[ERROR]:日志:{$log}</font>";
		} else {
			return ;
		}
	}

	/**
	 + ----------------------------------------------------------------
	 * 计算执行的时间
	 + ----------------------------------------------------------------
	 * @access protected
	 * @param void
	 * @reurn void
	 + ----------------------------------------------------------------
	 */
	protected function _calExeTime() {
		if($this->_debug) {
			return microtime() - $this->_exeTime;
		}
	}

	/**
	 + -----------------------------------------------------------------
	 * 开始计算执行时间
	 + -----------------------------------------------------------------
	 * @access protected
	 * @param void
	 * @return void
	 + -----------------------------------------------------------------
	 */
	protected function _startCal() {
		if($this->_debug) {
			$this->_exeTime = microtime();
		}
	}

	/**
	 + -----------------------------------------------------------------
	 * 设置Debug
	 + -----------------------------------------------------------------
	 * @access public
	 * @param bool $debug
	 * @return void
	 + -----------------------------------------------------------------
	 */
	public function setDebug($debug = false) {
		$this->_debug = $debug;
	}

	/**
	 + -----------------------------------------------------------------
	 * 显示Debug信息
	 + -----------------------------------------------------------------
	 * @access public
	 * @param void
	 * @return void
	 + -----------------------------------------------------------------
	 */
	public function showDebug() {
		if($this->_debug) {
			foreach($this->_log as $tmp) {
				echo "<br/>".$tmp;
			}
		} else {
			echo "<br/>您没有开启Debug，请开启Debug后再试!";
		}
	}
	/**
	 + -----------------------------------------------------------------
	 * 显示所有的信息
	 + -----------------------------------------------------------------
	 * @access public
	 * @param void
	 * @return void
	 + -----------------------------------------------------------------
	 */
	public function getAllInfo() {
		if($this->_debug) {
			echo "<br/>数据库连接参数为:";
			tp_echo($this->_config);
			if($this->_dsn) {
				echo "<br/>DSN信息为".$this->_dsn;
			}
			echo "<br/>已执行的SQL为:";
			$count = 0;
			foreach($this->_exeSql as $tmp) {
				$count ++;
				echo "<br/>".$count.":".$tmp;
			}
			echo "<br/>日志信息为:";
			$count = 0;
			foreach($this->_log as $tmp) {
				$count ++;
				echo "<br/>".$count.":".$tmp;
			}
		} else {
			echo "<br/>您没有开启Debug，请开启Debug后再试!";
		}
	}

	/**
	 + -----------------------------------------------------------------
	 * 执行SQL
	 + -----------------------------------------------------------------
	 * @access public
	 * @param string $sql
	 * @param int $operation
	 * @return mixed
	 + -----------------------------------------------------------------
	 */
	public function execute($sql = '',$operation = null) {}

	/**
	 + ------------------------------------------------------------------
	 * 取得execute()执行的结果
	 + ------------------------------------------------------------------
	 * @access public
	 * @param void
	 * @return mixed
	 + ------------------------------------------------------------------
	 */
	public function getAll() {
		return $this->_queryResult;
	}

	/**
	 + -----------------------------------------------------------------
	 * 执行Select
	 + -----------------------------------------------------------------
	 * @access public
	 * @param mixed $option 操作
	 * @param mixed $operation 是否通过这个函数获得额外的操作
	 * @return mixed
	 + -----------------------------------------------------------------
	 */
	public function select($options = array(),$operation = false) {
		$this->_startCal();
		if(is_array($options)) {
			if(isset($options['page'])) {
				$options['limit'] = isset($options['limit']) ? $options['limit'] : '';
				if(is_array($options['page'])) {
					//传递了当前页码和每页显示的条数
					$page = $options['page'][0];
					if(isset($options['page'][1])) {
						$num = $options['page'][1];
					} else {
						$num = is_numeric($options['limit']) ? $options['limit'] : C('db=>recordPerPage');
					}
					$offset = $num * (int)($page - 1);
					$options['limit'] = $offset.",".$num;
				} else {
					//数字
					$options['page'] = intval($options['page']);
					$page = ($options['page'] > 0) ? $options['page'] : 1;
					$num = is_numeric($options['limit']) ? $options['limit'] : C('db=>recordPerPage');
					$offset = $num * (int)($page - 1);
					$options['limit'] = $offset.",".$num;
				}
			}
			$sql = str_replace(
				//按条件替换
				array(
					'%DISTINCT%',
					'%FIELD%',
					'%TABLE%',
					'%JOIN%',
					'%WHERE%',
					'%GROUP%',
					'%HAVING%',
					'%ORDER%',
					'%LIMIT%'
					),
				array(
					$this->_parseDistinct(isset($options['distinct']) ? $options['distinct'] : false),
					$this->_parseField(isset($options['field']) ? $options['field'] : '*'),
					$this->_parseTable($options['table']),
					$this->_parseJoin(isset($options['join']) ? $options['join'] : ''),
					$this->_parseWhere(isset($options['where']) ? $options['where'] : ''),
					$this->_parseGroup(isset($options['group']) ? $options['group'] : ''),
					$this->_parseHaving(isset($options['having']) ? $options['having'] : ''),
					$this->_parseOrder(isset($options['order']) ? $options['order'] : ''),
					$this->_parseLimit(isset($options['limit']) ? $options['limit'] : '')
					),
				$this->_sql
			);
			$this->_log("执行select解析结束,解析SQL为:".$sql);
			return $this->_dealSelectOperation($sql,$operation);
		} else if(is_string($options)) {
			//直接sql,不需要解析
			return $this->_dealSelectOperation($options,$operation);
		} else {
			$this->_log("执行select解析失败，传递的参数不是有效参数",Tp_DbConstants::ERROR);
		}
	}

	/**
	 + -----------------------------------------------------------------
	 * 处理select中的operation
	 + -----------------------------------------------------------------
	 * @access private
	 * @param string $sql
	 * @param mixed $operation
	 * @return mixed
	 + -----------------------------------------------------------------
	 */
	private function _dealSelectOperation($sql,$operation) {
		if(is_int($operation)) {
			if(Tp_DbConstants::INSERT_SQL === $operation) {
				//代表此时不是执行SQL，而是嵌套SQL
				return $sql;
			}
			$status = $this->execute($sql,$operation);
			if(Tp_DbConstants::AUTO_FETCH_DATA === $operation) {
				if($status) {
					return $this->getAll();
				} else {
					return false;
				}
			} else {
				return ;
			}
		} else if(is_array($operation)) {
			if(in_array(Tp_DbConstants::INSERT_SQL,$operation)) {
				//代表此时不是执行SQL，而是嵌套SQL
				return $sql;
			}
			$status = $this->execute($sql,$operation);
			if(in_array(Tp_DbConstants::AUTO_FETCH_DATA,$operation)) {
				if($status) {
					return $this->getAll();
				} else {
					return false;
				}
			} else {
				return ;
			}
		} else {
			return $this->execute($sql,$operation);
		}
	}

	/**
	 + -----------------------------------------------------------------
	 * 处理lock,默认为关闭(悲观锁)
	 + -----------------------------------------------------------------
	 * @access protected
	 * @param bool $lock
	 * @return string
	 + -----------------------------------------------------------------
	 */
	protected function _parseLock($lock = false) {
		//为数据库加上悲观锁
		if(!$lock) return '';
		if('oracle' === $this->_dbType) {
			return ' FOR UPDATE NOWAIT ';
		} else {
			return ' FOR UPDATE';
		}
	}

	/**
	 + -----------------------------------------------------------------
	 * 处理distinct
	 + -----------------------------------------------------------------
	 * @access protected
	 * @param bool $distinct
	 * @return string
	 + -----------------------------------------------------------------
	 */
	protected function _parseDistinct($distinct = true) {
		return (true === $distinct) ? " DISTINCT " : "";
	}

	/**
	 + -----------------------------------------------------------------
	 * 处理field
	 * 支持string:空代表所有，即*
	 * 支持string:method1 A,method2 B
	 * 支持array:array('method1','method2')
	 * 支持array('method1'=>'A','method2'=>'B')
	 + -----------------------------------------------------------------
	 * @access protected
	 * @param mixed $field
	 * @return string
	 + -----------------------------------------------------------------
	 */
	protected function _parseField($field) {
		if(is_array($field)) {
			$tmpArr = array();
			foreach($field as $key => $val) {
				if(is_numeric($key)) {
					//将数组解析成以，分隔的形式
					$tmpArr[] = $val;
				} else {
					//添加别名,如test as A
					$tmpArr[] = $key.' AS '.$val;
				}
			}
			return implode(',',$tmpArr);
		} else if((is_string($field)) && !empty($field)) {
			//只查询一个属性或者通过test1,test2,test3这种已经处理的方式输入的
			return $field;
		} else {
			//为空代表查找所有
			return '*';
		}
	}

	/**
	 + -----------------------------------------------------------------
	 * 处理table
	 * 支持string:test1 A,test2 B这种
	 * 支持array:array('test1','test2')
	 * 支持array:array('test1'=>'A','test2'=>'B')
	 + -----------------------------------------------------------------
	 * @access protected
	 * @param mixed $table
	 * @return string
	 + -----------------------------------------------------------------
	 */
	protected function _parseTable($table) {
		if(is_array($table)) {
			$tmpArr = array();
			foreach($table as $key => $val) {
				if(is_numeric($key)) {
					$tmpArr[] = $val;
				} else {
					//解析别名，如table1 A,table2 B这种形式
					$tmpArr[] = $key.' '.$val;
				}
			}
			return implode(',',$tmpArr);
		} else {
			return $table;
		}
	}

	/**
	 + -----------------------------------------------------------------
	 * 处理join
	 * 支持string:left join A test on
	 * 支持string:A on ...(被解析为left join A on ... )
	 * 支持array:array('join A on ...','left join B on ...')
	 * 支持array:array('test'=>array('A','on ...'),'test2'=>array('B','on ...'))
	 * 支持array:array('test'=>array('A','join','on ...'))
	 + -----------------------------------------------------------------
	 * @access protected
	 * @param mixed $join
	 * @return string
	 + -----------------------------------------------------------------
	 */
	protected function _parseJoin($join) {
		if(empty($join)) {
			return "";
		}
		if(is_array($join)) {
			$tmpStr = "";
			foreach($join as $key => $val) {
				if(is_numeric($key)) {
					if(false === stripos($val,'JOIN')) {
						//没有JOIN，那么调用left join，即MYSQL可以执行的这种形式
						//stripos不区分大小写来找到第一个匹配的位置
						//因为它返回可能为正数,0,false,为了区别0与false使用===
						$tmpStr .= " LEFT JOIN ".$val;
					} else {
						$tmpStr .= " ".$val;
					}
				} else {
					//有别名,解析别名
					if(false === stripos($val[1],'JOIN')) {
						$tmpStr .= " LEFT JOIN ".$key." ".$val[0]." ".$val[1];
					} else {
						$tmpStr .= " ".$key." ".$val[0]." ".$val[1]." ".$val[2];
					}
				}
			}
			return $tmpStr;
		} else {
			if(false === stripos($join,'JOIN')) {
				//没有JOIN，调用left join
				return " LEFT JOIN ".$join;
			} else {
				return " ".$join;
			}
		}
	}


	/**
	 + ----------------------------------------------------------------
	 * 比较表达式的解析
	 * 可以解析string: gt 解析为>
	 * 可以解析array: array('gt','neq')解析为array('>','!=')
	 + ----------------------------------------------------------------
	 * @access protected
	 * @param mixed $exp
	 * @return string
	 + -----------------------------------------------------------------
	 */
	protected function _parseCompExp($exp) {
		if(is_string($exp)) {
			return str_replace(array_keys($this->_compExp),array_values($this->_compExp),$exp);
		}else if(is_array($exp)) {
			$tmpArr = array();
			foreach($exp as $val) {
				$tmpArr[] = str_replace(array_keys($this->_compExp),array_values($this->_compExp),$val);
			}
			return $tmpArr;
		} else {
			//不能解析，直接返回
			return $exp;
		}
	}

	/**
	 + -----------------------------------------------------------------
	 * 处理where
	 * 注意:数组的最后一维不能带有and or 这种符号
	 * 如果为数组,每一维每个元素的意义为:
	 * $method=>array($command,$val,$con,$quote)
	 * $method代表要操作的属性列
	 * $command代表gt,eq,exp等操作
	 * $val为它的取值，如:$command=gt,$val=5
	 * $con为它与前一维元素的连接关系，如and
	 * 支持 空
	 * 支持 string: A.a = '1' and A.b < 3
	 * 支持 array: array('metho1'=>array('gte','2'))
	 * 支持 array: array('method1'=>array('exp',''))
	 + -----------------------------------------------------------------
	 * @access protected
	 * @param mixed $where
	 * @return string
	 + -----------------------------------------------------------------
	 */
	protected function _parseWhere($where) {
		if(empty($where)) {
			return "";
		}
		$whereStr = " WHERE "; //存储Where语句的字符串
		if(is_string($where)) {
			//直接字符串表示
			return " WHERE ".$where;
		} else if(is_array($where)) {
			$count = count($where);
			$tmpCount = 0;
			foreach($where as $key => $val) {
				$needQuote = isset($val[3]) ? ((false === $val[3]) ? false : true) : true;
				//需要添加引号吗，默认为需要
				if(preg_match('/^(eq|neq|gt|gte|lt|lte|notlike|like)$/i',$val[0])) {
					//比较运算符
					$command = $this->_parseCompExp($val[0]);
					if($needQuote) {
						$whereStr .= " ".$key." ".$command."'".$val[1]."'";
					} else {
						$whereStr .= " ".$key." ".$command.$val[1];
					}
				} else if(preg_match("/IN/i",$val[0])) {
					//IN
					$tmpStr = "";
					if(is_array($val[1])) {
						$tmpStr = implode(',',$val[1]);
					} else {
						$tmpStr = $val[1];
					}
					$whereStr .= " ".$key." IN (".$tmpStr.")";
				} else if(preg_match("/BETWEEN/i",$val[0])) {
					//Between
					$tmpData = is_string($val[1]) ? explode(',',$val[1]) : $val[1];
					//如果是字符串，那么通过，变成数组，如果是数组，不改变
					if($needQuote) {
						$whereStr .= " ".$key." BETWEEN '".$tmpData[0]."' AND '".$tmpData[1]."'";
					} else {
						$whereStr .= " ".$key." BETWEEN ".$tmpData[0]." AND ".$tmpData[1];
					}
				} else {
				}
				if($tmpCount < $count -1) {
					if(isset($val[2]) && $val[2]) {
						//存在连接操作符
						$whereStr .= " ".$val[2];
					} else {
						$whereStr .= " "."AND ";
					}
				}
				$tmpCount ++;
			}
			return $whereStr;
		} else {
			//出错,暂时只支持字符串和数组
			return "";
		}
	}

	/**
	 + -----------------------------------------------------------------
	 * 处理group
	 + -----------------------------------------------------------------
	 * @access protected
	 * @param mixed $group
	 * @return string
	 + -----------------------------------------------------------------
	 */
	protected function _parseGroup($group) {
		return empty($group) ? "" : "GROUP BY ".$group;
	}

	/**
	 + -----------------------------------------------------------------
	 * 处理having
	 + -----------------------------------------------------------------
	 * @access protected
	 * @param mixed $having
	 * @return string
	 + -----------------------------------------------------------------
	 */
	protected function _parseHaving($having) {
		return empty($having) ? "" : " HAVING ".$having;
	}

	/**
	 + -----------------------------------------------------------------
	 * 处理order
	 * 支持 string "testField"
	 * 支持 array array('testField','testField2'=>'asc')
	 + -----------------------------------------------------------------
	 * @access protected
	 * @param mixed $order
	 * @return string
	 + -----------------------------------------------------------------
	 */
	protected function _parseOrder($order) {
		if(empty($order)) {
			return "";
		}
		$tmpStr = " ORDER BY ";
		if(is_array($order)) {
			foreach($order as $key => $val) {
				if(is_numeric($key)) {
					$tmpStr .= $val;
				} else {
					$tmpStr .= " ".$key." ".$val;
				}
			}
		} else if(is_string($order)) {
			$tmpStr .= $order;
		} else {
			return "";
		}
		return $tmpStr;
	}

	/**
	 + -----------------------------------------------------------------
	 * 处理Limit
	 + -----------------------------------------------------------------
	 * @access protected
	 * @param mixed $limit
	 * @return string
	 + -----------------------------------------------------------------
	 */
	protected function _parseLimit($limit) {
		if(empty($limit)) {
			return "";
		}
		if(is_array($limit)) {
			return " LIMIT ".$limit[0]." , ".$limit[1];
		} else if(is_string($limit)) {
			@list($offset,$num) = explode(',',$limit);
			if($num) {
				return "LIMIT ".$offset.",".$num." ";
			} else {
				return " LIMIT 0,".$limit;
			}
		} else if(is_int($limit)){
			return "LIMIT 0,".$limit;
		} else {
			return "";
		}
	}

	/**
	 + ---------------------------------------------------------------------
	 * 处理set
	 + ---------------------------------------------------------------------
	 * @access proected
	 * @param array $set
	 * @return string
	 + ---------------------------------------------------------------------
	 */
	protected function _parseSet($set) {
		if(is_array($set)) {
			$tmpStr = " SET ";
			$count = count($set);
			$tmpCount = 0;
			foreach($set as $key => $val) {
				if($tmpCount < $count - 1) {
					$tmpStr .= (" ".$key."='".$val."' ,");
				} else {
					$tmpStr .= (" ".$key."='".$val."'");
				}
				$tmpCount ++;
			}
			return $tmpStr;
		} else {
			return "";
		}
	}

	/**
	 + ----------------------------------------------------------------------
	 * 处理insert的数据
	 + ----------------------------------------------------------------------
	 * @access protected
	 * @param array $data
	 * return string
	 + ----------------------------------------------------------------------
	 */
	protected function _parseInsert($data) {
		if(is_array($data)) {
			$tmpKeys = array();
			$tmpVals = array();
			foreach($data as $key => $val) {
				$tmpKeys[] = $key;
				$tmpVals[] = "'".$val."'";
			}
			$tmpStr = " ( ".implode(',',$tmpKeys).") VALUES ( ".implode(',',$tmpVals).")";
			return $tmpStr;
		} else {
			return "";
		}
	}


	/**
	 + -------------------------------------------------------------------
	 * delete
	 + -------------------------------------------------------------------
	 * @access public
	 * @param mixed $option
	 * @return bool
	 + -------------------------------------------------------------------
	 */
	public function delete($option = array()) {
		$this->_startCal();
		$sql = "DELETE FROM ";
		if(is_string($option)) {
			//比较弱智的一项
			$sql .= $option;
		} else if(is_array($option)) {
			$sql.= $this->_parseTable($option['table'])
				. $this->_parseWhere(isset($option['where']) ? $option['where'] : '')
				. $this->_parseLock(isset($option['lock']) ? $option['lock'] : false);
		} else {
			$this->_log("执行delete解析失败,传递参数非法",Tp_DbConstants::ERROR);
			return false;
		}
		$this->_log("执行delete解析完成,解析后的SQL为:".$sql);
		return $this->execute($sql);
	}

	/**
	 + -----------------------------------------------------------------------
	 * update
	 + -----------------------------------------------------------------------
	 * @access public
	 * @param array $data
	 * @param mixed $option
	 * @return bool
	 + -----------------------------------------------------------------------
	 */
	public function update($data,$option = array()) {
		$this->_startCal();
		$sql = "UPDATE "
			.$this->_parseTable($option['table'])
			.$this->_parseSet($data)
			.$this->_parseWhere(isset($option['where']) ? $option['where'] : "")
			.$this->_parseLock(isset($option['lock']) ? $option['lock'] : false);
		$this->_log("执行update解析完成,解析后的SQL为:".$sql);
		return $this->execute($sql);
	}

	/**
	 + -------------------------------------------------------------------------
	 * insert
	 + -------------------------------------------------------------------------
	 * @access public
	 * @param array $data
	 * @param array $option
	 * @return bool
	 + -------------------------------------------------------------------------
	 */
	public function insert($data,$option = array()) {
		$this->_startCal();
		$sql = "INSERT INTO "
			.$this->_parseTable($option['table'])
			.$this->_parseInsert($data)
			.$this->_parseLock(isset($option['lock']) ? $option['lock'] : false);
		$this->_log("执行insert解析完成,解析后的SQl为:".$sql);
		return $this->execute($sql);
	}

	/**
	 + -------------------------------------------------------
	 * 获取execute()执行后结果的某一列,针对N条记录 可以多次抓取
	 * 注意:只有当数据以数组返回时此方法才有效
	 + -------------------------------------------------------
	 * @access public
	 * @param mixed $property 属性名 默认获得所有的列
	 * @param int $num 抓取的条数 默认为全部抓取
	 * @return mixed 属性值
	 + -------------------------------------------------------
	 */
	public function getCols($property = Tp_DbConstants::GET_ALL_COLS,$num = Tp_DbConstants::GET_ALL_ROWS) {
		if(!is_int($num) || $num < 0) {
			tp_include(TP_PATH.'/Core/Exception/CommonException.class.php');
			throw new Tp_CommonException(Tp_CommonException::RANGE_ERROR);
		}
		$tmpArr = array();
		if(is_array($this->_queryResult)) {
			$count = 0;
			$num = (Tp_DbConstants::GET_ALL_ROWS === $num ) ? $this->_queryNum : $num;
			foreach($this->_queryResult as $tmp) {
				$count ++;
				if(is_string($property)) {
					if(isset($tmp[$property])) {
						$tmpArr[] = $tmp[$property];
					} else {
						tp_include(TP_PATH.'/Core/Exception/DbException.class.php');
						throw new Tp_DbException(Tp_DbException::NOT_FOUNDED_PROPERTY);
					}
				} else if(is_array($property)) {
					$tmpProArr = array();
					foreach($property as $tmpPro) {
						if(isset($tmp[$tmpPro])) {
							$tmpProArr[$tmpPro] = $tmp[$tmpPro];
						} else {
							tp_include(TP_PATH.'/Core/Exception/DbException.class.php');
							throw new Tp_DbException(Tp_DbException::NOT_FOUNDED_PROPERTY);
						}
					}
					$tmpArr[] = $tmpProArr;
				} else if(is_int($property) && (Tp_DbConstants::GET_ALL_COLS === $property)){
					$tmpArr[] = $tmp;
				} else {
					tp_include(TP_PATH.'/Core/Exception/CommonException.class.php');
					throw new Tp_CommonException(Tp_CommonException::INCORRECT_VAR_TYPE);
				}
				if($count >= $num) {
					break;
				}
			}
			return $tmpArr;
		} else {
			tp_include(TP_PATH.'/Core/Exception/CommonException.class.php');
			throw new Tp_CommonException(Tp_CommonException::INCORRECT_VAR_TYPE);
		}
	}
}