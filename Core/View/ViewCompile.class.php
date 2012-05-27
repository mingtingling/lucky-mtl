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
 * View的编译类(辅助类，用户透明)
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage View
 * @author mingtingling
 * @version 1.1
 +--------------------------------------------------
 */
 tp_include(TP_PATH.'/Core/Helper/File.class.php');
class Tp_ViewCompile extends Tp {

	private $_tags = array(); //标志的记录
	private $_errorInfo = array(); //错误信息

	/**
	 + -------------------------------------------------------------
	 * 构造函数
	 + -------------------------------------------------------------
	 * @access public
	 * @param void
	 + -------------------------------------------------------------
	 */
	public function __construct() {}
	
	/**
	 + -------------------------------------------------------------
	 * 模板编译
	 + -------------------------------------------------------------
	 * @access public
	 * @param string $contents 需要编译的内容
	 * @param string $file 编译的文件
	 * @return string
	 + -------------------------------------------------------------
	 */
	public function compile($contents,$file = null) {
		$this->_searchTags($contents,$file);
		if(C('view=>debug')) {
			//如果调试打开，显示错误信息
			if($this->_errorInfo) {
				foreach($this->_errorInfo as $tmpArr) {
					echo "<br/>"."<font color = 'red'>在文件"
						.$tmpArr['file']."的第".$tmpArr['lineNumber']."行出错了，出错信息为:"
						.$tmpArr['info']."</font>";
				}
				return '';
			}
			$haveError = false;
			foreach($this->_tags as $tagName => $tagVal) {
				if(0 !== $tagVal) {
					echo "<font color = 'red'>不匹配的标签=>".$tagName."</font>";
					$haveError = true;
				}
			}
			unset($this->_tags);
			if(true === $haveError) {
				return '';
			}
		}
		$contents = $this->_mergeFiles($contents);
		//$contents = $this->_stripNote($contents);
		$contents = $this->_replaceSpecialWords($contents);
		if(!file_exists(APP_PATH.C('cache=>path').'/~Tmp')) {
			//如果~Tmp目录不存在，那么建立它
			Tp_File::mkdir(APP_PATH.C('cache=>path'),'~Tmp/');
		}
		$tmpViewPathPrefixLen = strlen(MODULES_PATH.'/Views/');
		$tmpFilePath = APP_PATH.C('cache=>path').'/~Tmp/'.str_replace('/','_',substr($file,$tmpViewPathPrefixLen,(strrpos($file,C('view=>defaultSuffix'))-$tmpViewPathPrefixLen-1)));
		file_put_contents($tmpFilePath,$contents);
		if(null === C('myTagLib')) {
			tp_include(TP_PATH.'/Core/View/TagLib.class.php');
			$tagLib = new Tp_TagLib();
		} else {
			//支持用户自定义标签
			$class = C('myTagLib');
			$tagLib = new $class();
		}
		$contents = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		//加入DOCTYPE 头
		$contents .= $tagLib->parse($tmpFilePath);
		$contents = $this->_parseConstants($contents);
		$contents = $this->_parseArray($contents);
		$contents = $this->_parseVal($contents);
		$contents = $this->_parseHtmlCharacterEntity($contents);
		$contents = $this->_resumeSpecialWords($contents);
		unlink($tmpFilePath); //删除临时文件
		$contents = $this->_stripBlank($contents);
		return $contents;
	}


	/**
	 + --------------------------------------------------------------
	 * 搜索标签
p	 + --------------------------------------------------------------
	 * @access protected
	 * @param string $contents
	 * @param string $file 文件名
	 * @return void
	 + ---------------------------------------------------------------
	 */
	protected function _searchTags($contents,$file = null) {
		$lineContents = explode("\n",$contents);
		//此处暂不支持Mac系统，它换行为\r
		foreach($lineContents as $lineNumber => $val) {
			$this->_parseLine($lineNumber+1,$val,$file);
		}
	}

	/**
	 + --------------------------------------------------------------
	 * 处理某一行的信息
	 + --------------------------------------------------------------
	 * @access protected
	 * @param int $lineNumber 行数
	 * @param string $line 这一行的内容
	 * @param string $file
	 * @return void
	 + ---------------------------------------------------------------
	 */
	protected function _parseLine($lineNumber,$line,$file = null) {
		$line = trim($line);
		if(!$line) {
			//空白
			return ;
		}
		$line = preg_replace('/<!(.*?)>/','',$line);
		//将注释全部去掉
		if(!$line) {
			return ;
		}
		if(preg_match_all('/<(.*?)\/>/',$line,$matchedArray)) {
			//查询< test />这种标签
			//P($matchedArray);
			foreach($matchedArray[1] as $tag) {
				if(!$this->_isTagCorrect($tag)) {
					$this->_logError($file,$lineNumber,"错误的标签格式=>".$tag);
				} else {
					//查看是否是<include file = "" type = "file" />这种标签
					//如果是这种标签，那么include这个文件
					if(false !== strpos($tag,'include')) {
						if(preg_match('/type\s*?=\s*?\"(.*?)\"/',$tag,$tagType)) {
							if(isset($tagType[1]) && ('file' === strtolower($tagType[1]))) {
								if(preg_match('/file\s*?=\s*?\"(.*?)\"/',$tag,$tagFile)) {
									if(isset($tagFile[1]) && $tagFile[1]) {
										$filePath = MODULES_PATH.'/Views/'
													.(str_replace('.','/',$tagFile[1]))
													.'.'.C('view=>defaultSuffix');
										
										if(is_file($filePath)) {
											$tmpFileContent = file_get_contents($filePath);
											if(false !== $tmpFileContent) {
												$this->_searchTags($tmpFileContent,$filePath);
											} else {
												$this->_logError($file,$lineNumber,"标签=>".$tag.",文件打开失败=>".$filePath);
											}
										} else {
											$this->_logError($file,$lineNumber,"标签=>".$tag.",路径出错=>".$filePath);
										}
									
									}
								}
							}
						}
					}
				}		
			}
			$line = preg_replace('/<(.*?)\/>/','',$line);
			if(!$line) {
				return ;
			}
		}
		if(preg_match_all('/<([a-zA-Z0-9].*?)>/',$line,$matchedArray)) {
			//查询开始标签
			//P($matchedArray);
			foreach($matchedArray[1] as $tag) {
				if(!$this->_isTagCorrect($tag)) {
					$this->_logError($file,$lineNumber,"错误的标签格式=>".$tag);
				} else {
					$pos = strpos($tag,' ');
					$tagName = $pos?substr($tag,0,$pos):$tag;
					if(!array_key_exists($tagName,$this->_tags)) {
						$this->_tags[$tagName] = 1;
					} else {
						$this->_tags[$tagName] ++;
					}
				}			
			}
			$line = preg_replace('/<([a-zA-Z0-9].*?)>/','',$line);
			if(!$line) {
				return ;
			}
		}
		if(preg_match_all('/<\/(.*?)>/',$line,$matchedArray)) {
			//查询结束标签
			//P($matchedArray);
			foreach($matchedArray[1] as $tag) {
				if(array_key_exists($tag,$this->_tags) && ($this->_tags[$tag] > 0)) {
					$this->_tags[$tag] --;
				} else {
					$this->_logError($file,$lineNumber,"不匹配的标签=>".$tag);
				}
			}
			$line = preg_replace('/<\/(.*?)>/','',$line);
			if(!$line) {
				return ;
			}
		}


	}

	/**
	 + --------------------------------------------------------------
	 * 记录错误信息
	 + --------------------------------------------------------------
	 * @access protected
	 * @param string $file
	 * @param int $lineNumber
	 * @param string $info
	 * @return void
	 + ---------------------------------------------------------------
	 */
	 private function _logError($file,$lineNumber,$info) {
	 	$this->_errorInfo[] = array(
	 		'file' => $file,
	 		'lineNumber' => $lineNumber,
	 		'info' => $info
	 	); 
	 }
	
	/**
	 + --------------------------------------------------------------
	 * 察看某一个标签是否格式正确(在调用这个函数前需确认标签存在且不为空)
	 + --------------------------------------------------------------
	 * @access protected
	 * @param string $tag
	 * @return bool
	 + ---------------------------------------------------------------
	 */
	 private function _isTagCorrect($tag) {
 		if(preg_match('/^\s*?[a-zA-Z:0-9]+(\s+?[a-zA-Z_-]+\s*?=\s*?\"(.*?)\")*\s*?$/',$tag,$matchedArray)) {
 			//P($matchedArray);
 			return true;
 		}
 		return false;
	 }
	
	

	/**
	 + -------------------------------------------------------------
	 * 将多个文件合并，即调用<include file = "Public.test" type = "file" />
	 + -------------------------------------------------------------
	 * @access protected
	 * @param string $contents
	 * @return string
	 + -------------------------------------------------------------
	 */
	protected function _mergeFiles($contents) {
		$pattern = '/<\s*?include\s+?file\s*?=\s*?\"([a-zA-Z0-9\.]+)\"\s+?type\s*?=\s*?\"file\"\s*?\/>/';
		$pattern2 = '/<\s*?include\s+?type\s*?=\s*?\"file\"\s+?file\s*?=\s*?\"([a-zA-Z0-9\.]+)\"\s*?\/>/';
		$haveFileToInclude = true;
		while($haveFileToInclude) {
			if(preg_match($pattern,$contents,$tagFile)) {
				if(isset($tagFile[1]) && $tagFile[1]) {
					$filePath = MODULES_PATH.'/Views/'
								.(str_replace('.','/',$tagFile[1]))
								.'.'.C('view=>defaultSuffix');
					$tmpFileContents = file_get_contents($filePath);
					$contents = preg_replace($pattern,$tmpFileContents,$contents,1);
				}
			} else if(preg_match($pattern2,$contents,$tagFile)) {
				if(isset($tagFile[1]) && $tagFile[1]) {
					$filePath = MODULES_PATH.'/Views/'
								.(str_replace('.','/',$tagFile[1]))
								.'.'.C('view=>defaultSuffix');
					$tmpFileContents = file_get_contents($filePath);
					$contents = preg_replace($pattern2,$tmpFileContents,$contents,1);
				}
			} else {
				$haveFileToInclude = false;
			}
		}
		return $contents;
	}
	
	/**
	+ -------------------------------------------------------------
	* 去除注释
	+ -------------------------------------------------------------
	* @access private
	* @param string $contents
	* @return string
	+ --------------------------------------------------------------
	*/
	private function _stripNote($contents) {
		$contents = preg_replace('/<!(.*?)>/','',$contents);
		return $contents;
	}


	/**
	 + -------------------------------------------------------------
	 * 编译常量
	 * 支持 __TP__PUBLIC__ 代表Public目录
	 * 支持 __TP__JS__ 代表JS目录
	 * 支持 __TP__CSS__ 代表CSS目录
	 * 支持 __TP__BASE__ 代表系统根目录，如www.toper2.com
	 * 支持 __TP__CURRENT__MODULE__ 代表当前模块
	 * 支持 __TP__CURRENT__GROUP__ 代表当前组
	 * 支持 __TP__CURRENT__CONTROLLER__ 代表当前控制器
	 * 支持 __TP__CURRENT__ACTION__ 代表当前Action
	 * 如果在一个模版中出现了上面的字符串，解析时会全部被替换掉
	 + -------------------------------------------------------------
	 * @access protected
	 * @param string $contents
	 * @return string
	 + -------------------------------------------------------------
	 */
	protected function _parseConstants($contents) {
		$division = C('url=>division');
		$protocol = C('url=>protocol').'://';
		$group = U('group')?($division.U('group')):"";
		$baseUrlVal = '<?php echo ($_tp_base_url_); ?>';
		$publicUrlVal = '<?php echo($_tp_public_url_); ?>';
		$replace = array(
			//以后可扩展
			'__TP__PUBLIC__' => $publicUrlVal,
			'__TP__JS__' => ($publicUrlVal.'/Js'),
			'__TP__CSS__' => ($publicUrlVal.'/Css'),
			'__TP__BASE__' => $baseUrlVal,
			'__TP__CURRENT__MODULE__' => ($baseUrlVal.$group.$division.U('module')),
			'__TP__CURRENT__GROUP__' => ($baseUrlVal.$group),
			'__TP__CURRENT__CONTROLLER__' => ($baseUrlVal.$group.$division.U('module').$division.U('controller')),
			'__TP__CURRENT__ACTION__' => ($baseUrlVal.$group.$division.U('module').$division.U('controlller').$division.U('action'))
		);
		$contents = str_replace(array_keys($replace),array_values($replace),$contents);
		return $contents;
	}

	/**
	 + ------------------------------------------------------------
	 * 编译一般的变量
	 * 支持{$php}
	 + ------------------------------------------------------------
	 * @access protected
	 * @param string $contents
	 * @return string
	 + ------------------------------------------------------------
	 */
	protected function _parseVal($contents) {
		$pattern = '/\{\s*?(\$.*?)\s*?\}/';
		while(preg_match($pattern,$contents,$matchedArr)) {
			if(preg_match('/\$[a-zA-Z0-9_-]{1,}$/',$matchedArr[1],$tmpArr)) {
				//如果是单个变量
				$replace = '<?php if(isset(\1)): echo(\1); endif; ?>';
			} else {
				//如果是表达式
				$replace = '<?php echo (\1); ?>';
			}
			$contents = preg_replace($pattern,$replace,$contents,1);
		}
		return $contents;
	}

	/**
	 + ------------------------------------------------------------
	 * 解析html字符实体(html character entity)
	 * 比如:__tp__character__entity__copy__
	 * 将会被解析成为&copy;
	 * 注意:此处不是常量，所以不是大写
	 + ------------------------------------------------------------
	 * @access protected
	 * @param string $contents
	 * @return string
	 + ------------------------------------------------------------
	 */
	protected function _parseHtmlCharacterEntity($contents) {
		$pattern = '/__tp__character__entity__([a-zA-Z0-9]{1,})__/';
		while(preg_match($pattern,$contents,$matchedArr)) {
			$replace = '&'.$matchedArr[1].';';
			$contents = preg_replace($pattern,$replace,$contents,1);
		}
		return $contents;
	}

	/**
	 + ------------------------------------------------------------
	 * 解析数组
	 * 将$data.test.test 这种变成$data['test']['test']的形式
	 + ------------------------------------------------------------
	 * @access protected
	 * @param string $contents
	 * @return string
	 + ------------------------------------------------------------
	 */
	protected function _parseArray($contents) {
		$pattern = '/(\$[0-9a-zA-Z_-]+)((\.\$?[0-9a-zA-Z_-]+)+)/';
		while(preg_match($pattern,$contents,$tmpArr)) {
			$replaceArr = $tmpArr[2];
			$replaceArr = substr($replaceArr,1);
			$replaceArr = explode('.',$replaceArr);
			$replacedArr = '';
			foreach($replaceArr as $tmpStr) {
				if(0 === strpos($tmpStr,'$')) {
					$replacedArr .= "[".$tmpStr."]";
				} else {
					$replacedArr .= "['".$tmpStr."']";
				}
			}
			$contents = preg_replace($pattern,'\1'.$replacedArr,$contents,1);
		}
		return $contents;
	}

	/**
	+ ---------------------------------------------------------------
	* 将一些特殊字符替换
	+ ---------------------------------------------------------------
	* @access private
	* @param string $contents
	* @return string
	+ ---------------------------------------------------------------
	*/
	private function _replaceSpecialWords($contents) {
		$search = array(
			'&'
		);
		$replace = array(
			'__tp__replace__with__'
		);
		return str_replace($search,$replace,$contents);
	}

	/**
	+ ----------------------------------------------------------------
	* 将已经替换的字符恢复
	+ ----------------------------------------------------------------
	* @access private
	* @param string $contents
	* @return string
	+ ----------------------------------------------------------------
	*/
	private function _resumeSpecialWords($contents) {
		$search = array(
			'__tp__replace__with__'
		);
		$replace = array(
			'&'
		);
		return str_replace($search,$replace,$contents);
	}


	/**
	 + --------------------------------------------------------------
	 * 将PHP符号优化
	 + --------------------------------------------------------------
	 * @access protected
	 * @param string $contents
	 * @return string
	 + ---------------------------------------------------------------
	 */
	protected function _optimizePhpSymbol($contents) {
		$pattern = "/\?>\s*?<\?php/";
		$replace = "";
		$contents = preg_replace($pattern,$replace,$contents);
		return $contents;
	}

	/**
	 + --------------------------------------------------------------
	 * 去掉多余的空白
	 + --------------------------------------------------------------
	 * @access protected
	 * @param string $contents
	 * @return string
	 + ---------------------------------------------------------------
	 */
	protected function _stripBlank($contents) {
		$pattern = "/\s{2,}/";
		$replace = " ";
		$contents = preg_replace($pattern,$replace,$contents);
		return $contents;
	}

	/**
	 + --------------------------------------------------------------
	 * 模板优化
	 + --------------------------------------------------------------
	 * @access public
	 * @param string $contents
	 * @return string
	 + ---------------------------------------------------------------
	 */
	public function optimize($contents) {
		$contents = $this->_optimizePhpSymbol($contents);
		return $contents;
	}


	/**
	 + -------------------------------------------------------------
	 * 更新缓存
	 + -------------------------------------------------------------
	 * @access public
	 * @param string $contents 需要缓存的内容
	 * @param string $tpl 模板
	 * @return void
	 + --------------------------------------------------------------
	 */
	public function updateCache($contents,$tpl) {
		$fileName = MODULES_PATH.'/Views/~Compile/'.$tpl.'.php';
		if(!file_exists($fileName)) {
			Tp_File::mkdir((MODULES_PATH.'/Views/~Compile'),($tpl.'.php'));
		}
		file_put_contents($fileName,$contents);
	}
}