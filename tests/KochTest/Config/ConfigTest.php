<?php

namespace KochTest\Config;

use Koch\Config\Config;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new Config;

        vfsStreamWrapper::register();

        $root = vfsStream::setup('root');

        $this->moduleConfigFileURL = vfsStream::url('root/module.config.php');
        $this->file = vfsStream::newFile('module.config.php', 0777)->at($root)
            ->withContent($this->getModuleConfigFileContent());

        $this->applicationConfigFileURL = vfsStream::url('root/application.config.php');
        $this->file2 = vfsStream::newFile('application.config.php', 0777)->at($root)
            ->withContent($this->getApplicationConfigFileContent());

        $this->root = new vfsStreamDirectory('root');
        $this->root->addChild($this->file);
        $this->root->addChild($this->file2);
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

    public function getModuleConfigFileContent()
    {
    return <<<EOF
<?php
// Module Configuration File generated by Koch Framework.
return array(
  "module" => "value"
);
EOF;
    }

    public function getApplicationConfigFileContent()
    {
    return <<<EOF
<?php
// Application Configuration File generated by Koch Framework.
return array(
  "app" => "value"
);
EOF;
    }

    /**
     * @covers Koch\Config\Config::read
     * @todo   Implement testread().
     */
    public function testread()
    {
        $config = $this->object->read($this->moduleConfigFileURL);

        $this->assertTrue(is_array($config));
    }

    /**
     * @covers Koch\Config\Config::readModuleConfig
     */
    public function testReadModuleConfig()
    {
        $config = $this->object->readModuleConfig('articles');

        $this->assertTrue(is_array($config));
    }

    /**
     * @covers Koch\Config\Config::writeModuleConfig
     */
    public function testWriteModuleConfig()
    {
        $array = array('module' => 'value');

        $this->assertTrue($this->object->writeModuleConfig($array, 'Articles'));
    }

    /**
     * @covers Koch\Config\Config::write
     */
    public function testwrite()
    {
        $file = 'test.config.php';
        $array = array('key' => 'value');

        $this->assertTrue($this->object->write($file, $array));

        if (is_file($file)) {
            unlink($file);
        }
    }

    /**
     * @covers Koch\Config\Config::write
     */
    public function testGetApplicationConfig()
    {
        $config = $this->object->getApplicationConfig();
        $this->assertTrue(is_array($config));
    }
}
