<?php
// +------------------------------------------------
// | Version:Toper 1.1
// +------------------------------------------------
// | Author:zhaojianghua  309927063
// +------------------------------------------------
// | Copyright www.qingyueit.com
// +------------------------------------------------

/**
 +--------------------------------------------------
 * Toper Tp_XmlConfig 处理Xml配置信息,此类为辅助类
 * 外部不应该直接调用，而是调用Tp_ConfigFactory
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage Config
 * @author zhaojianghua
 * @rewiew mingtingling
 * @version 1.1
 + --------------------------------------------------
 */
class Tp_XmlConfig extends Tp {

	/**
	 + --------------------------------------------
	 * get array from DOMDocument
	 + --------------------------------------------
	 * @access private
	 * @static
	 * @param $node
	 * @return array
	 + --------------------------------------------
	 */
	private static function _getArray($node){
		$array = array();
		if($node->hasChildNodes()) {
			foreach($node->childNodes as $childNode) {
				if(XML_TEXT_NODE === $childNode->nodeType) {
					if('' !== trim($childNode->nodeValue)) {
						return $childNode->nodeValue;	
					}
				} else {
					$array[$childNode->nodeName] = self::_getArray($childNode);
					if($childNode->hasAttributes()) {
						$valType = $childNode->getAttribute('type');
						if('int' === $valType) {
							$array[$childNode->nodeName] = intval($array[$childNode->nodeName]);
						} else if('float' === $valType) {
							$array[$childNode->nodeName] = floatval($array[$childNode->nodeName]);
						} else if('bool' === $valType) {
							$array[$childNode->nodeName] = (($array[$childNode->nodeName] == '') || (strtolower($array[$childNode->nodeName]) == 'false'))?false:true;
						} else {
						}
					}
				}
			}
		} else {
			return $node->nodeValue;
		}
		return $array;
	}
	
	/**
	 + --------------------------------------------
	 * parse xml files
	 * 注意:本框架对数据类型要求比较严格
	 * xml解析出来的默认为字符串
	 * 如果需要解析成int,float,bool
	 * 那么需要使用如下格式:
	 * <test type = "int">222</test>
	 * <test type = "bool">1</test>
	 * <test type = "float">2.2</test>
	 + --------------------------------------------
	 * @access public
	 * @static
	 * @param string $path
	 * @return array
	 + --------------------------------------------
	 */
	public static function parse($path) {
		$dom = new DOMDocument();
		try {
			$dom->load($path);
			return self::_getArray($dom->documentElement);
		} catch(DOMException $e) {
			echo $e->getMessage();
		}
	}
}