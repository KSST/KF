<?php

namespace KochTest\Config\Adapter;

use Koch\Config\Adapter\INI;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class INITest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var INI
     */
    protected $object;

    public function setUp()
    {
        $this->object = new INI;

        vfsStreamWrapper::register();
        $this->configFileURL = vfsStream::url('root/config.ini');
        $this->booleanConfigFileURL = vfsStream::url('root/booleans.ini');
        $this->file1 = vfsStream::newFile('config.ini', 0777)->withContent($this->getConfigFileContent());
        $this->file2 = vfsStream::newFile('booleans.ini', 0777)->withContent($this->getBooleanConfigFileContent());
        $this->root = new vfsStreamDirectory('root');
        $this->root->addChild($this->file1);
        $this->root->addChild($this->file2);
        vfsStreamWrapper::setRoot($this->root);
    }

    public function tearDown()
    {
        unset($this->object);
    }

    /*
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage File not found: not-existant-file.ini
     */
    public function testReadConfigWithException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->object->readConfig('not-existant-file.ini');
    }

    /**
     * @covers Koch\Config\Adapter\INI::writeConfig
     */
    public function testWriteConfig()
    {
        $written = $this->object->writeConfig($this->configFileURL, $this->getConfigArray());
        $this->assertTrue($written);
        $this->assertEquals($this->getContentArray(), $this->object->readConfig($this->configFileURL));
    }

    public function testWriteConfigAddValuesToExistingConfig()
    {
        // write config
        $writtenOne = $this->object->writeConfig($this->configFileURL, $this->getConfigArray());
        $this->assertTrue($writtenOne);

        // now test appending to the just written config
        $writtenTwo = $this->object->writeConfig($this->configFileURL, array('newKey' => 'newValue'));
        $this->assertTrue($writtenTwo);
        $this->assertEquals($this->object->readConfig($this->configFileURL), $this->getConfigArrayOverloaded());
    }

    /**
     * @covers Koch\Config\Adapter\INI::writeConfig
     * @expectedException Koch\Exception\Exception
     * @expectedExceptionMessage Parameter $file is not given.
     */
    public function testWriteConfigFirstParameterGiven()
    {
        $this->object->writeConfig(null, array());
    }

    public function testReadingBooleanValues()
    {
        $config = $this->object->readConfig($this->booleanConfigFileURL);

        $this->assertTrue((bool) $config['booleans']['test_on']);
        $this->assertFalse((bool) $config['booleans']['test_off']);

        $this->assertTrue((bool) $config['booleans']['test_yes']);
        $this->assertFalse((bool) $config['booleans']['test_no']);

        $this->assertTrue((bool) $config['booleans']['test_true']);
        $this->assertFalse((bool) $config['booleans']['test_false']);

        $this->assertFalse((bool) $config['booleans']['test_null']);
    }

    public function testReadingWithoutSection()
    {
        $file = __DIR__ . '/../fixtures/no-section.ini';
        $config = $this->object->readConfig($file);

        $expected = array(
            'string_key' => 'string_value',
            'bool_key' => true
        );

        $this->assertEquals($expected, $config);
    }

    /**
     * @covers Koch\Config\Adapter\INI::writeConfig
     * @covers Koch\Config\Adapter\INI::readConfig
     */
    public function testReadConfig()
    {
        $this->object->writeConfig($this->configFileURL, $this->getConfigArray());

        $ini_array = $this->object->readConfig($this->configFileURL);

        $this->assertEquals($ini_array, $this->getConfigArray());

        $this->assertInternalType('string', $ini_array['section']['key3-int']);
    }

    public function getConfigFileContent()
    {
        return <<<EOF
; <?php die('Access forbidden.'); /* DO NOT MODIFY THIS LINE! ?>
;
; Koch Framework Configuration File :
; I:\0.Github\KSST\KF\tests\KochTest\Config\Adapter\..\fixtures\writeTest.ini
;
; This file was generated on 04-11-2012 19:37
;


;----------------------------------------
; section
;----------------------------------------
[section]
key1 = "value1"
key2 = "value2"
key3-int = "123"
newKey = "newValue"

; DO NOT REMOVE THIS LINE */ ?>
EOF;
    }

     public function getBooleanConfigFileContent()
    {
        return <<<EOF
[booleans]
test_on = on
test_off = off
test_yes = yes
test_no = no
test_true = true
test_false = false
test_null = null
EOF;
    }
    public function getConfigArray()
    {
        return array(
            'section' => array (
                'key1' => 'value1',
                'key2' => 'value2',
                'key3-int' => 123
        ));
    }

    public function getConfigArrayOverloaded()
    {
        return array (
            'section' => array (
                'key1' => 'value1',
                'key2' => 'value2',
                'key3-int' => 123,
                'newKey' => 'newValue'
            )
        );
    }
}
