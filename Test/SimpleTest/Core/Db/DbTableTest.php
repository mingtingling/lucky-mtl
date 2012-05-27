<?php
define('APP_PATH',dirname(__FILE__).'/../../../../..');
define('TP_PATH',dirname(__FILE__).'/../../..');
define('MODULES_PATH',dirname(__FILE__).'/../../../../../Modules');
require_once(dirname(__FILE__).'\..\..\..\Common\function.php');
$config = include(dirname(__FILE__).'\..\..\..\Common/config.php');
C($config);
require_once(dirname(__FILE__).'\..\..\..\Db\DbTable.class.php');
class DbTableTest extends Tp_DbTable{
/*	public function __construct() {
	
		parent::__construct(C('db'));
	}*/
	public function test(){
		$this->setDebug(true);
		$this->_initTableInfo();
		echo "tableName::".$this->getTableFullName()."  success<br>";
//		if($this->insert(array('id'=>"128",'name'=>"ling",'age'=>23,'grade'=>24))) echo "insert success<br>";
//		echo "ssss";
		if($this->where(array('id'=>array('eq',126)))->update(array("name"=>"lisdjfsd","age"=>26,"grade"=>29))) echo "update succes<br>";
		print_r($this->select());
		$this->_setter('name','lllllllll');
		echo "_getter::".$this->_getter('name')."<br>";
		$this->_initTableInfo();
		$this->_setOptionTable();
		$this->_setTable('test');
//		$this->delete();
		$this->showTableInfo();
		$this->showDebug();
	}
}
?>