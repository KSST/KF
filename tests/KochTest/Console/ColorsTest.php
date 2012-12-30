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

        $this->assertEquals('\033[0;31m\342\234\227\033[0m', Colors::unicodeSymbol('x', 'red'));

        $this->assertEquals('\342\234\223', Colors::unicodeSymbol('check', 'green'));

        $this->assertEquals('\342\234\227', Colors::unicodeSymbol('x', array('green', 'black')));
    }

    /**
     * @covers Koch\Console\Colors::write()
     */
    public function testWrite()
    {
        $out = Colors::write('On a dark desert highway', 'red');
        echo $out;
        $this->assertEquals('\033[0;31mOn a dark desert highway\033[0m', $out);

        $out = Colors::write('On a dark desert highway', 'green', 'black');
        echo $out;

        $out = Colors::write('On a dark desert highway', 'green', 'black', 'bold');
        echo $out;

        $out = Colors::write('On a dark desert highway', array('green', 'black', 'bold', 'underscore'));
        echo $out;

        $this->assertEquals("\033[32;40;1;4mOn a dark desert highway\033[0m", $out);
    }

    /**
     * @covers Koch\Console\Colors::colorize()
     */
    public function testColorize()
    {
        $out = Colors::colorize('On a dark desert highway', 'dark desert', 'red');

        echo $out;

        $this->assertEquals('On a \033[0;31mdark desert\033[0m highway', $out);
    }
}
