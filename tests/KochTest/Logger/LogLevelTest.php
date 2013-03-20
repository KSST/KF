<?php

namespace KochTest\Logger;

use Koch\Logger\LogLevel;

class LogLevelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Koch\Logger\LogLevel::getLevelName
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Logging level "not-existant-level" is not defined, use one of:
     * 100, 200, 250, 300, 400, 500, 550, 600
     */
    public function testGetLevelName_throwsException()
    {
        LogLevel::getLevelName('not-existant-level');
    }

    /**
     * @covers Koch\Logger\LogLevel::getLevelName
     */
    public function testGetLevelName()
    {
        $name = LogLevel::getLevelName(LogLevel::INFO); // 200
        $this->assertEquals('INFO', $name);
    }
}
