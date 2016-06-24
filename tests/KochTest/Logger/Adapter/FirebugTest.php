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
        $this->object = new Firebug();
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
        $loglevels = ['LOG', 'INFO', 'WARN', 'ERROR', 'TABLE', 'TRACE', 'DUMP'];
        foreach ($loglevels as $loglevel) {
            // lowercased
            $this->assertEquals($loglevel, $this->object->getFirePHPLoglevel(strtolower($loglevel)));
            // uppercased
            $this->assertEquals($loglevel, $this->object->getFirePHPLoglevel($loglevel));
        }

        // testing default loglevel
        $this->assertEquals('ERROR', $this->object->getFirePHPLoglevel());
    }

    /**
     * @covers Koch\Logger\Adapter\Firebug::Log
     * @expectedException Exception
     * @expectedExceptionMessage Headers already sent
     */
    public function testLog()
    {
        $level   = 'ERROR';
        $message = 'Error Message';
        $context = ['Yarp!', 'Some Content', 'Yarp! Yarp!'];

        $r = $this->object->log($level, $message, $context);

        $this->assertTrue($r);
    }
}
