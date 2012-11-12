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
        # file 1
        $this->configFileAURL = vfsStream::url('root/errorlog.txt');
        $this->fileA = vfsStream::newFile('errorlog.txt', 0777);
        # file 2
        $this->configFileBURL = vfsStream::url('root/errorlog-full.txt');
        $this->fileB = vfsStream::newFile('errorlog-full.txt', 0777)->withContent($this->getFileContent());
        $this->root = new vfsStreamDirectory('root');
        $this->root->addChild($this->fileA);
        $this->root->addChild($this->fileB);
        vfsStreamWrapper::setRoot($this->root);

        $this->object->setErrorLogFilename($this->configFileAURL);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->object);
    }

    public function getFileContent()
    {
        return "Line1\nLine2\nLine3\nLine4\nLine5\nLine6\n";
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
        $file = $this->object->getErrorLogFilename();
        $this->assertNotEmpty($file);

        $this->object->setErrorLogFilename('ABC');
        $this->assertEquals('ABC', $this->object->getErrorLogFilename());
    }

    /**
     * @covers Koch\Logger\Adapter\File::getEntriesFromLogfile
     */
    public function testGetEntriesFromLogfile()
    {
        $e = $this->object->getEntriesFromLogfile('5');
        $this->assertEquals('<b>No Entries</b>', $e);

        $e = $this->object->getEntriesFromLogfile('5', $this->configFileBURL);

// @todo remove newline before closing span's
$out = <<<EOD
<span class="log-id">Entry 6</span><span class="log-entry">Line6
</span>
<span class="log-id">Entry 5</span><span class="log-entry">Line5
</span>
<span class="log-id">Entry 4</span><span class="log-entry">Line4
</span>
<span class="log-id">Entry 3</span><span class="log-entry">Line3
</span>
<span class="log-id">Entry 2</span><span class="log-entry">Line2
</span>

EOD;

        $this->assertEquals($out, $e);
    }
}
