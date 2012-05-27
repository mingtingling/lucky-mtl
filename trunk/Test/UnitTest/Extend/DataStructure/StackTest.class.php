<?php
require_once(dirname(__FILE__).'/../../../../Core/PHPUnit.class.php');
class StackTest extends Tp_PHPUnit {
	function __construct() {
		parent::__construct();
		import('Tp.Extend.DataStructure.Stack');		
	}
	public function testPush() {
		$stack = new Tp_Stack();
		for($i = 0; $i < 10; $i ++) {
			$stack->push('test');
			$this->assertEquals($i + 1,$stack->length());
		}
		$this->assertEquals(10,count($stack));
	}
	public function testClear() {
		$stack = new Tp_Stack();
		for($i = 0; $i < 10; $i ++) {
			$stack->push('test');
		}
		$this->assertEquals(10,$stack->length());
		$stack->clear();
		$this->assertEquals(0,$stack->length());
	}
	public function testPop() {
		$stack = new Tp_Stack();
		for($i = 0; $i < 10; $i ++) {
			$stack->push('test'.($i + 1));
		}
		for($i = 10; $i > 0; $i --) {
			$this->assertEquals($i,$stack->length());
			$data = $stack->pop();
			$this->assertEquals('test'.$i,$data);
		}
		$this->assertEquals(0,$stack->length());
		for($i = 0; $i < 10; $i ++) {
			$stack->push('test');
		}
		for($i = 1; $i < 10; $i ++) {
			$stack->pop($i);
			for($j = 1; $j <= $i; $j ++) {
				$stack->push('test');
			}
			$this->assertEquals(10,$stack->length());
		}
	}
	public function testIsEmpty() {
		$stack = new Tp_Stack();
		$this->assertTrue($stack->isEmpty());
		for($i = 0; $i < 10; $i ++) {
			$stack->push('test');
			$this->assertFalse($stack->isEmpty());
		}
	}
}
