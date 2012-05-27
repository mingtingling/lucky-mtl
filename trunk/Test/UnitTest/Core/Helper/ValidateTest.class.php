<?php
require_once(dirname(__FILE__).'/../../../../Core/PHPUnit.class.php');
class ValidateTest extends Tp_PHPUnit {
	function __construct() {
		parent::__construct();
		import('Tp.Core.Helper.Validate');
	}
	public function testIsMail() {
		$this->assertTrue(Tp_Validate::isMail("717547858@qq.com"));
		$this->assertFalse(Tp_Validate::isMail("717547858.com"));
		$this->assertFalse(Tp_Validate::isMail("717547858@qq."));
		$this->assertFalse(Tp_Validate::isMail("717547858"));
	}

	public function testIsNumber() {
		$this->assertTrue(Tp_Validate::isNumber("123"));
		$this->assertFalse(Tp_Validate::isNumber("abc"));
		$this->assertFalse(Tp_Validate::isNumber("123abc"));
	}
	public function testIsIp() {
		$this->assertTrue(Tp_Validate::isIp("127.0.0.1"));
		$this->assertFalse(Tp_Validate::isIp("a.b.c.d"));
	}
	public function testIsQQ() {
		$this->assertTrue(Tp_Validate::isQQ("717547858"));
		$this->assertFalse(Tp_Validate::isQQ("1"));
	}
	public function testIsEnglishWord() {
		$this->assertTrue(Tp_Validate::isEnglishWord("abc"));
		$this->assertFalse(Tp_Validate::isEnglishWord("不是英文"));
	}
	public function testIsChinese() {
		$this->assertTrue(Tp_Validate::isChinese("这个是中文哦"));
		$this->assertFalse(Tp_Validate::isChinese("english"));
	}
	public function testIsSafePassword() {
		$this->assertTrue(Tp_Validate::isSafePassword("abcd123ABC"));
		$this->assertFalse(Tp_Validate::isSafePassword("12345"));
	}
	public function testIsSfzNum() {
		$this->assertTrue(Tp_Validate::isSfzNum("500235199210103333"));
		$this->assertFalse(Tp_Validate::isSfzNum("50023519921010353"));
	}
	public function testcheckSfzNum() {
		$this->assertNotEquals(false,Tp_Validate::checkSfzNum("500235199210103535"));
		$this->assertFalse(Tp_Validate::checkSfzNum("500235199210103434"));
	}
}