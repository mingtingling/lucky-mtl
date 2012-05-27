<?php
/**
 * @author:zhaojianghua
 * @date:2012-3-9
 * aim:测试DbBase
 */

 define('APP_PATH',dirname(__FILE__).'/../../../../..');
 define('TP_PATH',dirname(__FILE__).'/../../..');
 define('MODULES_PATH',dirname(__FILE__).'/../../../../../Modules');
require_once(dirname(__FILE__).'/../../../Common/function.php');
$config = include(dirname(__FILE__).'/../../../Common/config.php');
C($config);

/*
*	construct()
*/
$dbtest = new Tp_DbBase(array("user" => "xiaoming","level" => "zero","state" =>"lazygay","autoFree" =>array("host" => "xiaozhang"),"name" => "goods"));

/*
*	setDb()
*/
$dbtest ->setDb(array("happ" => array("yes" => "happy"),"user" => "xiaoxiao","debug" => true));

/*
*	setLog()
*	setDebug()
*	showDebug()
*	getAllInfo()
*/
$dbtest ->setLog("Mingtingling is a <H1>SB!</H1>",Tp_DbBase::Error);
$dbtest -> setDebug(true);
//$dbtest ->showDebug();
$dbtest ->getAllInfo();

/*
*	select()
*/
P("");
$dbtest ->select(array(
	"distinct"=>true,
	'field'=>array('method1'=>'A','method2'=>'B'),
	"table"=>"test",
	"join"=>"",
	"where"=>"",
	"group"=>"",
	"having"=>"",
	"order"=>"",
	"limit"=>""
));

/*
*	delete()
*/
P("");
$dbtest ->delete(array("table" =>"test","where"=>"id='xiaozhang'"));

/*
*	update()
*/
P("");
$dbtest ->update(array("id" => "1","name" =>"xiaohua"),array("table" =>"test","where"=>"id='xiaozhang'"));

/*
*	insert()
*/

P("");
$dbtest ->insert(array("name" =>"xinling","sex" =>"male","age"=>"21"),array("table" =>"test","where"=>"id='xiaozhang'"));

P("OK!");

?>