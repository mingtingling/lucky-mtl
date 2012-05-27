<?php
/**
 * @author:mingtingling
 * @date:2012-3-9
 * aim:测试Common.config
 */
 define('APP_PATH',dirname(__FILE__).'/../../../../..');
 define('TP_PATH',dirname(__FILE__).'/../../..');
 define('MODULES_PATH',dirname(__FILE__).'/../../../../../Modules');
include(dirname(__FILE__).'/../../../Common/function.php');
Tp_Debug::start();
sleep(2);
Tp_Debug::counterStart();
sleep(2);
Tp_Debug::counterReset();
Tp_Debug::counterEnd();
sleep(2);
Tp_Debug::end();
?>