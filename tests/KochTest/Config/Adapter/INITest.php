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
        $this->object = new INI();

        vfsStreamWrapper::register();

        $this->configFileURL = vfsStream::url('root/config.ini');
        $this->file          = vfsStream::newFile('config.ini', 0777)->withContent($this->getConfigFileContent());

        $this->booleanConfigFileURL = vfsStream::url('root/booleans.ini');
        $this->fileB                = vfsStream::newFile('booleans.ini', 0777)->withContent($this->getBooleanConfigFileContent());

        $this->root = new vfsStreamDirectory('root');
        $this->root->addChild($this->file);
        $this->root->addChild($this->fileB);
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
    public function testreadWithException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->object->read('not-existant-file.ini');
    }

    /**
     * @covers Koch\Config\Adapter\INI::read
     */
    public function testRead()
    {
        $array = $this->object->read($this->configFileURL);
        $this->assertEquals($array, $this->getConfigArray());
    }

    /**
     * @covers Koch\Config\Adapter\INI::write
     */
    public function testWrite()
    {
        $result = $this->object->write($this->configFileURL, $this->getConfigArray());
        $this->assertTrue($result);
    }

    public function testwriteAddValuesToExistingConfig()
    {
        // test appending to the just written config
        $writtenTwo = $this->object->write($this->configFileURL, ['newKey' => 'newValue']);
        $this->assertTrue($writtenTwo);
        $this->assertEquals($this->object->read($this->configFileURL), $this->getConfigArrayOverloaded());
    }

    /**
     * @covers Koch\Config\Adapter\INI::write
     * @expectedException Koch\Exception\Exception
     * @expectedExceptionMessage Parameter $file is not given.
     */
    public function testWriteFirstParameterGiven()
    {
        $this->object->write(null, []);
    }

    public function testReadingBooleanValues()
    {
        $config = $this->object->read($this->booleanConfigFileURL);

        $this->assertTrue((bool) $config['booleans']['test_on']);
        $this->assertFalse((bool) $config['booleans']['test_off']);
        $this->assertTrue((bool) $config['booleans']['test_yes']);
        $this->assertFalse((bool) $config['booleans']['test_no']);
        $this->assertTrue((bool) $config['booleans']['test_true']);
        $this->assertFalse((bool) $config['booleans']['test_false']);
        $this->assertFalse((bool) $config['booleans']['test_null']);
        $this->assertTrue((bool) $config['booleans']['test_numeric_on']);
        $this->assertFalse((bool) $config['booleans']['test_numeric_off']);
    }

    public function testReadingWithoutSection()
    {
        $this->fileCURL = vfsStream::url('root/no-section.ini');
        $this->fileC    = vfsStream::newFile('no-section.ini', 0777)->withContent(
            'string_key = string_value
             bool_key = 1'
        );

        $this->root = new vfsStreamDirectory('root');
        $this->root->addChild($this->fileC);
        vfsStreamWrapper::setRoot($this->root);

        $config = $this->object->read($this->fileCURL);

        $expected = [
            'string_key' => 'string_value',
            'bool_key'   => true,
        ];

        $this->assertEquals($expected, $config);
    }

    public function getConfigFileContent()
    {
        return <<<EOF
; <?php die('Access forbidden.'); /* DO NOT MODIFY THIS LINE! ?>
;
; Koch Framework Configuration File :
; writeTest.ini
;
; This file was generated on 04-11-2012 19:37
;


;----------------------------------------
; section
;----------------------------------------
[section]
key1 = "value1"
key2 = "value2"
key3-int = 123

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
test_numeric_on = 1
test_numeric_off = 0
EOF;
    }

    public function getConfigArray()
    {
        return [
            'section' => [
                'key1'     => 'value1',
                'key2'     => 'value2',
                'key3-int' => 123,
        ], ];
    }

    public function getConfigArrayOverloaded()
    {
        return  [
            'section' => [
                'key1'     => 'value1',
                'key2'     => 'value2',
                'key3-int' => 123,
                'newKey'   => 'newValue',
            ],
        ];
    }
}
