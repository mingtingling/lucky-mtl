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
 * Toper 发送邮件
 * 此处借鉴了php mailSender
 +--------------------------------------------------
 * @category Toper
 * @package Extend
 * @subpackage Util
 * @author mingtingling
 * @version 1.1
 +---------------------------------------------------
 */

class Tp_SendMail extends Tp {
	
	const NOTICE = 1;
	const ERROR = 2;
	private $_user = null; //登录到邮箱的用户，实际为发信人
	private $_password = null; //登录用户的密码
	private $_host = null; //登录的邮箱服务器
	private $_mailTo = null; //收信者
	private $_mailFrom = null; //发信者
	private $_mailCC = null; //抄送
	private $_mailBCC = null; //秘密抄送
	private $_mailSubject = null; //主题
	private $_mailText = ''; //内容
	private $_mailHtml = ''; //以Html形式传递
	private $_mailAttachments = null; //附件
	private $_mailType = 'smtp'; //发送的邮件类型
	private $_socket = null; // 连接的socket
	private $_debug = false; //调试是否打开
	private $_log = array(); //记录信息
	private $_charset = 'utf8'; //编码格式
	private $_CRLF = '\r\n';
	/**
	+ ----------------------------------------------------
	* 初始化信息
	* 包括:
	* user 发邮件的人,也为登陆邮箱的用户名
	* to 收邮件的人,多人以;分隔
	* cc 抄送,多人以;分隔
	* bcc 秘送,多人以;分隔
	* password 登录到邮箱的密码
	* host 邮箱服务器
	* debug 调试
	* charset 编码格式
	* 支持init(array(
	* 			'password'=>'password',
	*			'host'=>'smtp.qq.com',
	*			'user'=>'test@gmail.com',
	*			'to'=>'test@gmail.com',
	*			'cc'=>'test@gmail.com',
	*			'bcc'=>'test@gmail.com',
	*			'debug'=>true))
	+ ----------------------------------------------------
	* @access public
	* @param array $mailConfig
	* @return void
	+ ----------------------------------------------------
	*/
	public function init($mailConfig = array()) {
		foreach($mailConfig as $key=>$val) {
			if('to' === $key) {
				$this->_setMailTo($val);
			} else if('user' === $key) {
				$this->_setMailFrom($val);
			} else if('cc' === $key) {
				$this->_setMailCC($val);
			} else if('bcc' === $key) {
				$this->_setMailBCC($val);
			} else if('debug' === $key) {
				$this->_debug = $val;
			} else if('type' === $key) {
				$this->_mailType = $val;
			} else if('password' === $key) {
				$this->_password = $val;
			} else if('host' === $key) {
				$this->_host = $val;
			} else if('charset' === $val) {
				$this->_charset = $val;
			} else {
				$this->_log('未知的配置项!');
			}
		}
	}


	/**
	+ ----------------------------------------------------
	* 记录日志
	+ ----------------------------------------------------
	* @access private
	* @param string $log
	* @param int $type 日志类型，默认为Tp_SendMail::ERROR
	* @return void
	+ ----------------------------------------------------
	*/
	private function _log($log,$type = Tp_SendMail::ERROR) {
		if(Tp_SendMail::ERROR === $type) {
			$this->_log[] = "<font color = 'red'>[Error]".$log."</font>";
		} else {
			$this->_log[] = "[Notice]".$log;
		}
	}

	/**
	+ ----------------------------------------------------
	* 显示调试信息
	+ ----------------------------------------------------
	* @access public
	* @param void
	* @return void
	+ ----------------------------------------------------
	*/
	public function showDebug() {
		if(true === $this->_debug) {
			foreach($this->_log as $logStr) {
				echo "<br/>".$logStr;
			}
			if(null !== $this->_socket) {
				$this->_socket->showDebug();
			}
		} else {
			echo "<br/><font color = 'red'>您没有打开调试，请打开调试后再试!</font>";
		}
	}

	/**
	+ ----------------------------------------------------
	* 设置邮件收信人，支持多个收信人，以；分隔
	+ ----------------------------------------------------
	* @access private
	* @param string $mailTo
	* @return bool
	+ ----------------------------------------------------
	*/
	private function _setMailTo($mailTo) {
		$mailToArray = explode(';',$mailTo);
		for($tmp = 0;$tmp < count($mailToArray);$tmp++) {
			if(!$this->_isMail($mailToArray[$tmp])) {
				$this->_log('收信人格式出错!');
				return false;
			}
		}
		$this->_mailTo = $mailTo;
		$this->_log('设置收信人成功!',Tp_SendMail::NOTICE);
		return true;
	}

	/**
	+ ----------------------------------------------------
	* 检测邮箱格式是否合格
	+ ----------------------------------------------------
	* @access private
	* @param string $mail
	* @return bool
	+ ----------------------------------------------------
	*/
	private function _isMail($mail) {
		$pattern = '/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+\.[a-zA-Z0-9_-]{2,4}$/';
		return preg_match($pattern,$mail);
	}
	
	/**
	+ ----------------------------------------------------
	* 设置抄送地址,多人以;分隔
	+ ----------------------------------------------------
	* @access private
	* @param string $ccAddr
	* @return bool
	+ ----------------------------------------------------
	*/
	private function _setMailCC($ccAddr) {
		$ccArray = explode(';',$ccAddr);
		for($tmp = 0;$tmp < count($ccArray);$tmp ++) {
			if(!$this->_isMail($ccArray[$tmp])) {
				$this->_log('设置抄送失败!');
				return false;
			}
		}
		$this->_mailCC = $ccAddr;
		$this->_log('设置抄送成功!',Tp_SendMail::NOTICE);
		return true;
	}

	/**
	+ ----------------------------------------------------
	* 设置秘密抄送地址,多人以;分隔
	+ ----------------------------------------------------
	* @access private
	* @param string $bccAddr
	* @return bool
	+ ----------------------------------------------------
	*/
	private function _setMailBCC($bccAddr) {
		$bccArray = explode(';',$bccAddr);
		for($tmp = 0;$tmp < count($bccArray);$tmp ++) {
			if(!$this->_isMail($bccArray[$tmp])) {
				$this->_log('设置秘密抄送失败!');
				return false;
			}
		}
		$this->_mailBCC = $bccAddr;
		$this->_log('设置秘密抄送成功!',Tp_SendMail::NOTICE);
		return true;
	}

	/**
	+ ----------------------------------------------------
	* 设置发送人
	+ ----------------------------------------------------
	* @access private
	* @param string $mailFrom
	* @return bool
	+ ----------------------------------------------------
	*/
	private function _setMailFrom($mailFrom) {
		if(!$this->_isMail($mailFrom)) {
			$this->_log('设置发信人失败!');
			return false;
		} else {
			$this->_mailFrom = $mailFrom;
			$this->_user = $mailFrom;
			$this->_log('设置发信人成功!',Tp_SendMail::NOTICE);
			return true;
		}
	}
	
	/**
	+ ----------------------------------------------------
	* 设置邮件主题
	+ ----------------------------------------------------
	* @access public
	* @param string $mailSubject
	* @return bool
	+ ----------------------------------------------------
	*/
	public function setMailSubject($mailSubject) {
		if(strlen(trim($mailSubject)) > 0) {
			$mailSubject = preg_replace(
				array(
					'/\\\n/',
					'/\\\r/',
					'/<br\/>/',
					'/<br>/'
				),
				array(
					'',
					'',
					'',
					''
				),
				$mailSubject);
			$this->_mailSubject = $mailSubject;
			$this->_log('设置邮件主题成功!',Tp_SendMail::NOTICE);
			return true;
		} else {
			//字符串为空或者只有空格
			$this->_log('邮件主题为空!');
			return false;
		}
	}

	/**
	+ ----------------------------------------------------
	* 设置邮件内容
	+ ----------------------------------------------------
	* @access public
	* @param string $mailText
	* @return bool
	+ ----------------------------------------------------
	*/
	public function setMailText($mailText) {
		if(strlen(trim($mailText)) > 0) {
			$this->_mailText = base64_encode($mailText);
			$this->_log('设置邮件内容成功',Tp_SendMail::NOTICE);
			return true;
		} else {
			//字符串为空或者只有空格
			$this->_log('邮件内容为空!');
			return false;
		}
	}

	/**
	+ ----------------------------------------------------
	* 设置HTML版的邮件内容
	+ ----------------------------------------------------
	* @access public
	* @param string $mailHtml
	* @return bool
	+ ----------------------------------------------------
	*/
	public function setMailHtml($mailHtml) {
		if(strlen(trim($mailHtml)) > 0) {
			$this->_mailHtml = base64_encode($mailHtml);
			$this->_log('设置HTML版的邮件内容成功!',Tp_SendMail::NOTICE);
			return true;
		} else {
			//字符串为空或者只有空格
			$this->_log('HTML版邮件内容为空!');
			return false;
		}
	}

	/**
	+ ----------------------------------------------------
	* 设置邮件附件
	+ ----------------------------------------------------
	* @access public
	* @param string $maiAttachments 附件，实际上为路径
	* @return bool
	+ ----------------------------------------------------
	*/
	public function setMailAttachments($mailAttachments) {
		if(strlen(trim($mailAttachments)) > 0) {
			$this->_mailAttachments = $mailAttachments;
			$this->_log('设置邮件附件成功!',Tp_SendMail::NOTICE);
			return true;
		} else {
			//字符串为空或者只有空格
			$this->_log('邮件附件为空!');
			return false;
		}
	}

	/**
	+ ----------------------------------------------------
	* 得到边界值
	+ ----------------------------------------------------
	* @access private
	* @param void
	* @return string
	+ ----------------------------------------------------
	*/
	private function _getBoundary() {
		return ("------".md5(mt_rand()));
	}

	/**
	+ ----------------------------------------------------
	* 判断附件的类型
	+ ----------------------------------------------------
	* @access private
	* @param string $inFileLocation 该文件的路径
	* @return string
	+ ----------------------------------------------------
	*/
	private function _getContentType($inFileLocation) {
		$inFileLocation = basename($inFileLocation); //去除文件路径
		$suffix = strrchr($inFileLocation,'.'); //取得文件后缀名
		if(false === $suffix) {
			return 'application/octet-stream';
		}
		switch($suffix) {
			case '.gif':
				return 'image/gif';
				break;
			case '.jpg':
				return 'image/jpeg';
				break;
			case '.png':
				return 'image/png';
				break;
			case '.gz':
				return 'application/x-gzip';
				break;
			case '.htm':
				return 'text/html';
				break;
			case '.html':
				return 'text/html';
				break;
			case '.tar':
				return 'application/x-tar';
				break;
			case '.txt':
				return 'text/plain';
				break;
			case '.zip':
				return 'application/zip';
				break;
			default:
				return 'application/octet-stream';
		}
	}
	
	/**
	+ ----------------------------------------------------
	* 为内容添加文件头
	+ ----------------------------------------------------
	* @access private
	* @param void
	* @return string
	+ ----------------------------------------------------
	*/
	private function _formatMailTextHeader() {
		$out = '';
		$out .= ('Content-type: text/plain; charset="'.$this->_charset.'"'.$this->_CRLF);
		$out .= ('Content-Transfer-Encoding: base64'.$this->_CRLF);
		$out .= ($this->_CRLF.$this->_CRLF.$this->_mailText.$this->_CRLF);
		return $out;
	}
	
	/**
	+ ----------------------------------------------------
	* 为HTML内容添加文件头
	+ ----------------------------------------------------
	* @access private
	* @param void
	* @return string
	+ ----------------------------------------------------
	*/
	private function _formatMailHtmlHeader() {
		$out = '';
		$out .= ('Content-type: text/html; charset="'.$this->_charset.'"'.$this->_CRLF);
		$out .= ('Content-Transfer-Encoding: base64'.$this->_CRLF);
		$out .= ($this->_CRLF.$this->_mailHtml.$this->_CRLF);
		return $out;
	}
	
	/**
	+ ----------------------------------------------------
	* 为附件添加文件头
	+ ----------------------------------------------------
	* @access private
	* @param string $inFileLocation 输入的文件路径
	* @return string
	+ ----------------------------------------------------
	*/
	private function _formatAttachmentHeader($inFileLocation) {
		$out = '';
		
		$contentType = $this->_getContentType($inFileLocation);
		//P($contentType);
		if(preg_match('/text/',$contentType)) {
			//文本型附件
			$out .= ('Content-type: '.$contentType.';');
			$out .= ('name="'.basename($inFileLocation).'"'.$this->_CRLF);
			$out .= ('Content-Transfer-Encoding: base64'.$this->_CRLF);
			$out .= ('Content-Disposition: attachment;');
			$out .= ('filename="'.basename($inFileLocation).'"'.$this->_CRLF);
			$out .= $this->_CRLF;
			$file = fopen($inFileLocation,'r');
			while(!feof($file)) {
				$out .= (base64_encode(fgets($file,4096)).$this->_CRLF);
			}
			fclose($file);
			$out .= $this->_CRLF;
		} else {
			//非文本，以64位编码
			$out .= ('Content-type: '.$contentType.';');
			$out .= ('name="'.basename($inFileLocation).';'.$this->_CRLF);
			$out .= ('Content-Transfer-Encoding: base64'.$this->_CRLF);
			$out .= ('Content-Disposition: attachment; ');
			$out .= ('filename="'.basename($inFileLocation).'"'.$this->_CRLF);
			$out .= $this->_CRLF;
			$file = fopen($inFileLocation,'r');
			while(!feof($file)) {
				$out .= (base64_encode(fgets($file,1024)).$this->_CRLF);
			}
			fclose($file);
			$out .= $this->_CRLF;
		}
		return $out;
	}

	/**
	+ ----------------------------------------------------
	* 设置邮件主题，发件人，收件人信息
	+ ----------------------------------------------------
	* @access private
	* @param void
	* @return string
	+ ----------------------------------------------------
	*/
	private function _getCommonInfo() {
		$out = '';
		$out .= ('From: <'.$this->_mailFrom.'>'.$this->_CRLF);
		$out .= ('To: <'.$this->_mailTo.'>'.$this->_CRLF);
		$out .= ('Subject: '.$this->_mailSubject.$this->_CRLF);
		$out .= ('MIME_Version: 1.0'.$this->_CRLF);
		return $out;
	}

	/**
	+ ----------------------------------------------------
	* 发送邮件
	+ ----------------------------------------------------
	* @access public
	* @param void
	* @return bool
	+ ----------------------------------------------------
	*/
	public function send() {
		if((!$this->_mailFrom) || (!$this->_mailTo)) {
			echo "just to sendf";
			$this->_log('没有发信人或者收信人!');
			return false;
		} else {
			if(($this->_mailText) && (!$this->_mailHtml) && (!$this->_mailAttachments)) {
				//仅文本
				$mail = '';
				$mail .= $this->_getCommonInfo();
				$mail .= ('Content-Type:  multipart/alternative'.$this->_CRLF);
				$boundary = $this->_getBoundary();
				$mail .= ('boundary="'.$boundary.'"'.$this->_CRLF);
				$mail .= $this->_formatMailTextHeader();
				$mail .= ($boundary.$this->_CRLF);
				//P($mail);
			} else if((!$this->_mailText) && ($this->_mailHtml) && (!$this->_mailAttachments)) {
				//仅HTML
				$mail = '';
				$mail .= $this->_getCommonInfo();
				$mail .= $this->_formatMailHtmlHeader();
				//P($mail);
			} else if(($this->_mailText) && ($this->_mailHtml) && (!$this->_mailAttachments)) {
				//文本和HTML
				$mail = '';
				$mail .= $this->_getCommonInfo();
				$mail .= ('Content-Type:  multipart/alternative'.$this->_CRLF);
				$boundary = $this->_getBoundary();
				$mail .= ('boundary="'.$boundary.'"'.$this->_CRLF);
				$mail .= ($boundary.$this->_CRLF);
				$mail .= $this->_formatMailTextHeader();
				$mail .= ($boundary.$this->_CRLF);
				$mail .= $this->_formatMailHtmlHeader();
				$mail .= ($boundary.$this->_CRLF);
				//P($mail);
			} else if(($this->_mailText) && (!$this->_mailHtml) && ($this->_mailAttachments)) {
				//文本和附件
				$mail = '';
				$mail .= $this->_getCommonInfo();
				$mail .= ('Content-Type: multipart/mixed'.$this->_CRLF);
				$boundary = $this->_getBoundary();
				$mail .= ('boundary="'.$boundary.'"'.$this->_CRLF);
				$mail .= ($boundary.$this->_CRLF);
				$mail .= $this->_formatMailTextHeader();
				$mail .= ($boundary.$this->_CRLF);
				$mail .= $this->_formatAttachmentHeader($this->_mailAttachments);
				$mail .= ($boundary.$this->_CRLF);
				//P($mail);
			} else if((!$this->_mailText) && ($this->_mailHtml) && ($this->_mailAttachments)) {
				//HTML和附件
				$mail = '';
				$mail .= $this->_getCommonInfo();
				$mail .= ('Content-Type: multipart/mixed'.$this->_CRLF);
				$boundary = $this->_getBoundary();
				$mail .= ('boundary="'.$boundary.'"'.$this->_CRLF);
				$mail .= ($boundary.$this->_CRLF);
				$mail .= $this->_formatMailHtmlHeader();
				$mail .= ($boundary.$this->_CRLF);
				$mail .= $this->_formatAttachmentHeader($this->_mailAttachments);
				$mail .= ($boundary.$this->_CRLF);
				//P($mail);
			} else {
				//文本,HTML和附件
				$mail = '';
				$mail .= $this->_getCommonInfo();
				$mail .= ('Content-Type: multipart/mixed'.$this->_CRLF);
				$boundary = $this->_getBoundary();
				$mail .= ('boundary="'.$boundary.'"'.$this->_CRLF);
				$mail .= ($boundary.$this->_CRLF);
				$mail .= $this->_formatMailHtmlHeader();
				$mail .= ($boundary.$this->_CRLF);
				$mail .= $this->_formatMailTextHeader();
				$mail .= ($boundary.$this->_CRLF);
				$mail .= $this->_formatAttachmentHeader($this->_mailAttachments);
				$mail .= ($boundary.$this->_CRLF);
				//P($mail);
			}
			$this->_selectedSendMail($mail);
		}
	}

	/**
	+ -------------------------------------------------
	* 选择smtp或者pop3发送邮件
	+ -------------------------------------------------
	* @access private
	* @param string $mailContent 邮件要发送的内容
	* @return bool
	+ -------------------------------------------------
	*/
	private function _selectedSendMail($mailContent) {
		switch($this->_mailType) {
			case 'smtp':
				tp_include(TP_PATH.'/Extend/Util/Protocol/Smtp.class.php');
				$this->_socket = new Tp_Smtp();
				break;
			case 'pop3':
				tp_include(TP_PATH.'/Extend/Util/Protocol/Pop3.class.php');
				$this->_socket = new Tp_Pop3();
				break;
			default:
				tp_include(TP_PATH.'/Extend/Util/Protocol/Smtp.class.php');
				$this->_socket = new Tp_Smtp();
				break;
		}
		$this->_socket->setDebug($this->_debug);
		$this->_socket->init($this->_user,$this->_password,$this->_host);
		$this->_socket->auth();
		if(is_array($this->_mailTo)) {
			foreach($this->_mailTo as $mailToStr) {
				$this->_socket->send($mailToStr,$mailContent);
			}
		} else {
			$this->_socket->send($this->_mailTo,$mailContent);
		}
		$this->_socket->quit();
	}
}