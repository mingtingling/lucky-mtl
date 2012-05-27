<?php
require_once(dirname(__FILE__).'/../../../../../Core/PHPUnit.class.php');
class MysqlDriverTest extends Tp_PHPUnit {
	private $_mysqlDriver = null;
	private $_table = null;
	private $_sql = null;
	function __construct() {
		parent::__construct();
		$config = include TP_PATH.'/config.php';
		$this->setConfig($config);
		import('Tp.Core.Db.Driver.MysqlDriver');
		$this->_mysqlDriver = new Tp_MysqlDriver(C('db'));
		$this->_table = C('db=>prefix').'user';
		$this->_sql = "select * from $this->_table";
	}
	public function testExec() {
		$this->assertTrue($this->_mysqlDriver->exec($this->_sql));
	}
	public function testExecute() {
		$this->_mysqlDriver->execute($this->_sql,Tp_DbConstants::FETCH_OBJ);
		$rs = $this->_mysqlDriver->getAll();
		while($rs->valid()) {
			$current = $rs->current();
			$this->assertObjectHasAttribute("test",$current);
			$rs->next();
		}
		$this->_mysqlDriver->execute($this->_sql,Tp_DbConstants::FETCH_ASSOC_ARR);
		$arrs = $this->_mysqlDriver->getAll();
		foreach($arrs as $arr) {
			$this->assertArrayHasKey("test",$arr);
		}
		$this->_mysqlDriver->execute($this->_sql);
		$rs = $this->_mysqlDriver->getAll();
		while($rs->valid()) {
			$current = $rs->current();
			$this->assertNotEquals($current->getInt(1),0);
			$rs->next();
		}
	}
}