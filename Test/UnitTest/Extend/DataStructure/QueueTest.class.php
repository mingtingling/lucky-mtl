<?php
require_once(dirname(__FILE__).'/../../../../Core/PHPUnit.class.php');
class QueueTest extends Tp_PHPUnit {
	function __construct() {
		parent::__construct();
		import('Tp.Extend.DataStructure.Queue');		
	}
	public function testEnqueue() {
		$queue = new Tp_Queue();
		for($i = 0; $i < 10; $i ++) {
			$queue->enqueue('test');
			$this->assertEquals($i + 1,$queue->length());
		}
		$this->assertEquals(10,count($queue));
	}
	public function testClear() {
		$queue = new Tp_Queue();
		for($i = 0; $i < 10; $i ++) {
			$queue->enqueue('test');
		}
		$this->assertEquals(10,$queue->length());
		$queue->clear();
		$this->assertEquals(0,$queue->length());
	}
	public function testDequeue() {
		$queue = new Tp_Queue();
		for($i = 0; $i < 10; $i ++) {
			$queue->enqueue('test'.($i + 1));
		}
		for($i = 10; $i > 0; $i --) {
			$this->assertEquals($i,$queue->length());
			$data = $queue->dequeue();
			$this->assertEquals('test'.(11 - $i),$data);
		}
		$this->assertEquals(0,$queue->length());
		for($i = 0; $i < 10; $i ++) {
			$queue->enqueue('test');
		}
		for($i = 1; $i < 10; $i ++) {
			$queue->dequeue($i);
			for($j = 1; $j <= $i; $j ++) {
				$queue->enqueue('test');
			}
			$this->assertEquals(10,$queue->length());
		}
	}
	public function testIsEmpty() {
		$queue = new Tp_Queue();
		$this->assertTrue($queue->isEmpty());
		for($i = 0; $i < 10; $i ++) {
			$queue->enqueue('test');
			$this->assertFalse($queue->isEmpty());
		}
	}
}
