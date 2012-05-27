<?php
define('APP_PATH',dirname(__FILE__).'/../../../../..');
define('TP_PATH',dirname(__FILE__).'/../../..');
define('MODULES_PATH',dirname(__FILE__).'/../../../../../Modules');
require_once(dirname(__FILE__).'/../../../Common/function.php');
$config = include(dirname(__FILE__).'/../../../Common/config.php');
C($config);
class PdoDriveTest extends Tp_PdoDrive {
	public function __construct() {
		parent::__construct(C('db'));
		$this->setDebug(true);
	}
	public function test() {
		$this->showDebug();
	}
}
$test = new PdoDriveTest();
$test->test();
?>
