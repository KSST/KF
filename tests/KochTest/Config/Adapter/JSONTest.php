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
    public function testreadThrowsExceptionFileNotFound()
    {
        $this->setExpectedException('Koch\Exception\Exception');
        $this->object->read('not-existant-file.json');
    }

    /**
     * @covers Koch\Config\Adapter\JSON::read
     * @expectedException Koch\Config\Exception\JsonException
     */
    public function testreadThrowsExceptionJsonError()
    {
        $this->object->read($this->invalidConfigFileURL);
    }

    /**
     * @covers Koch\Config\Adapter\JSON::read
     */
    public function testread()
    {
        $array = $this->object->read($this->configFileURL);
        $this->assertEquals($array, $this->getConfigArray());
    }

    /**
     * @covers Koch\Config\Adapter\JSON::write
     */
    public function testwrite()
    {
        $result = $this->object->write($this->configFileURL, $this->getConfigArray());
        $this->assertTrue($result);
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
