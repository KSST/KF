<?php

namespace KochTest\Logger;

use Koch\Logger\Compositum;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

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
     * @covers Koch\Logger\Compositum::Log
     */
    public function testLog()
    {
        // create a virtual error log file
        vfsStreamWrapper::register();
        $this->configFile = vfsStream::url('root/errorlog.txt');
        $this->file = vfsStream::newFile('errorlog.txt', 0777)->withContent('someContent');
        $this->root = new vfsStreamDirectory('root');
        $this->root->addChild($this->file);
        vfsStreamWrapper::setRoot($this->root);

        // add file logger and add virtual log file
        $logger = new \Koch\Logger\Adapter\File;
        $logger->setErrorLogFilename($this->configFile);
        $this->object->addLogger($logger);

        // setup message to log
        $message = 'TestMessage';
        $context = array('Context Info');
        $level = 'ERROR';

        $this->assertTrue($this->object->log($level, $message, $context));
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
