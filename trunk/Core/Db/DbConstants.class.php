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
 * 数据库的常量
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Db
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */
 class Tp_DbConstants {
	 const GET_ALL_ROWS = 0;
	 //得到所有的记录集
	const NOTICE = 1;
	//在_log函数中需要记录notice使用
	const ERROR = 2;
	//在_log函数中需要记录error使用
	const AUTO_FETCH_DATA = 3;
	//自动获取数据
	const NO_PARAMETER = 4;
	//没有参数
	const INSERT_SQL = 5;
	//代表这个SQL是嵌入其他sql的
	const NO_DEAL_DATA = 6;
	//不处理查询的数据
	const FETCH_OBJ = 7;
	//通过对象方式抓取
	const FETCH_ASSOC_ARR = 8;
	//通过关联数组返回
	const FETCH_DEFAULT = 9;
	//通过默认方式抓取
	const GET_ALL_COLS = 10;
	//得到所有的列
}