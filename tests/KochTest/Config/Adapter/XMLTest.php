<?php

namespace KochTest\Config\Adapter;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class XMLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var XML
     */
    protected $object;

    public function setUp()
    {
        $this->object = new \Koch\Config\Adapter\XML();

        vfsStreamWrapper::register();
        $this->configFileURL = vfsStream::url('root/config.xml');
        $this->file = vfsStream::newFile('config.xml', 0777)->withContent($this->getConfigFileContent());
        $this->root = new vfsStreamDirectory('root');
        $this->root->addChild($this->file);
        vfsStreamWrapper::setRoot($this->root);
    }

    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Config\Adapter\XML::write
     */
    public function testwrite()
    {
        $result = $this->object->write($this->configFileURL, $this->getConfigArray());
        $this->assertTrue($result);
    }

    /**
     * @covers Koch\Config\Adapter\XML::read
     */
    public function testread()
    {
        $array = $this->object->read($this->configFileURL);
        $this->assertEquals($array, $this->getConfigArray());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage XML File non-existant.xml not existing or not readable.
     */
    public function testread_throwsExceptionFileNotFound()
    {
        $this->object->read('non-existant.xml');
    }

    public function getConfigArray()
    {
        return array(
            'data' => array(
                'one' => "Content 1",
                'two' => array(
                    'three' => "Content 3",
                    'four' => "Content 4"
        )));
    }

    public function getConfigFileContent()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <data>
                 <one>Content 1</one>
                 <two>
                      <three>Content 3</three>
                      <four>Content 4</four>
                 </two>
            </data>';
    }
}
