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
 * Toper的标签库，支持用户扩展
 * 如果您想扩展标签，那么需要在Tp_FrontController中的init()方法传入myTagLib参数
 * 如：$frontController->init(array('myTagLib'=>'MyTagLibHelper'))(MyTagLibHelper是您自定义的一个类，只需要继承至Tp_TagLib即可)(入口文件处)
 * 也支持直接写入配置:C('myTagLib','MyTagLibHelper')
 * 此处借鉴了thinkphp的taglib和java的jstl与taglib
 * 具体每个标签的使用，请见下面具体函数的注释
 * 比如要察看foreach标签使用方法，那么请查询_parseForeachStart()方法
 * 注意:本标签库非一般html，要求比较严苛，比如:不允许标签内使用单引号，必须使用双引号，如:<div class = "test"></div>
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage View
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */
class Tp_TagLib extends Tp {
	
	private $_dom = null; //文档树的资源标识
	private $_errorInfo = array(); //错误信息
	//标签库
	protected $_tags = array(
		'foreach' => array('attr' => 'from|item|key'),
		'if' => array('attr' => 'condition'),
		'elseif' => array('attr' => 'condition'),
		'else' => array('attr' => ''),
		'include' => array('attr' => 'file|type'),
		'print' => array('attr' => 'name|type'),
		'for' => array('attr' => 'start|end|loop'),
		'switch' => array('attr' => 'name'),
		'case' => array('attr' => 'name'),
		'default' => array('attr' => ''),
		'break' => array('attr' => ''),
		'continue' => array('attr' => ''),
		'while' => array('attr' => 'condition|loop|start'),
		'isset' => array('attr' => 'name'),
		'set' => array('attr' => 'name|value')
	);
	protected $_compExp = array(
		' neq ' => ' != ',
		' gte ' => ' >= ',
		' lte ' => ' <= ',
		' gt ' => ' > ',
		' lt ' => ' < ',
		' eq ' => ' == ',
		' and ' => ' && ',
		' or ' => ' || ',
		' ass ' => ' = '
	);
	protected $_tagCondition = ' neq | gte | lte | gt | lt | eq | and | or | ass ';
	//特殊的HTML标签，例如:<input />
	private $_specialHtmlTags = array(
		'input','br','img','hr'
	);
	
	/**
	+ ---------------------------------------------------
	* 解析函数
	+ ---------------------------------------------------
	* @access public
	* @param string $file
	* @return void
	+ ---------------------------------------------------
	*/
	public function parse($file) {
		$htmlContents = file_get_contents($file);
		//此处本来可以使用$dom->loadHTMLFile($file)来完成
		//但是因为xml格式较为严格，所以此处使用xml来解析
		$xmlContents = '<?xml version="1.0" encoding="utf-8"?><root>'.$htmlContents.'</root>';
		file_put_contents($file,$xmlContents);
		//将即将处理的文件变为xml文件
		try {
			$this->_dom = new DOMDocument();
			$this->_dom->load($file);
			$contents = $this->_parseTag($this->_dom->documentElement);
			if(C('view=>debug')) {
				foreach($this->_errorInfo as $errorStr) {
					echo "<br/><font color = 'red'>".$errorStr."</font>";
				}
			}
			return $contents;
		} catch(DOMException $e) {
			echo $e->getMessage();
		}		
	}
	
	/**
	+ ---------------------------------------------------
	* 解析Tag(递归调用)
	+ ---------------------------------------------------
	* @access private
	* @param object $element
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseTag($element) {
		$contents = "";
		if($element->hasChildNodes()) {
			foreach($element->childNodes as $childNode) {
				if(XML_TEXT_NODE === $childNode->nodeType) {
					if('' !== trim($childNode->nodeValue)) {
						//文本节点
						return $childNode->nodeValue;
					}
				} else {
					$tagName = $childNode->nodeName;
					$tagAttr = array();
					if($childNode->hasAttributes()) {
						//抓取这个节点的所有属性
						foreach($childNode->attributes as $attr) {
							$tagAttr[$attr->nodeName] = $attr->nodeValue;
						}
					}
					$contents .= $this->_parseTagStart($tagName,$tagAttr);
					$contents .= $this->_parseTag($childNode);
					$contents .= $this->_parseTagEnd($tagName);
				}
			}
		} else {
			//文本节点
			return $element->nodeValue;
		}
		return $contents;
	}

	/**
	+ ---------------------------------------------------
	* 解析Tag的开始标记
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param array $tagAttr
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseTagStart($tagName,$tagAttr = array()) {
		if($this->_isInTagLib($tagName)) {
			$method = '_parse'.(ucfirst($tagName).'Start');
			return $this->$method($tagName,$tagAttr);
			//调用相应的解析函数
		} else {
			//非标签库的标签
			if('#comment' === $tagName) {
				//注释
				$tag = '<!-- ';
			} else {
				$tag = '<'.$tagName;
				foreach($tagAttr as $attr=>$val) {
					$tag .= ' '.$attr.' = "'.$val.'"';
				}
				if(!$this->_isSpecialHtmlTag($tagName)) {
					$tag .= '>';
				} else {
					$tag .= '/>';
				}
			}
			return $tag;
		}
	}
	
	/**
	+ ---------------------------------------------------
	* 解析Tag的结束标记
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseTagEnd($tagName) {
		if($this->_isInTagLib($tagName)) {
			$method = '_parse'.(ucfirst($tagName).'End');
			if(!method_exists($this,$method)) {
				return $this->_parseUniversalTagEnd($tagName);
			} else {
				return $this->$method($tagName);
			}
		} else {
			//非标签库的标签
			if('#comment' === $tagName) {
				//注释
				return ' -->';
			} else {
				if(!$this->_isSpecialHtmlTag($tagName)) {
					return '</'.$tagName.'>';
				} else {
					return '';
				}
			}
		}
	}	

	/**
	+ ---------------------------------------------------
	* 解析通用的标签库的标签的结束符
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseUniversalTagEnd($tagName) {
		return '<?php end'.$tagName.'; endif; ?>';
	}
	
	/**
	+ ---------------------------------------------------
	* 这个标签是否是标签库的标签
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return bool
	+ ---------------------------------------------------
	*/
	private function _isInTagLib($tagName) {
		if(array_key_exists($tagName,$this->_tags)) {
			return true;
		}
		return false;
	}	

	/**
	+ ---------------------------------------------------
	* 这个标签是否是特殊的HTML标记
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return bool
	+ ---------------------------------------------------
	*/
	private function _isSpecialHtmlTag($tagName) {
		if(in_array($tagName,$this->_specialHtmlTags)) {
			return true;
		}
		return false;
	}
	
	/**
	+ ---------------------------------------------------
	* 判断这个标签的属性是否全部合法(即是否是taglib中定义的)
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param mixed $attrName
	* @return bool
	+ ---------------------------------------------------
	*/
	private function _isTagAttrLegal($tagName,$attrName) {
		$tagAttrs = explode('|',$this->_tags[$tagName]['attr']);
		if(is_array($attrName)) {
			//对某属性组判定
			foreach($attrName as $key => $val) {
				if(!in_array($key,$tagAttrs)) {
					$this->_log('标签'.$tagName.'中不存在属性'.$key);
					return false;
				}
			}
		} else {
			//对某个属性判定
			if(!in_array($attrName,$tagAttrs)) {
				$this->_log('标签'.$tagName.'中不存在属性'.$attrName);
				return false;
			}
		}
		return true;
	}
	
	/**
	+ ---------------------------------------------------
	* Log日志(只记录ERROR)
	+ ---------------------------------------------------
	* @access protected
	* @param string $log
	* @return void
	+ ---------------------------------------------------
	*/
	protected function _log($log) {
		$this->_errorInfo[] = $log;
	}
	
	/**
	 + ----------------------------------------------------------------
	 * 比较表达式的解析
	 * 可以解析string: gt 解析为>
	 * 可以解析array: array('gt','neq')解析为array('>','!=')
	 + ----------------------------------------------------------------
	 * @access protected
	 * @param mixed $exp
	 * @return string
	 + -----------------------------------------------------------------
	 */
	protected function _parseCompExp($exp) {
		if(is_string($exp)) {
			return str_replace(array_keys($this->_compExp),array_values($this->_compExp),$exp);
		}else if(is_array($exp)) {
			$tmpArr = array();
			foreach($exp as $val) {
				$tmpArr[] = str_replace(array_keys($this->_compExp),array_values($this->_compExp),$val);
			}
			return $tmpArr;
		} else {
			//不能解析，直接返回
			return $exp;
		}
	}	

	/**
	+ ---------------------------------------------------
	* 处理标签的condition
	* 将一些特殊字符串替换 eg:gte
	+ ---------------------------------------------------
	* @access private
	* @param string $condition
	* @return string
	+ ---------------------------------------------------
	*/
	protected function _parseTagCondition($condition) {
		if(preg_match_all(('/('.$this->_tagCondition.')/i'),$condition,$matchedArr)) {
			foreach($matchedArr[1] as $pattern) {
				$replace = $this->_parseCompExp($pattern);
				//此处不需要区分大小写，所以使用str_ireplace
				$condition = str_ireplace($pattern,$replace,$condition);
			}
		}
		return $condition;
	}

	/**
	+ ---------------------------------------------------
	* 下面的代码是对taglib的标签的解析
	+ ---------------------------------------------------
	*/

	/**
	+ ---------------------------------------------------
	* 解析foreach开始标签
	* 支持:<foreach from = "$data" item = "list" >
	* 支持<foreach from = "$data" key = "key" item = "list">
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param array $tagAttr
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseForeachStart($tagName,$tagAttr = array()) {
		if(!array_key_exists('from',$tagAttr) || !array_key_exists('item',$tagAttr)) {
			$this->_log('foreach标签必须含有from和item两个属性');
			return '';
		}
		if(!$this->_isTagAttrLegal($tagName,$tagAttr)) {
			//如果存在不合法的标签，直接返回空串
			return '';
		}
		$tag = '<?php if(isset('.$tagAttr['from'].')): if(is_array('.$tagAttr['from'].')): ';
		$tag .= 'foreach('.$tagAttr['from'].' as ';
		if(array_key_exists('key',$tagAttr)) {
			$tag .= '$'.$tagAttr['key'].'=>';
		}
		$tag .= '$'.$tagAttr['item'].'): ?>';
		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析foreach结束标签
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseForeachEnd($tagName) {
		$tag = '<?php endforeach; endif; endif; ?>';
		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析if开始标签
	* 支持:<if condition = "$data gt 3" >
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param array $tagAttr
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseIfStart($tagName,$tagAttr = array()) {
		if(!array_key_exists('condition',$tagAttr)) {
			$this->_log('if标签必须含有condition属性');
			return '';
		}
		if(!$this->_isTagAttrLegal($tagName,$tagAttr)) {
			//如果存在不合法的标签，直接返回空串
			return '';
		}
		$tagAttr['condition'] = $this->_parseTagCondition($tagAttr['condition']);
		$tag = '<?php if('.$tagAttr['condition'].'): ?>';
		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析if结束标签
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseIfEnd($tagName) {
		$tag = '<?php endif; ?>';
		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析elseif开始标签
	* 支持:<elseif condition = "$data gt 3" />
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param array $tagAttr
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseElseifStart($tagName,$tagAttr = array()) {
		if(!array_key_exists('condition',$tagAttr)) {
			$this->_log('elseif标签必须含有condition属性');
			return '';
		}
		if(!$this->_isTagAttrLegal($tagName,$tagAttr)) {
			//如果存在不合法的标签，直接返回空串
			return '';
		}
		$tagAttr['condition'] = $this->_parseTagCondition($tagAttr['condition']);
		$tag = '<?php elseif('.$tagAttr['condition'].'): ?>';
		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析elseif结束标签
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseElseifEnd($tagName) {
		return '';
	}
	
	/**
	+ ---------------------------------------------------
	* 解析else开始标签
	* 支持:<else/>
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param array $tagAttr
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseElseStart($tagName,$tagAttr = array()) {
		if($tagAttr) {
			$this->_log('else标签不应该含有属性');
			return '';
		}
		$tag = '<?php else: ?>';
		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析else结束标签
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseElseEnd($tagName) {
		return '';
	}

	/**
	+ ---------------------------------------------------
	* 解析print开始标签
	* type：
	* 有var 代表 输出php变量
	* 有str 代表 输出字符串
	* 支持:<print name = "$data" type = "var" />
	* 支持:<print name = "测试" type = "str"/>
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param array $tagAttr
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parsePrintStart($tagName,$tagAttr = array()) {
		if(!array_key_exists('name',$tagAttr)) {
			$this->_log('print标签必须含有name属性');
			return '';
		}
		if(!$this->_isTagAttrLegal($tagName,$tagAttr)) {
			//如果存在不合法的标签，直接返回空串
			return '';
		}
		if(array_key_exists('type',$tagAttr)) {
			if('str' === $tagAttr['type']) {
				$tag = $tagAttr['name'];
			} else if('var' === $tagAttr['type']) {
				$tag = '<?php if(isset('.$tagAttr['name'].')): echo('.$tagAttr['name'].'); endif; ?>';
			} else {
				$tag = '<?php echo('.$tagAttr['name'].'); ?>';
			}
		} else {
			$tag = '<?php echo('.$tagAttr['name'].'); ?>';
		}
		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析print结束标签
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parsePrintEnd($tagName) {
		return '';
	}
	
	/**
	+ ---------------------------------------------------
	* 解析include开始标签
	* type：
	* 有css 代表 导入css文件
	* 有js 代表 导入js文件
	* 有file 代表 导入View文件
	* 支持:<include file = "Public.test" type = "file" />
	* 支持:<include file = "test" type = "css" />
	* 支持:<include file = "test" type = "js" />
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param array $tagAttr
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseIncludeStart($tagName,$tagAttr = array()) {
		if(!array_key_exists('file',$tagAttr)) {
			$this->_log('include标签必须含有file属性');
			return '';
		}
		if(!$this->_isTagAttrLegal($tagName,$tagAttr)) {
			//如果存在不合法的标签，直接返回空串
			return '';
		}
		if(array_key_exists('type',$tagAttr)) {
			if('js' === $tagAttr['type']) {
				if(0 === strpos($tagAttr['file'],'$')) {
					//代表使用php变量来赋值
					$tag = '<script language = "javascript" src = "__TP__JS__/<?php echo (str_replace(".","/",'.$tagAttr['file'].')); ?>.js"></script>';
				} else {
					$js = str_replace('.','/',$tagAttr['file']);
					$tag = '<script language = "javascript" src = "__TP__JS__/'.$js.'.js"></script>';
				}
			} else if('css' === $tagAttr['type']) {
				if(0 === strpos($tagAttr['file'],'$')) {
					//代表使用php变量来赋值
					$tag = '<link href = "__TP__CSS__/<?php echo (str_replace(".","/",'.$tagAttr['file'].')); ?>.css" rel="stylesheet" type="text/css"/>';
				} else {
					$css = str_replace('.','/',$tagAttr['file']);
					$tag = '<link href = "__TP__CSS__/'.$css.'.css" rel="stylesheet" type="text/css"/>';
				}
			} else {
				return '';
			}
		} else {
			return '';
		}
		//注:此处理论上不会再存在include file = "Public.index" type = "file" />这种，因为之前已经将这种标签解析完毕
		//所以，此处如果是这种类型，那么直接返回空串
		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析include结束标签
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseIncludeEnd($tagName) {
		return '';
	}

	/**
	+ ---------------------------------------------------
	* 解析for开始标签
	* 支持:<for start = "$i = 2" end = "$i lt 5" loop = "$i ++" >
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param array $tagAttr
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseForStart($tagName,$tagAttr = array()) {
		if(!array_key_exists('start',$tagAttr) || !array_key_exists('end',$tagAttr) || !array_key_exists('loop',$tagAttr)) {
			$this->_log('for标签必须含有start,end,loop属性');
			return '';
		}
		if(!$this->_isTagAttrLegal($tagName,$tagAttr)) {
			//如果存在不合法的标签，直接返回空串
			return '';
		}
		$tagAttr['start'] = $this->_parseTagCondition($tagAttr['start']);
		$tagAttr['end'] = $this->_parseTagCondition($tagAttr['end']);
		$tagAttr['loop'] = $this->_parseTagCondition($tagAttr['loop']);
		$tagAttr['start'] = ($tagAttr['start'])?('('.$tagAttr['start'].')'):'';
		$tagAttr['end'] = ($tagAttr['end'])?('('.$tagAttr['end'].')'):'';
		$tagAttr['loop'] = ($tagAttr['loop'])?('('.$tagAttr['loop'].')'):'';
		$tag = '<?php for('.$tagAttr['start'].';'.$tagAttr['end'].';'.$tagAttr['loop'].'): ?>';
		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析for结束标签
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseForEnd($tagName) {
		return '<?php endfor; ?>';
	}

	/**
	+ ---------------------------------------------------
	* 解析switch开始标签
	* 支持:<switch name = "$test" >
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param array $tagAttr
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseSwitchStart($tagName,$tagAttr = array()) {
		if(!array_key_exists('name',$tagAttr)) {
			$this->_log('swtich标签必须含有name属性');
			return '';
		}
		if(!$this->_isTagAttrLegal($tagName,$tagAttr)) {
			//如果存在不合法的标签，直接返回空串
			return '';
		}
		$tag = '<?php switch('.$tagAttr['name'].'): ?>';
		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析switch结束标签
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseSwitchEnd($tagName) {
		return '<?php endswitch; ?>';
	}

	/**
	+ ---------------------------------------------------
	* 解析case开始标签
	* 支持:<case name = "3"/>
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param array $tagAttr
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseCaseStart($tagName,$tagAttr = array()) {
		if(!array_key_exists('name',$tagAttr)) {
			$this->_log('case标签必须含有name属性');
			return '';
		}
		if(!$this->_isTagAttrLegal($tagName,$tagAttr)) {
			//如果存在不合法的标签，直接返回空串
			return '';
		}
		$tag = '<?php case "'.$tagAttr['name'].'": ?>';
		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析case结束标签
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseCaseEnd($tagName) {
		return '';
	}

	/**
	+ ---------------------------------------------------
	* 解析default开始标签
	* 支持:<default />
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param array $tagAttr
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseDefaultStart($tagName,$tagAttr = array()) {
		if(!$this->_isTagAttrLegal($tagName,$tagAttr)) {
			//如果存在不合法的标签，直接返回空串
			return '';
		}
		$tag = '<?php default: ?>';
		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析default结束标签
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseDefaultEnd($tagName) {
		return '';
	}

	/**
	+ ---------------------------------------------------
	* 解析break开始标签
	* 支持:<break />
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param array $tagAttr
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseBreakStart($tagName,$tagAttr = array()) {
		if(!$this->_isTagAttrLegal($tagName,$tagAttr)) {
			//如果存在不合法的标签，直接返回空串
			return '';
		}
		$tag = '<?php break; ?>';
		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析break结束标签
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseBreakEnd($tagName) {
		return '';
	}

	/**
	+ ---------------------------------------------------
	* 解析continue开始标签
	* 支持:<continue />
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param array $tagAttr
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseContinueStart($tagName,$tagAttr = array()) {
		if(!$this->_isTagAttrLegal($tagName,$tagAttr)) {
			//如果存在不合法的标签，直接返回空串
			return '';
		}
		$tag = '<?php continue; ?>';
		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析continue结束标签
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseContinueEnd($tagName) {
		return '';
	}

	/**
	+ ---------------------------------------------------
	* 解析while开始标签
	* 支持:<while condition = "$i gt 2" loop = "$i --" start = "$i = 5">
	* 支持:<while condition = "$i gt 2"  loop = "$i ++" >
	* 注意: 此处的loop 是在while之后马上执行
	* 比如:$i = 0 loop = "$i ++"
	* 那么这个标签之后马上$i = 1
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param array $tagAttr
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseWhileStart($tagName,$tagAttr = array()) {
		if(!array_key_exists('condition',$tagAttr) || !array_key_exists('loop',$tagAttr)) {
			$this->_log('while标签必须含有condition,loop属性');
			return '';
		}
		if(!$this->_isTagAttrLegal($tagName,$tagAttr)) {
			//如果存在不合法的标签，直接返回空串
			return '';
		}
		$tag = '';
		if(array_key_exists('start',$tagAttr)) {
			$tagAttr['start'] = $this->_parseTagCondition($tagAttr['start']);
			$tag .= '<?php '.$tagAttr['start'].'; ?>';
		}
		$tagAttr['condition'] = $this->_parseTagCondition($tagAttr['condition']);
		$tagAttr['loop'] = $this->_parseTagCondition($tagAttr['loop']);
		$tag .= '<?php while('.$tagAttr['condition'].'): '.$tagAttr['loop'].'; ?>';
		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析while结束标签
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseWhileEnd($tagName) {
		return '<?php endwhile; ?>';
	}

	/**
	+ ---------------------------------------------------
	* 解析isset开始标签
	* 支持:<isset name = "$test"><else /></isset>
	* 支持:<isset name = "$test"></isset>
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param array $tagAttr
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseIssetStart($tagName,$tagAttr = array()) {
		if(!array_key_exists('name',$tagAttr)) {
			$this->_log('isset标签必须含有name属性');
			return '';
		}
		if(!$this->_isTagAttrLegal($tagName,$tagAttr)) {
			//如果存在不合法的标签，直接返回空串
			return '';
		}
		$tag = '<?php if(isset('.$tagAttr['name'].')): ?>';
		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析isset结束标签
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseIssetEnd($tagName) {
		return '<?php endif; ?>';
	}

	/**
	+ ---------------------------------------------------
	* 解析set开始标签
	* 支持:<set name = "$test" value = "2" />
	* 支持:<set name = "$test" value = "++" />
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @param array $tagAttr
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseSetStart($tagName,$tagAttr = array()) {
		if(!array_key_exists('name',$tagAttr) || !array_key_exists('value',$tagAttr)) {
			$this->_log('set标签必须含有name属性和value属性');
			return '';
		}
		if(!$this->_isTagAttrLegal($tagName,$tagAttr)) {
			//如果存在不合法的标签，直接返回空串
			return '';
		}
		if(preg_match('/^\d+$/',$tagAttr['value'])) {
			//全部为数字
			$tag = '<?php '.$tagAttr['name'].'='.$tagAttr['value'].'; ?>';
		} else if(preg_match('/^\+{2}|-{2}$/',$tagAttr['value'])) {
			// ++ 或者 --
			$tag = '<?php '.$tagAttr['name'].$tagAttr['value'].'; ?>';
		} else {
			$tag = '<?php '.$tagAttr['name'].'='.$tagAttr['name'].$tagAttr['value'].'; ?>';
		}

		return $tag;
	}
	
	/**
	+ ---------------------------------------------------
	* 解析set结束标签
	+ ---------------------------------------------------
	* @access private
	* @param string $tagName
	* @return string
	+ ---------------------------------------------------
	*/
	private function _parseSetEnd($tagName) {
		return '';
	}
}