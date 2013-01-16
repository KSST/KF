<?php

namespace KochTest\Config;

use Koch\Config\Exception\JsonException;

class JsonExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    /**
     * @expectedException Koch\Config\Exception\JsonException
     * @expectedExceptionMessage The json content from file was null.
     */
    public function testConstructor()
    {
        $filename = 'file.json';
        $json_error = JSON_ERROR_NULL;
        throw new JsonException($filename, $json_error);
    }

    /**
     * @covers Koch\Config\Exception\JsonException::getJsonErrorMessage
     */
    public function testgetJsonErrorMessage()
    {
        $errmsg = JsonException::getJsonErrorMessage(JSON_ERROR_DEPTH);
        $expected = 'The maximum stack depth has been exceeded.';
        $this->assertEquals($expected, $errmsg);
    }
}
