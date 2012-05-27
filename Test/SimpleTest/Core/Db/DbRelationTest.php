<?php
require_once(dirname(__FILE__).'\..\..\..\Common\function.php');
$config = include(dirname(__FILE__).'\..\..\..\Common/config.php');
C($config);
require_once(dirname(__FILE__).'\..\..\..\Db\DbRelation.class.php');
class DbRelationTest extends Tp_DbRelation{
	public function test(){
		$this->setDebug(true);
		$this->table($this->useTable('relation'))->_setter('name','sssss');
		echo $this->table($this->useTable('relation'))->_getter('name');
		$this->table($this->useTable('relation'))->_setKey();
		$this->showDebug();
	}
}
?>