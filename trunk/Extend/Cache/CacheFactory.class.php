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
 * Toper 缓存的工厂
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Cache
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */

class Tp_CacheFactory extends Tp {
	
	const FILE = 1;
	const TABLE = 2;
	const MEM_CACHE = 3;
	
	/**
	+-----------------------------------------------------------
	* 缓存工厂
	* $cache = Tp_CacheFactory::factory(Tp_CacheFactory::FILE);
	* 或者
	* $cache = Tp_CacheFactory::factory(Tp_CacheFactory::TABLE);
	* 或者
	* $cache = Tp_CacheFactory::factory(Tp_CacheFactory::MEM_CACHE);
	* 支持:
	* $cache->set('test','A',2); 将test变量的值A存储，过期时间为2s
	* $cache->have('test'); 察看缓存中是否存在变量test
	* $cache->get('test') ;得到缓存中test变量的值
	* $cache->remove('test');将缓存中的test变量清除
	* $cache->clear();清除缓存中所有的变量
	* 由于各个缓存略有不同，详情请参照各个缓存辅助类
	+-----------------------------------------------------------
	* @access public
	* @static
	* @param int $type 连接的类型
	* @return object
	+-----------------------------------------------------------
	*/
	public static function factory($type = Tp_CacheFactory::FILE) {
		if(Tp_CacheFactory::FILE === $type) {
			tp_include(TP_PATH.'/Core/Cache/FileCache.class.php');
			return new Tp_FileCache();
		} else if(Tp_CacheFactory::TABLE === $type) {
			tp_include(TP_PATH.'/Core/Cache/TableCache.class.php');
			return new Tp_TableCache();
		} else if(Tp_CacheFactory::MEM_CACHE === $type) {
			tp_include(TP_PATH.'/Core/Cache/MemCache.class.php');
			return new Tp_MemCache();
		} else {
			tp_include(TP_PATH.'/Core/Exception/CommonException.class.php');
			throw new Tp_CommonException(Tp_CommonException::INCORRECT_VAR_TYPE);
		}
	}
}