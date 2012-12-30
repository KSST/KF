<?php

namespace KochTest\Console;

use Koch\Console\Colors;

class ColorsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Colors
     */
    protected $object;

    public function setUp()
    {
        $this->object = new Colors;
    }

    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Console\Colors::unicodeSymbol()
     */
    public function testUnicodeSymbol()
    {
        $this->assertEquals('\342\234\227', Colors::unicodeSymbol('x'));
    }

    /**
     * @covers Koch\Console\Colors::write()
     */
    public function testWrite()
    {
        $out = Colors::write('On a dark desert highway', 'red');

        $this->assertEquals('', $out);
    }

    /**
     * @covers Koch\Console\Colors::colorize()
     */
    public function testColorize()
    {
        $out = Colors::colorize('On a dark desert highway', 'dark desert', 'red');

        $this->assertEquals('', $out);
    }
}
