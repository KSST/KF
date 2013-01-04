<?php

namespace KochTest\Config\Adapter;

use Koch\Config\Adapter\Native;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class NativeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Native
     */
    protected $object;

    public function setUp()
    {
        $this->object = new Native;

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

    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Config\Adapter\Native::readConfig
     */
    public function testReadConfig()
    {
        $array = $this->object->readConfig($this->configFileURL);
        $this->assertEquals($array, $this->getConfigArray());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The config file "non-existant.file" is not existing or not readable.
     */
    public function testReadConfig_throwsExceptionFileNotFound()
    {
        $this->object->readConfig('non-existant.file');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage The config file "vfs://root/config2.php" does not contain a PHP array.
     */
    public function testReadConfig_throwsExceptionIfFileContentNotArray()
    {
        $this->object->readConfig($this->configFileURL2);
    }

    /**
     * @covers Koch\Config\Adapter\Native::writeConfig
     */
    public function testWriteConfig()
    {
        $result = $this->object->writeConfig($this->configFileURL, $this->getConfigArray());
        $this->assertTrue($result);
    }

    public function getConfigFileContent()
    {
        return <<<EOF
<?php
// Configuration File generated by Koch Framework.
return array(
  "test" => "value"
);
EOF;
    }

    public function getConfigArray()
    {
        return array(
            'test' => 'value'
        );
    }
}
