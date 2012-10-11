<?php

namespace KochTest\Config\Adapter;

use Koch\Config\Adapter\INI;

class INITest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var INI
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new INI;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->object);
    }

    public function getIniArray()
    {
        return array(
            'section' => array (
                'key1' => 'value1',
                'key2' => 'value2',
                'key3-int' => 123
        ));
    }

    public function getFile()
    {
        return dirname(__DIR__) . '/fixtures/writeTest.ini';
    }

    /*
     * @expectedException        Exception
     * @expectedExceptionMessage File not found
     */
    public function testReadConfigWithException()
    {
        $this->setExpectedException('Exception');

        $this->object->readConfig('not-existant-file.ini');
    }

    /**
     * @covers Koch\Config\Adapter\INI::writeConfig
     */
    public function testWriteConfig()
    {
        $ini_array = $this->object->writeConfig($this->getFile(), $this->getIniArray());

        $this->assertEquals($ini_array, $this->getIniArray());

        unlink($this->getFile());
    }

    /**
     * @covers Koch\Config\Adapter\INI::writeConfig
     *
     * @expectedException PHPUnit_Framework_Error
     * @expectedException FileNotFound
     */
    public function testWriteConfigSecondParameterMustBeArray()
    {
        // from "array" type hint
        $this->object->writeConfig($this->getFile(), 'string');
    }

    public function testReadingBooleanValues()
    {
        $file = dirname(__DIR__) . '/fixtures/booleans.ini';
        $config = $this->object->readConfig($file);

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
        $file = dirname(__DIR__) . '/fixtures/no-section.ini';
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
        $this->object->writeConfig($this->getFile(), $this->getIniArray());

        $ini_array = $this->object->readConfig($this->getFile());

        $this->assertEquals($ini_array, $this->getIniArray());

        $this->assertInternalType('integer', $ini_array['section']['key3-int']);
    }
}
