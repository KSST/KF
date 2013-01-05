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
$expectedOutput = <<<'EOD'
<pre><b>Debugging<font color=red>DebugTest.php</font> on line <font color=red>66</font></b>:
<div style='background: #f5f5f5; padding: 0.2em 0em;'>        \Koch\Debug\Debug::printR($var, $var2);
</div>
<b>Type</b>: array
<b>Length</b>: 2
<b>Value</b>: Array
(
    [0] =&gt; Array
        (
            [Key] =&gt; Value
        )

    [1] =&gt; Array
        (
            [Key2] =&gt; Value2
        )

)
</pre>
EOD;

        $this->expectOutputString($expectedOutput);

        $var = array('Key' => 'Value');
        $var2 = array('Key2' => 'Value2');
        \Koch\Debug\Debug::printR($var, $var2);
    }

    /**
     * @covers Koch\Debug\Debug::dump
     */
    public function testDump()
    {
// NOTE: this is NOWDOC instead of HEREDOC
// so its without parsing, because of the inlined $var
$expectedOutput = <<<'EOD'
Debugging DebugTest.php on line 88: \Koch\Debug\Debug::dump($var);
array(1) {
  'Key' =>
  string(5) "Value"
}

EOD;

       $this->expectOutputString($expectedOutput);

       $var = array('Key' => 'Value');
       \Koch\Debug\Debug::dump($var);
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
     */
    public function testGetOriginOfDebugCall()
    {
// NOTE: this is NOWDOC instead of HEREDOC
// so its without parsing, because of the inlined $var
$expectedOutput = <<<'EOD'
Debugging DebugTest.php on line 117: \Koch\Debug\Debug::getOriginOfDebugCall(0);

EOD;
        $this->expectOutputString($expectedOutput);

        $_SERVER['REMOTE_ADDR'] = null;
        \Koch\Debug\Debug::getOriginOfDebugCall(0);
    }

    /**
     * @covers Koch\Debug\Debug::getIncludedFiles
     */
    public function testGetIncludedFiles()
    {
        $returnArray = true;
        $array = \Koch\Debug\Debug::getIncludedFiles($returnArray);

        $this->assertTrue(is_array($array));
        $this->assertArrayHasKey('count', $array);
        $this->assertArrayHasKey('size', $array);
        $this->assertArrayHasKey('files', $array);

        $this->assertTrue(is_array($array['files']));
        $this->assertTrue(is_array($array['files'][0]));
        $this->assertArrayHasKey('name', $array['files'][0]);
        $this->assertArrayHasKey('size', $array['files'][0]);
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
     */
    public function testGetWrappers()
    {
        $returnArray = true;
        $array = \Koch\Debug\Debug::getWrappers($returnArray);
        var_dump($array);

        $this->assertTrue(is_array($array));
        $this->assertArrayHasKey('openssl', $array);
        $this->assertArrayHasKey('http', $array);
        $this->assertArrayHasKey('https', $array);
        $this->assertArrayHasKey('all', $array);
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
