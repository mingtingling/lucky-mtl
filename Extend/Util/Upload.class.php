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
 * Toper 上传信息的处理
 +--------------------------------------------------
 * @category Toper
 * @package Extend
 * @subpackage Util
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */

class Tp_Upload extends Tp {
	
	private $_maxUploadSize = 204800000000;
	//最大上传文件大小
	private $_allowedFileType = 'gif|jpeg|png|jpg|zip|tar|gz|txt';
	//允许的文件类型
	private $_errorNum = array();
	//错误代号
	private $_errorType = array(
		'0' => 'no error',
		'1' => 'invalid $_FILE',
		'2' => 'incorrect parameter',
		'3' => 'fail to upload the file',
		'4' => 'the file is to large to be allowed',
		'5' => 'the suffix of the file is not allowed',
		'6' => 'fail to move the file',
		'7' => 'move the file to a none exists folder'
	);
	//错误信息

	/**
	+--------------------------------------------------------
	* 设置错误代号
	+--------------------------------------------------------
	* @access private
	* @param int $errorNum 错误代号
	* @return void
	+--------------------------------------------------------
	*/
	private function _setErrorNum($errorNum) {
		$this->_errorNum[] = $errorNum;
	}
	
	/**
	+--------------------------------------------------------
	* 初始化信息
	+--------------------------------------------------------
	* @access public
	* @param string $allowedFileType 如果允许多个文件后缀名，那么以|分隔开
	* @param int $maxUploadSize 允许上传的文件的最大大小
	* @return void
	+--------------------------------------------------------
	*/
	public function init($allowedFileType = null,$maxUploadSize = null) {
		if(null !== $allowedFileType) {
			$this->_allowedFileType = $allowedFileType;
		}
		if(null !== $maxUploadSize) {
			if(!is_int($maxUploadSize)) {
				$this->_setErrorNum(2);
			} else {
				if($maxUploadSize > 0) {
					$this->_maxUploadSize = $maxUploadSize;
				}
			}
		}
		return $this;
	}

	/**
	+--------------------------------------------------------
	* 上传
	+--------------------------------------------------------
	* @access public
	* @param string $name 上传文件名
	* @param string $storePath 存储的路径
	* @param string $storeFileName 存储文件名
	* @return bool
	+--------------------------------------------------------
	*/
	public function upload($name,$storePath,$storeFileName) {
		$fileInfo = $_FILES[$name];
		if(!is_array($fileInfo) || !$fileInfo) {
			$this->_setErrorNum(1);
			return false;
		}
		if((!isset($fileInfo['error'])) || (0 !== $fileInfo['error'])) {
			//检测上传错误
			$this->_setErrorNum(3);
			return false;
		}
		if((!isset($fileInfo['size'])) || ($fileInfo['size'] > $this->_maxUploadSize)) {
			//检测文件大小
			$this->_setErrorNum(4);
			return false;
		}
		if(true !== $this->_suffixIsAllowed($fileInfo['name'])) {
			//检测文件后缀名
			$this->_setErrorNum(5);
			return false;
		}
		if(true !== $this->_moveFile($fileInfo['tmp_name'],$storePath,$storeFileName)) {
			$this->_setErrorNum(6);
			return false;
		}
		return true;
	}

	/**
	+--------------------------------------------------------
	* 检测文件后缀名
	+--------------------------------------------------------
	* @access public
	* @param string $fileName 上传文件名
	* @return bool
	+--------------------------------------------------------
	*/
	private function _suffixIsAllowed($fileName) {
		$suffix = substr($fileName,strpos($fileName,'.') + 1);
		$this->_allowedFileType = explode('|',$this->_allowedFileType);
		foreach($this->_allowedFileType as $allowedSuffix) {
			if($suffix === $allowedSuffix) {
				return true;
			}
		}
		return false;
	}

	/**
	+--------------------------------------------------------
	* 移动文件到正确的位置
	+--------------------------------------------------------
	* @access private
	* @param string $preFile 移动之前的文件
	* @param string $currentFilePath 移动之后的文件路径
	* @param string $currentFileName 移动之后的文件名
	* @return bool
	+--------------------------------------------------------
	*/
	private function _moveFile($preFile,$currentFilePath,$currentFileName) {
		if(!is_dir($currentFilePath)) {
			$this->_setErrorNum(7);
			return false;
		}
		return move_uploaded_file($preFile,$currentFilePath.'/'.$currentFileName);
	}

	/**
	+--------------------------------------------------------
	* 得到信息
	+--------------------------------------------------------
	* @access public
	* @param void
	* @return mixed
	+--------------------------------------------------------
	*/
	public function getMsg() {
		$arr = array();
		if($this->_errorNum) {
			foreach($this->_errorNum as $val) {
				$arr[] = $this->_errorType[$val];
			}
		} else {
			return $this->_errorType[0];
		}
		return $arr;
	}

	/**
	+--------------------------------------------------------
	* 得到Json信息
	+--------------------------------------------------------
	* @access public
	* @param void
	* @return string
	+--------------------------------------------------------
	*/
	public function getJsonMsg() {
		$arr = array();
		if($this->_errorNum) {
			foreach($this->_errorNum as $val) {
				$arr[$val] = $this->_errorType[$val];
			}
		} else {
			return json_encode(array(
				'0' => $this->_errorType[0]
			));
		}
		return json_encode($arr);
	}

	/**
	+--------------------------------------------------------
	* 是否存在错误信息
	+--------------------------------------------------------
	* @access public
	* @param void
	* @return bool
	+--------------------------------------------------------
	*/
	public function haveError() {
		return 0 === count($this->_errorNum) ? false : true;
	}
}
