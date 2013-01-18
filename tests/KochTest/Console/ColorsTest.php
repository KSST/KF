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
        $this->assertEquals('✖', Colors::unicodeSymbol('big fail'));

        $this->assertEquals('\033[0;31m✖\033[0m', Colors::unicodeSymbol('big fail', array('red')));

        $this->assertEquals('\033[0;32m✔\033[0m', Colors::unicodeSymbol('big ok', array('green')));

        $this->assertEquals('\033[0;32;43m✖\033[0m', Colors::unicodeSymbol('big fail', array('green', 'yellow')));
    }

    /**
     * @covers Koch\Console\Colors::unicodeSymbol()
     * @expectedException InvalidArgumentException
     */
    public function testUnicodeSymbol_throwsException()
    {
        Colors::unicodeSymbol('not existant');
    }

    /**
     * @covers Koch\Console\Colors::write()
     */
    public function testWrite()
    {
        // only foreground color
        $this->assertEquals(
            '\033[0;31mOn a dark desert highway\033[0m',
            Colors::write('On a dark desert highway', 'red')
        );

        // foreground and background color
        $this->assertEquals(
            '\033[0;32;40mOn a dark desert highway\033[0m',
            Colors::write('On a dark desert highway', 'green', 'black')
        );

        // foreground, background and a modifier
       $this->assertEquals(
            '\033[0;32;40;1mOn a dark desert highway\033[0m',
            Colors::write('On a dark desert highway', 'green', 'black', 'bold')
        );

        // foreground, background and two modifiers
        $options = array('green', 'black', 'bold', 'underscore');
        $this->assertEquals(
            '\033[0;32;40;1mOn a dark desert highway\033[0m',
            Colors::write('On a dark desert highway', $options)
        );
    }

    /**
     * @covers Koch\Console\Colors::colorizePart()
     */
    public function testColorizePart()
    {
        // let's colorize only a defined part of a string
        $this->assertEquals(
            'On a \033[0;31mdark desert\033[0m highway',
            Colors::colorizePart('On a dark desert highway', 'dark desert', 'red')
        );
    }

    /**
     * @covers Koch\Console\Colors::setOptions()
     */
    public function testSetOptions()
    {
        $options = 'a,b,c,d';
        $array = Colors::setOptions($options);
        $expectedArray = array(0 => 'a',  1 => 'b',  2 => 'c',  3 => 'd');
        $this->assertEquals($array, $expectedArray);
    }

    /**
     * @covers Koch\Console\Colors::colorizeReturnValue()
     * @covers Koch\Console\Colors::unicodeSymbol()
     */
    public function testColorizeReturnValue()
    {
        $this->assertEquals('\033[0;32m✓\033[0m', Colors::colorizeReturnValue(1));
        $this->assertEquals('\033[0;31m✖\033[0m', Colors::colorizeReturnValue(0));
    }
}
