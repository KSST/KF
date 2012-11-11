<?php

namespace KochTest\Logger\Adapter;

use Koch\Logger\Adapter\File;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var File
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new File($config);

        vfsStreamWrapper::register();
        $this->configFileURL = vfsStream::url('root/file.txt');
        $this->file = vfsStream::newFile('file.txt', 0777);//->withContent($this->getConfigFileContent());
        $this->root = new vfsStreamDirectory('root');
        $this->root->addChild($this->file);
        vfsStreamWrapper::setRoot($this->root);
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
     * @covers Koch\Logger\Adapter\File::writeLog
     * @covers Koch\Logger\Adapter\File::readLog
     */
    public function testWriteLog()
    {
        $string = 'String to log';
        $this->object->writeLog($string);

        $this->assertEquals($string, $this->object->readLog());
    }

      /**
     * @covers Koch\Logger\Adapter\File::getErrorLogFilename
     * @covers Koch\Logger\Adapter\File::setErrorLogFilename
     */
    public function testGetErrorLogFilename()
    {
        $this->assertContains('errorlog', $this->object->getErrorLogFilename());

        $this->object->setErrorLogFilename('ABC');
        $this->assertEquals('ABC', $this->object->getErrorLogFilename());
    }

    /**
     * @covers Koch\Logger\Adapter\File::getEntriesFromLogfile
     */
    public function testGetEntriesFromLogfile()
    {
        $e = $this->object->getEntriesFromLogfile('5');
        $this->assertEquals('', $e);;
    }
}
