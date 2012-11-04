<?php
namespace KochTest\Config\Adapter;

use Koch\Config\Adapter\JSON;

class JSONTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JSON
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new JSON;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->object);
    }

    public function getFile()
    {
        return __DIR__ . '/../fixtures/file.json';
    }

    /*
     * @expectedExceptiom        Koch\Exception\Exception
     * @expectedExceptionMessage JSON Config File not existing or not readable.
     */
    public function testReadConfigThrowsExceptionFileNotFound()
    {
        $this->setExpectedException('Koch\Exception\Exception');
        $this->object->readConfig('not-existant-file.json');
    }

    /**
     * @covers Koch\Config\Adapter\JSON::readConfig
     * @expectedException Koch\Exception\Exception
     * @expectedExceptionMessage
     * JSON Error in file I:\0.Github\KSST\KF\tests\KochTest\Config\Adapter/../fixtures/invalid.json - Syntax Error.
     */
    public function testReadConfigThrowsExceptionJsonError()
    {
        $this->object->readConfig(__DIR__ . '/../fixtures/invalid.json');
    }

    /**
     * @covers Koch\Config\Adapter\JSON::readConfig
     */
    public function testReadConfig()
    {
        $json = $this->object->readConfig(__DIR__ . '/../fixtures/file.json');
        $expected = array('section-1' => array('key1' => 'value1'));
        $this->assertEquals($expected, $json);
    }

    /**
     * @covers Koch\Config\Adapter\JSON::writeConfig
     */
    public function testWriteConfig()
    {
        $array = array('section-1' => array('key1' => 'value1'));
        $file = __DIR__ . '/../fixtures/writeConfig.json';

        $int_or_bool = $this->object->writeConfig($file, $array);

        $this->assertTrue((bool) $int_or_bool);

        unlink($file);
    }

    /**
     * @covers Koch\Config\Adapter\JSON::getJsonErrorMessage
     */
    public function testgetJsonErrorMessage()
    {
        $errmsg = $this->object->getJsonErrorMessage(JSON_ERROR_DEPTH);
        $expected = 'The maximum stack depth has been exceeded.';
        $this->assertEquals($expected, $errmsg);
    }
}
