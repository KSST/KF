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
     * @covers Koch\Config\Adapter\XML::writeConfig
     */
    public function testWriteConfig()
    {
        $result = $this->object->writeConfig($this->configFileURL, $this->getConfigArray());
        $this->assertTrue($result);
    }

    /**
     * @covers Koch\Config\Adapter\XML::readConfig
     */
    public function testReadConfig()
    {
        $array = $this->object->readConfig($this->configFileURL);
        $this->assertEquals($array, $this->getConfigArray());
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
