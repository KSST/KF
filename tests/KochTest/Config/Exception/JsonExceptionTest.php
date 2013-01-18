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
     * expectedExceptionMessage JSON Error in file file.json. The json content from file was null.
     */
    public function testConstructor()
    {
        throw new JsonException('file.json');
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