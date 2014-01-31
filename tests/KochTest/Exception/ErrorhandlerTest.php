<?php

namespace KochTest\Exception;

use Koch\Exception\Errorhandler;

class ErrorhandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testFormatBacktraceArgument()
    {
        // bool
        $backtraceArgument = true;
        $expected = Errorhandler::formatBacktraceArgument($backtraceArgument);
        $this->assertContains('true', $expected['arg']);
        $this->assertContains('bool', $expected['type']);

        // integer
        $backtraceArgument = 1;
        $expected = Errorhandler::formatBacktraceArgument($backtraceArgument);
        $this->assertContains('1', $expected['arg']);
        $this->assertContains('int', $expected['type']);

        // float
        $backtraceArgument = 1.1;
        $expected = Errorhandler::formatBacktraceArgument($backtraceArgument);
        $this->assertContains('1.1', $expected['arg']);
        $this->assertContains('float/double', $expected['type']);

        // string
        $backtraceArgument = 'Kraftwerk';
        $expected = Errorhandler::formatBacktraceArgument($backtraceArgument);
        $this->assertContains('Kraftwerk' , $expected['arg']);
        $this->assertContains('string', $expected['type']);

        // array
        $backtraceArgument = array('pizza', 'popcorn', 'coke');
        $expected = Errorhandler::formatBacktraceArgument($backtraceArgument);
        $this->assertContains('3', $expected['arg']);
        $this->assertContains('array', $expected['type']);

        // object
        $backtraceArgument = new \stdClass();
        $expected = Errorhandler::formatBacktraceArgument($backtraceArgument);
        $this->assertContains(get_class($backtraceArgument), $expected['arg']);
        $this->assertContains('object', $expected['type']);

        // resource
        /* $backtraceArgument = true;
        $expected = Errorhandler::formatBacktraceArgument($backtraceArgument);
        $this->assertContains(is_bool($expected['arg']));
        $this->assertContains('boolean', $expected['type']);

        // resource stream
        $backtraceArgument = true;
        $expected = Errorhandler::formatBacktraceArgument($backtraceArgument);
        $this->assertContains(is_bool($expected['arg']));
        $this->assertContains('boolean', $expected['type']); */

        // NULL
        $backtraceArgument = null;
        $expected = Errorhandler::formatBacktraceArgument($backtraceArgument);
        $this->assertEmpty($expected['arg']);
        $this->assertContains('null', $expected['type']);
    }
}
