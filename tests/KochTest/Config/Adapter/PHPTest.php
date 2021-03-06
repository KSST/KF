<?php

namespace KochTest\Config\Adapter;

use Koch\Config\Adapter\PHP;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class PHPTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHP
     */
    protected $object;

    public function setUp()
    {
        $this->object = new PHP();

        vfsStreamWrapper::register();
        $this->configFileURL = vfsStream::url('root/config.php');
        $this->file          = vfsStream::newFile('config.php', 0777)->withContent($this->getConfigFileContent());

        $this->configFileURL2 = vfsStream::url('root/config2.php');
        $this->file2          = vfsStream::newFile('config2.php', 0777)->withContent('');

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
     * @covers Koch\Config\Adapter\PHP::read
     */
    public function testread()
    {
        $array = $this->object->read($this->configFileURL);
        $this->assertEquals($array, $this->getConfigArray());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The config file "non-existant.file" is not existing or not readable.
     */
    public function testreadThrowsExceptionFileNotFound()
    {
        $this->object->read('non-existant.file');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage The config file "vfs://root/config2.php" does not contain a PHP array.
     */
    public function testreadThrowsExceptionIfFileContentNotArray()
    {
        $this->object->read($this->configFileURL2);
    }

    /**
     * @covers Koch\Config\Adapter\PHP::write
     */
    public function testwrite()
    {
        $result = $this->object->write($this->configFileURL, $this->getConfigArray());
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
        return [
            'test' => 'value',
        ];
    }
}
