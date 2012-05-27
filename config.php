<?php
// +------------------------------------------------
// | Version:Toper 1.1
// +------------------------------------------------
// | Author:mingtingling 717547858@qq.com
// +------------------------------------------------
// | Copyright www.qingyueit.com
// +------------------------------------------------

/**
 +--------------------------------------------------
 * Toper 默认配置信息
 +--------------------------------------------------
 * @category Toper
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */
return array(
	'appDebug' => true, //调试开关
	'timeZone' => 'PRC', //时区
	'sessionAutoStart' => true, //SESSION是否自动开启
	'autoloadRegister' => false, //是否自动注册autoload
	'errorReporting' => -1, //显示的错误级别
	//0 不显示任何错误
	//-1 显示所有PHP错误
	//E_ERROR | E_WARNING | E_PARSE 单个运行错误
	//E_ALL ^ E_NOTICE 显示所有错误除了notice
	//更多参数可参考php手册的error_reporting函数
	//路由信息
	'url' => array(
		'type' => 1,
		//路由的种类，默认为第一种，如http://localhost/index.php/moduleName/ControllerName/ActionName/
		'rewrite' => true,
		//是否开启重定向，如果开启，需要编写.htaccess,如:http://localhost/moduleName/ControllerName/ActionName
		'group' => false,
		//组是否关闭
		'groupDepth' => 3,
		//组的最大深度
		'division' => '/',
		//URL分隔符，默认为/
		'protocol' => 'http',
		//默认的协议,如http
		'defaultGroup' => 'Test=>Test2=>Test3',
		//默认组
		'defaultModule' => 'Main',
		//默认模型
		'defaultController' => 'Index',
		//默认控制器
		'defaultAction' => 'test',
		//默认动作
		'error404Action' => 'error404'
		//出现404时调用的action
	),
	'db' => array(
		'recordPerPage' => 10,
		//每页显示的记录条数
		'type' => 'mysql',
		//数据库类型
		'driver' => 'mysql',
		//数据库驱动
		'dbCnnType' => 'ORD',
		//默认的数据库连接方式，有DSN方式和普通连接(ORD)方式
		'host' => 'localhost',
		//服务器地址
		'user' => 'root',
		//数据库登录用户名
		'pwd' => '921010',
		//数据库登录用户名
		'name' => 'luckySwan',
		//数据库名
		'port' => '3306',
		//端口号
		'prefix' => 'sw_',
		//表前缀
		'autoFree' => true,
		//是否不保存数据库查询信息
		'autoConnect' => true,
		//自动连接数据库
		'encoding' => 'utf8',
		//设置编码
		'debug' => true,
		//数据库调试是否打开
		'dsn' => "mysql:dbname=chaba;host=localhost",
		//DNS信息
		'cacheTable' => '~Cache'
		//缓存表
	),
	'cache' => array(
	//缓存信息
		'viewCacheOn' => false,
		//视图缓存是否开启
		'importFilesCacheOn' => false,
		//如果开启，那么对于系统每次都要导入的文件将会生成缓存
		'path' => '/~Cache'
		//缓存存放路径
	),
	'view' => array(
	//视图信息
		'type' => 'tp',
		//载入的视图类型，默认为本框架的视图，存在tp,smarty类型
		'debug' => true,
		//是否打开视图的调试开关，如果打开调试,则如果视图文件有错误，会显示错误
		'defaultSuffix' => 'html',
		//下面的只有type=tp的时候才有效
		//默认视图文件的后缀名
		'leftDelimiter' => '<<',
		//模板匹配符
		'rightDelimiter' => '>>',

		'smartyPath' => '/Smarty'
		//存放smarty的目录
	),
	'cookie' => array(
	//cookie信息的默认配置
		'prefix' => 'toperCookie_',
		//Cookie前缀
		'encode' => true,
		//是否对cookie加密
		'expire' => 1200000000000,
		//过期时间
		'path' => '',
		//可以使用的路径
		'domain' => ''
		//可以使用的域名
	),
	'session' => array(
		'prefix' => 'toperSession_',
		//Session前缀,
		'expire' => 0,
		//过期时间,0代表使用系统默认的过期时间
		'encode' => true
		//是否加密
	),
	'encode' => array(
	//加密信息
		'key' => 'test'
		//密钥
	)
);