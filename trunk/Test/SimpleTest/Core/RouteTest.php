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
include(dirname(__FILE__).'/../../../Core/App.class.php');
$config = include(dirname(__FILE__).'/../../../Common/config.php');
C($config);

/*
 * test run
 */
Tp_Route::run();

?>