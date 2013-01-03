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
        $this->configFileURL = vfsStream::url('root/config.php');
        $this->file = vfsStream::newFile('config.php', 0777)->withContent($this->getConfigFileContent());

        $this->configFileURL2 = vfsStream::url('root/config2.php');
        $this->file2 = vfsStream::newFile('config2.php', 0777)->withContent('');

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

    /**
     * @covers Koch\Config\Config::readConfig
     * @todo   Implement testReadConfig().
     */
    public function testReadConfig()
    {
        $file = $a;
        $config = $this->object->readConfig($file);

        $this->assertTrue(is_array($array));
    }

    /**
     * @covers Koch\Config\Config::readModuleConfig
     * @todo   Implement testReadModuleConfig().
     */
    public function testReadModuleConfig()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Config\Config::writeModuleConfig
     * @todo   Implement testWriteModuleConfig().
     */
    public function testWriteModuleConfig()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Config\Config::writeConfig
     * @todo   Implement testWriteConfig().
     */
    public function testWriteConfig()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
