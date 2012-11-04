<?php
namespace KochTest\Config\Adapter;

use Koch\Config\Adapter\CSV;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class CSVTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CSV
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new CSV;

        vfsStreamWrapper::register();
        $this->configFileURL = vfsStream::url('root/config.csv');
        $this->file = vfsStream::newFile('config.csv', 0777)->withContent($this->getConfigFileContent());
        $this->root = new vfsStreamDirectory('root');
        $this->root->addChild($this->file);
        vfsStreamWrapper::setRoot($this->root);
    }

    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Config\Adapter\CSV::readConfig
     */
    public function testReadConfig()
    {
        $array = $this->object->readConfig($this->configFileURL);
        $this->assertEquals($array, $this->getConfigArray());
    }

    /**
     * @covers Koch\Config\Adapter\CSV::writeConfig
     */
    public function testWriteConfig()
    {
        $result = $this->object->writeConfig($this->configFileURL, $this->getConfigArray());
        $this->assertTrue($result);
    }

    public function getConfigFileContent()
    {
        return '1,2,3';
    }

    public function getConfigArray()
    {
        return array('1', '2', '3');
    }
}
