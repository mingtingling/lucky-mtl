<?php
require_once(dirname(__FILE__).'/../../../../../Core/PHPUnit.class.php');
class PdoDriverTest extends Tp_PHPUnit {
	private $_pdoDriver = null;
	private $_table = null;
	private $_sql = null;
	function __construct() {
		parent::__construct();
		$config = include TP_PATH.'/config.php';
		$this->setConfig($config);
		import('Tp.Core.Db.Driver.PdoDriver');
		$this->_pdoDriver = new Tp_PdoDriver(C('db'));
		$this->_table = C('db=>prefix').'user';
		$this->_sql = "select * from $this->_table";
	}
	public function testExec() {
		$this->assertTrue($this->_pdoDriver->exec($this->_sql));
	}
	public function testExecute() {
		$this->_pdoDriver->execute($this->_sql,Tp_DbConstants::FETCH_OBJ);
		$rs = $this->_pdoDriver->getAll();
		while($rs->valid()) {
			$current = $rs->current();
			$this->assertObjectHasAttribute("test",$current);
			$rs->next();
		}
		$this->_pdoDriver->execute($this->_sql,Tp_DbConstants::FETCH_ASSOC_ARR);
		$arrs = $this->_pdoDriver->getAll();
		foreach($arrs as $arr) {
			$this->assertArrayHasKey("test",$arr);
		}
		$this->_pdoDriver->execute($this->_sql);
		$rs = $this->_pdoDriver->getAll();
		while($rs->valid()) {
			$current = $rs->current();
			$this->assertNotEquals($current->getInt(1),0);
			$rs->next();
		}
	}
}