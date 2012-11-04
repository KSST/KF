<?php
namespace KochTest\Config\Adapter;

use Koch\Config\Adapter\JSON;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class JSONTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JSON
     */
    protected $object;

    public function setUp()
    {
        $this->object = new JSON;

        vfsStreamWrapper::register();
        $this->configFileURL = vfsStream::url('root/config.json');
        $this->invalidConfigFileURL = vfsStream::url('root/invalid-config.json');
        $this->file = vfsStream::newFile('config.json', 0777)->withContent($this->getConfigFileContent());
        $this->invalidFile = vfsStream::newFile('invalid-config.json', 0777)->withContent($this->getInvalidConfigFileContent());
        $this->root = new vfsStreamDirectory('root');
        $this->root->addChild($this->file);
        $this->root->addChild($this->invalidFile);
        vfsStreamWrapper::setRoot($this->root);
    }

    public function tearDown()
    {
        unset($this->object);
    }

    /*
     * @expectedExceptiom        Koch\Exception\Exception
     * @expectedExceptionMessage JSON Config File not existing or not readable.
     */
    public function testReadConfigThrowsExceptionFileNotFound()
    {
        $this->setExpectedException('Koch\Exception\Exception');
        $this->object->readConfig('not-existant-file.json');
    }

    /**
     * @covers Koch\Config\Adapter\JSON::readConfig
     * @expectedException Koch\Exception\Exception
     */
    public function testReadConfigThrowsExceptionJsonError()
    {
        $this->object->readConfig($this->invalidConfigFileURL);
    }

    /**
     * @covers Koch\Config\Adapter\JSON::readConfig
     */
    public function testReadConfig()
    {
        $array = $this->object->readConfig($this->configFileURL);
        $this->assertEquals($array, $this->getConfigArray());
    }

    /**
     * @covers Koch\Config\Adapter\JSON::writeConfig
     */
    public function testWriteConfig()
    {
        $result = $this->object->writeConfig($this->configFileURL, $this->getConfigArray());
        $this->assertTrue($result);
    }

    /**
     * @covers Koch\Config\Adapter\JSON::getJsonErrorMessage
     */
    public function testgetJsonErrorMessage()
    {
        $errmsg = $this->object->getJsonErrorMessage(JSON_ERROR_DEPTH);
        $expected = 'The maximum stack depth has been exceeded.';
        $this->assertEquals($expected, $errmsg);
    }

    public function getConfigFileContent()
    {
        return '{
            "section-1" : {
                "key1": "value1"
            }
        }';
    }

    public function getConfigArray()
    {
        return array('section-1' => array('key1' => 'value1'));
    }

    public function getInvalidConfigFileContent()
    {
        return '{
            "section-1" :
                "key1" "value1"
            }
        }';
    }
}
