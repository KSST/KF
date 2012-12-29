<?php

namespace KochTest\Debug;

use Koch\Debug\Debug;

class DebugTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Debug
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new Debug;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Debug\Debug::printR
     */
    public function testPrintR()
    {

// NOTE: this is NOWDOC instead of HEREDOC
// so its without parsing, because of the inlined $var
$printR_output = <<<'EOD'
<pre><b>Debugging<font color=red>DebugTest.php</font> on line <font color=red>54</font></b>:
<div style='background: #f5f5f5; padding: 0.2em 0em;'>        \Koch\Debug\Debug::printR($var);
</div>
<b>Type</b>: array
<b>Length</b>: 1
<b>Value</b>: Array
(
    [Key] =&gt; Value
)
</pre>
EOD;

        $this->expectOutputString($printR_output);

        $var = array('Key' => 'Value');
        \Koch\Debug\Debug::printR($var);
    }

    /**
     * @covers Koch\Debug\Debug::dump
     * @todo   Implement testDump().
     */
    public function testDump()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Debug\Debug::firebug
     * @todo   Implement testFirebug().
     */
    public function testFirebug()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Debug\Debug::getOriginOfDebugCall
     * @todo   Implement testGetOriginOfDebugCall().
     */
    public function testGetOriginOfDebugCall()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Debug\Debug::getIncludedFiles
     * @todo   Implement testGetIncludedFiles().
     */
    public function testGetIncludedFiles()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Debug\Debug::getApplicationConstants
     * @todo   Implement testGetApplicationConstants().
     */
    public function testGetApplicationConstants()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Debug\Debug::getBacktrace
     * @todo   Implement testGetBacktrace().
     */
    public function testGetBacktrace()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Debug\Debug::getInterfaces
     * @todo   Implement testGetInterfaces().
     */
    public function testGetInterfaces()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Debug\Debug::getClasses
     * @todo   Implement testGetClasses().
     */
    public function testGetClasses()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Debug\Debug::getFunctions
     * @todo   Implement testGetFunctions().
     */
    public function testGetFunctions()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Debug\Debug::getExtensions
     * @todo   Implement testGetExtensions().
     */
    public function testGetExtensions()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Debug\Debug::getPhpIni
     * @todo   Implement testGetPhpIni().
     */
    public function testGetPhpIni()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Debug\Debug::getWrappers
     * @todo   Implement testGetWrappers().
     */
    public function testGetWrappers()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Debug\Debug::getRegisteredEventListeners
     * @todo   Implement testGetRegisteredEventListeners().
     */
    public function testGetRegisteredEventListeners()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
