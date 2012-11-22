<?php

namespace KochTest\Logger;

use Koch\Logger\Compositum;

class CompositumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Compositum
     */
    protected $object;

    public function setUp()
    {
        $this->object = new Compositum;
    }

    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Logger\Compositum::writeLog
     */
    public function testWriteLog()
    {
        // add one logger
        $logger = new \Koch\Logger\Adapter\File;
        $this->object->addLogger($logger);

        // setup message to log
        $message = 'TestMessage';
        $label = 'Info';
        $level = '1';

        // log message via array
        $data = array($message, $label, $level);
        $this->assertTrue($this->object->writeLog($data));

        // log message via parameters
        $this->object->writeLog($message . '2', $label, $level);
        $this->assertTrue($this->object->writeLog($data));
    }

    /**
     * @covers Koch\Logger\Compositum::addLogger
     * @covers Koch\Logger\Compositum::removeLogger
     */
    public function testAddLogger()
    {
        // add
        $firebug = new \Koch\Logger\Adapter\Firebug;
        $this->object->addLogger($firebug);
        $this->assertEquals($firebug, $this->object->loggers[0]);

        // remove
        $this->object->removeLogger('firebug');
        $this->assertEquals(array(), $this->object->loggers);
    }
}
