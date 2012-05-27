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
 * Toper 通过smtp发送邮件
 +--------------------------------------------------
 * @category Toper
 * @package Extend
 * @subpackage Protocol
 * @author mingtingling
 * @version 1.1
 + -------------------------------------------------
 */

class Tp_Smtp {
	const ERROR = 1;
	const NOTICE = 2;
	private $_conn = null; //连接句柄
	private $_debug = false; //调试状态
	private $_log = array(); //日志信息
	private $_host = null; //主机
	private $_user = null; //用户名
	private $_password = null; //密码
	private $_mailFrom = null;
	private $_port = 25; //端口号
	private $_timeout = 35;
	private $_lineMaxLength = 998; //每一行的最大字符数量(发送时有效)
	private $_CRLF = "\r\n"; //每一行的结束符
	public function __construct() {
	}

	/**
	+ ----------------------------------------------------
	* 连接到服务器
	+ ----------------------------------------------------
	* @access private
	* @param void
	* @param bool
	+ -----------------------------------------------------
	*/
	private function _connect() {
		$this->_conn = fsockopen($this->_host,$this->_port,$errno,$errstr,$this->_timeout);
		if(!$this->_conn) {
			$this->_log('连接失败,errno:'.$errno.',errstr:'.$errstr);
			return false;
		} else {
			$reply = $this->_getLines();
			$this->_log('连接成功,信息为:'.$reply,Tp_Smtp::NOTICE);
			return true;
		}
	}

	/**
	+ ----------------------------------------------------
	* Quit
	+ ----------------------------------------------------
	* @access private
	* @param void
	* @param bool
	+ -----------------------------------------------------
	*/
	private function _quit() {
		fputs($this->_conn,'quit '.$this->_CRLF);
		$reply = $this->_getLines();
		if(substr($reply,0,3) != 221) {
			$this->_log('quit请求失败,信息为:'.$reply);
			return false;
		} else {
			$this->_log('quit 请求成功,信息为:'.$reply,Tp_Smtp::NOTICE);
			return true;
		}
	}

	/**
	+ ------------------------------------------------------
	* 初始化信息
	+ ------------------------------------------------------
	* @access public
	* @param string $user
	* @param string $password
	* @param string $host
	* @param int $port
	* @param int $timeout
	* @return void
	+ ------------------------------------------------------- 
	*/
	public function init($user,$password,$host = 'localhost',$port = 25,$timeout = 35) {
		$this->_mailFrom = $user;
		$this->_host = $host;
		$this->_user = $user;
		$this->_password = $password;
		$this->_port = $port;
		$this->_timeout = $timeout;
	}

	/**
	+ ------------------------------------------------------
	* 记录信息到日志信息中
	+ ------------------------------------------------------
	* @access private
	* @param string $log 日志信息
	* @param int $type 记录类型
	* @return void
	+ ------------------------------------------------------
	*/
	private function _log($log,$type = Tp_Smtp::ERROR) {
		if(Tp_Smtp::ERROR === $type) {
			$this->_log[] = "<font color = 'red'>[Error]".$log."</font>";
		} else {
			$this->_log[] = "[Notice]".$log;
		}
	}

	/**
	+ -------------------------------------------------------
	* 设置调试状态
	+ -------------------------------------------------------
	* @access public
	* @param bool $status
	* @return void
	+ -------------------------------------------------------
	*/
	public function setDebug($status = false) {
		$this->_debug = $status;
	}

	/**
	+ -------------------------------------------------------
	* 显示调试信息
	+ -------------------------------------------------------
	* @access public
	* @param void
	* @return void
	+ -------------------------------------------------------
	*/
	public function showDebug() {
		if(true === $this->_debug) {
			foreach($this->_log as $tmpStr) {
				echo "<br/>".$tmpStr;
			}
		} else {
			echo "<font color = 'red'>您没有开启调试，请开启调试后再试!</font>";
		}
	}

	/**
	+ --------------------------------------------------------
	* 断开连接
	+ --------------------------------------------------------
	* @access private
	* @param void
	* @return void
	+ --------------------------------------------------------
	*/
	private function _close() {
		fclose($this->_conn);
		$this->_log('断开成功',Tp_Smtp::NOTICE);
	}

	/**
	+ ---------------------------------------------------------
	* 是否已经连接
	+ ---------------------------------------------------------
	* @access private
	* @param void
	* @return bool
	+ ---------------------------------------------------------
	*/
	private function _isConnected() {
		if($this->_conn) {
			$socketStatus = socket_get_status($this->_conn);
			if(true !== $socketStatus['eof']) {
				return true;
			}
		}
		return false;
	}

	/**
	+ ----------------------------------------------
	* socket发出helo 命令 (连接之后的第一步)
	* 此命令用来标志发件人
	* 命令 HELO server.com
	* 返回状态码
	* 成功:250
	* 失败:500,501,504,421
	+ ----------------------------------------------
	* @access private
	* @param void
	* @return bool
	+ ----------------------------------------------
	*/
	private function _helo() {
		fputs($this->_conn,'HELO '.$this->_host.$this->_CRLF);
		$reply = $this->_getLines();
		if(substr($reply,0,3) != 250) {
			$this->_log('HELO命令出错,信息为:'.$reply);
			return false;
		} else {
			$this->_log('HELO命令成功,信息为:'.$reply,Tp_Smtp::NOTICE);
			return true;
		}
	}

	/**
	+ ----------------------------------------------
	* (连接之后的第二步)
	* 此命令用来认证
	* 命令 AUTH LOGIN 
	* 返回状态码
	* 成功:334
	+ ----------------------------------------------
	* @access private
	* @param void
	* @return bool
	+ ----------------------------------------------
	*/
	private function _authenticate() {
		fputs($this->_conn,"AUTH LOGIN".$this->_CRLF);
		$reply = $this->_getLines();
		if(substr($reply,0,3) != 334) {
			$this->_log('认证不被允许,信息为:'.$reply);
			return false;
		} else {
			$this->_log('认证成功,信息为:'.$reply,Tp_Smtp::NOTICE);
			fputs($this->_conn,base64_encode($this->_user).$this->_CRLF); //加密的用户名
			$reply = $this->_getLines();
			if(substr($reply,0,3) != 334) {
				$this->_log('用户名认证失败,信息为:'.$reply);
				return false;
			} else {
				$this->_log('用户名认证成功,信息为:'.$reply,Tp_Smtp::NOTICE);
				fputs($this->_conn,base64_encode($this->_password).$this->_CRLF);
				$reply = $this->_getLines();
				if(substr($reply,0,3) != 235) {
					$this->_log('密码认证失败,信息为:'.$reply);
					return false;
				} else {
					$this->_log('密码认证成功,信息为:'.$reply,Tp_Smtp::NOTICE);
					return true;
				}
			}
		}
	}
	
	/**
	+ ----------------------------------------------
	* (连接之后的第三步)
	* 此命令表明客户端发送Mail命令
	* 命令 MAIL FROM: <test@163.com>
	* 返回状态码
	* 成功:250
	+ ----------------------------------------------
	* @access private
	* @param void
	* @return bool
	+ ----------------------------------------------
	*/
	private function _mailFrom() {
		fputs($this->_conn,"MAIL FROM: <".$this->_mailFrom.">".$this->_CRLF);
		$reply = $this->_getLines();
		if(substr($reply,0,3) != 250) {
			$this->_log("MAIL请求失败,信息为:".$reply);
			return false;
		} else {
			$this->_log('MAIL请求成功，信息为:'.$reply,Tp_Smtp::NOTICE);
			return true;
		}
	}
	
	/**
	+ ----------------------------------------------
	* (连接之后的第四步)
	* 此命令说明客户端发送rcpt命令标志收件人
	* 命令 RCPT TO: <test@163.com>
	* 返回状态码
	* 成功:250
	+ ----------------------------------------------
	* @access private
	* @param string $mailTo
	* @return bool
	+ ----------------------------------------------
	*/
	private function _rcptTo($mailTo) {
		fputs($this->_conn,"RCPT TO:<".$mailTo.">".$this->_CRLF);
		$reply = $this->_getLines();
		if(substr($reply,0,3) != 250) {
			$this->_log('RCPT TO请求失败,信息为:'.$reply);
			return false;
		} else {
			$this->_log('RCPT TO请求成功，信息为:'.$reply,Tp_Smtp::NOTICE);
			return true;
		}
	}

	/**
	+ ----------------------------------------------
	* (连接之后的第五步)
	* 此命令代表发送邮件
	* 命令 DATA
	+ ----------------------------------------------
	* @access private
	* @param string $data
	* @return bool
	+ ----------------------------------------------
	*/
	private function _data($data) {
		fputs($this->_conn,"DATA".$this->_CRLF);
		$reply = $this->_getLines();
		if(substr($reply,0,3) != 354) {
			$this->_log("DATA 请求失败,信息为:".$reply);
			return false;
		} else {
			$data = str_replace('\r\n','\n',$data);
			$data = str_replace('\r','\n',$data);
			//将换行符统一为\n
			$lines = explode('\n',$data);
			//将所有的数据按照换行符隔开(\r,\n)
			foreach($lines as $lineStr) {
				if($this->_lineMaxLength < strlen($lineStr)) {
					//字符串太长，需要分割发送
					$lineOuts = array();
					do {
						$lineOuts[] = substr($lineStr,0,$this->_lineMaxLength-1);
						$lineStr = substr($lineStr,$this->_lineMaxLength-1);
					} while($this->_lineMaxLength < strlen($lineStr));
					foreach($lineOuts as $outStr) {
						fputs($this->_conn,$outStr.$this->_CRLF);
					}
				} else {
					fputs($this->_conn,$lineStr.$this->_CRLF);
				}
			}
			fputs($this->_conn,$this->_CRLF.".".$this->_CRLF);
			//<CRLF>.<CRLF> 结束
			$reply = $this->_getLines();
			if(substr($reply,0,3) != 250) {
				$this->_log('DATA命令发送数据失败,信息为:'.$reply);
				return false;
			} else {
				$this->_log('DATA命令发送数据成功，信息为:'.$reply,Tp_Smtp::NOTICE);
				return true;
			}
		}
	}
	
	/**
	+ ----------------------------------------------
	* 得到返回的字符串
	+ ----------------------------------------------
	* @access private
	* @param void
	* @return string
	+ ----------------------------------------------
	*/
	private function _getLines() {
		$reply = '';
		while($str = fgets($this->_conn,515)) {
			$reply .= $str;
			//如果第四个字符是空格，那么不需要再继续获取，结束
			if(' ' === substr($str,3,1)) {
				break;
			}
			
		}
		return $reply;
	}
	
	/**
	+ ----------------------------------------------
	* 认证(在调用send函数之前使用)
	* 包括连接，验证等功能
	+ ----------------------------------------------
	* @access public
	* @param void
	* return bool
	+ ----------------------------------------------
	*/
	public function auth() {
		if($this->_connect()) {
			if($this->_helo()) {
				if($this->_authenticate()) {
					if($this->_mailFrom()) {
						return true;
					}
				}
			}		
		}
		return false;
	}

	/**
	+ ----------------------------------------------
	* 退出，发送邮件之后调用
	+ ----------------------------------------------
	* @access public
	* @param void
	* return void
	+ ----------------------------------------------
	*/	
	public function quit() {
		if($this->_isConnected()) {
			$this->_quit();
			$this->_close();
		}
	}
	
	/**
	+ ----------------------------------------------
	* 发送smtp邮件
	+ ----------------------------------------------
	* @access public
	* @param string $mailTo
	* @param string $data
	* return bool
	+ ----------------------------------------------
	*/
	public function send($mailTo,$data) {
		if($this->_rcptTo($mailTo)) {
			if($this->_data($data)) {
				return true;
			}
		}
		return false;
	}
}