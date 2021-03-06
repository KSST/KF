<?php

namespace KochTest\Config\Adapter;

use Koch\Config\Adapter\YAML;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class YAMLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var YAML
     */
    protected $object;

    public function setUp()
    {
        $vendor = __DIR__ . '/../../../../';

        if ((extension_loaded('syck') === false) xor (is_file($vendor . '/spyc/Spyc.class.php') === true)) {
            $this->markTestSkipped(
                'This test requires a yaml reader, e.g. the PHP extension "yaml", "SYCK" or the vendor library "Spyc".'
            );
        }

        vfsStreamWrapper::register();
        $this->configFileURL = vfsStream::url('root/config.yml');
        $this->file          = vfsStream::newFile('config.yml', 0777)->withContent($this->getConfigFileContent());
        $this->root          = new vfsStreamDirectory('root');
        $this->root->addChild($this->file);
        vfsStreamWrapper::setRoot($this->root);

        $this->object = new YAML($this->configFileURL);
    }

    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Config\Adapter\YAML::write
     */
    public function testWrite()
    {
        $result = $this->object->write($this->configFileURL, $this->getConfigArray());
        $this->assertTrue($result);
    }

    /**
     * @covers Koch\Config\Adapter\YAML::read
     */
    public function testRead()
    {
        $array = $this->object->read($this->configFileURL);
        $this->assertEquals($array, $this->getConfigArray());
    }

    public function getConfigArray()
    {
        return [

        ];
    }

    public function getConfigFileContent()
    {
        return '--- # Favorite movies, block format
        - Casablanca
        - Spellbound
        - Notorious
        --- # Shopping list, inline format
        [milk, bread, eggs]';
    }
}
