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
        $this->object = new File();

        vfsStreamWrapper::register();
        # file 1
        $this->configFileURL1 = vfsStream::url('root/errorlog.txt');
        $this->file1 = vfsStream::newFile('errorlog.txt', 0777);
        # file 2
        $this->configFileURL2 = vfsStream::url('root/errorlog-full.txt');
        $this->file2 = vfsStream::newFile('errorlog-full.txt', 0777)->withContent($this->getFileContent());
        $this->root = new vfsStreamDirectory('root');
        $this->root->addChild($this->file1);
        $this->root->addChild($this->file2);
        vfsStreamWrapper::setRoot($this->root);

        $this->object->setErrorLogFilename($this->configFileURL1);
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
     * @covers Koch\Logger\Adapter\File::log
     * @covers Koch\Logger\Adapter\File::readLog
     */
    public function testLog()
    {
        $level = 'ERROR';
        $message = 'String to log';
        $this->object->log($level, $message);

        $this->assertEquals($message, $this->object->readLog());
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

        $e = $this->object->getEntriesFromLogfile('5', $this->configFileURL2);

// @todo remove newline before closing span's
$expected = <<<EOD
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
        $this->assertEquals($expected, $e);

        $e = $this->object->getEntriesFromLogfile('5', 'not-existing-log.file');
        $this->assertEquals('<b>No Logfile found. No entries yet.</b>', $e);

    }
}
