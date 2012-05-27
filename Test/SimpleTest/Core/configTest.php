<?php
/**
 * @author:mingtingling
 * @date:2012-3-9
 * aim:测试Common.config
 */
 define('TP_PATH',dirname(__FILE__).'/../../..');
 define('MODULES_PATH',dirname(__FILE__).'/../../../../../Modules');
$config = include(dirname(__FILE__).'/../../../Common/config.php');
var_dump($config);