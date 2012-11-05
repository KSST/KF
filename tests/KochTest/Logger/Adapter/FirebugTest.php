<?php

namespace KochTest\Logger\Adapter;

use Koch\Logger\Adapter\Firebug;

class FirebugTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Firebug
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new Firebug;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Logger\Adapter\Firebug::getFirePHPLoglevel
     */
    public function testGetFirePHPLoglevel()
    {
        $this->assertEquals('LOG', $this->object->getFirePHPLoglevel('log'));
        $this->assertEquals('LOG', $this->object->getFirePHPLoglevel('LOG'));
    }

    /**
     * @covers Koch\Logger\Adapter\Firebug::writeLog
     * @expectedException Exception
     * @expectedExceptionMessage Headers already sent
     */
    public function testWriteLog()
    {
        $data = array('message' => 'Message', 'label' => 'Label', 'level' => 'error');
        $this->object->writeLog($data);
        // @todo assert that headers are sent?
    }
}
