<?php
//require_once 'PHPUnit/Framework.php';
 
class TableCacheTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $this->setExpectedException('InvalidArgumentException');
    }
}
