<?php
define('APP_PATH',dirname(__FILE__).'/../../../../..');
define('TP_PATH',dirname(__FILE__).'/../../..');
define('MODULES_PATH',dirname(__FILE__).'/../../../../../Modules');
require_once(dirname(__FILE__).'/../../../Common/function.php');
$config = include(dirname(__FILE__).'/../../../Common/config.php');
C($config);
class ModelTest extends Tp_Model {

	public function test() {
		$this->setDebug(true);
		$this->field(array('a'=>'A','b'=>'B'))
			->where(array('A' => array('gte',2,'OR'),'B'=>array('gte',2,'OR')))
			->table(array('test','test3'))
			->select();
		$this->fetch("select * from test where a >2");
		$this->table('test')
			->insert(array(
				'a'=>'A',
				'b'=>'B'
			));
			$this->table('test')
					->where(array('a'=>array('gte',3)))
					->update(
						array(
							'a'=>'A'
						)
					);
			$this->table('test')
						->where(array('a'=>array('gte',4),'b'=>array('gte',5)))
						->delete();
		$this->showDebug();
	}
}
$test = new ModelTest();
$test->test();
?>