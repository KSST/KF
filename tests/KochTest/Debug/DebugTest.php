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
<pre><b>Debugging <font color=red>DebugTest.php</font> on line <font color=red>64</font></b>:
<div style='background: #f5f5f5; padding: 0.2em 0em;'>        \Koch\Debug\Debug::printR($var, $var2, $string);
</div>
<b>Type</b>: array
<b>Length</b>: 1
<b>Value</b>: Array
(
    [Key] =&gt; Value
)
<b>Length</b>: 1
<b>Value</b>: Array
(
    [Key2] =&gt; Value2
)
<b>Length</b>: 13
<b>Value</b>: Just a string</pre>
EOD;

        $this->expectOutputString($expectedOutput);

        $var = array('Key' => 'Value');
        $var2 = array('Key2' => 'Value2');
        $string = 'Just a string';
        \Koch\Debug\Debug::printR($var, $var2, $string);
    }

    /**
     * @covers Koch\Debug\Debug::dump
     */
    public function testDump()
    {
/**
 * finally after 18 or something years, someone decided to add <pre> tags to var_dump()
 * in PHP 5.5.0alpha1! thats utterly impressive!
 * are you fucking serious? Come get some!
 */

// NOTE: this is NOWDOC instead of HEREDOC
// so its without parsing, because of the inlined $var
$expectedOutput = <<<'EOD'
Debugging DebugTest.php on line 92: \Koch\Debug\Debug::dump($var);
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
     * @covers Koch\Debug\Debug::getOriginOfDebugCall
     */
    public function testGetOriginOfDebugCall()
    {
// NOTE: this is NOWDOC instead of HEREDOC
// so its without parsing, because of the inlined $var
$expectedOutput = <<<'EOD'
Debugging DebugTest.php on line 109: \Koch\Debug\Debug::getOriginOfDebugCall(0);

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
     */
    public function testGetApplicationConstants()
    {
        $returnArray = true;
        $array = \Koch\Debug\Debug::getApplicationConstants($returnArray);

        $this->assertTrue(is_array($array));
    }

    /**
     * @covers Koch\Debug\Debug::getBacktrace
     */
    public function testGetBacktrace()
    {
        $returnArray = true;
        $limit = 1;
        $array = \Koch\Debug\Debug::getBacktrace($limit, $returnArray);

        $this->assertTrue(is_array($array));
    }

    /**
     * @covers Koch\Debug\Debug::getInterfaces
     */
    public function testGetInterfaces()
    {
       $returnArray = true;
        $array = \Koch\Debug\Debug::getInterfaces($returnArray);

        $this->assertTrue(is_array($array));
    }

    /**
     * @covers Koch\Debug\Debug::getClasses
     */
    public function testGetClasses()
    {
       $returnArray = true;
        $array = \Koch\Debug\Debug::getClasses($returnArray);

        $this->assertTrue(is_array($array));
    }

    /**
     * @covers Koch\Debug\Debug::getFunctions
     */
    public function testGetFunctions()
    {
        $returnArray = true;
        $array = \Koch\Debug\Debug::getFunctions($returnArray);

        $this->assertTrue(is_array($array));
    }

    /**
     * @covers Koch\Debug\Debug::getExtensions
     */
    public function testGetExtensions()
    {
        $returnArray = true;
        $array = \Koch\Debug\Debug::getExtensions($returnArray);

        $this->assertTrue(is_array($array));
    }

    /**
     * @covers Koch\Debug\Debug::getPhpIni
     */
    public function testGetPhpIni()
    {
        $returnArray = true;
        $array = \Koch\Debug\Debug::getPhpIni($returnArray);

        $this->assertTrue(is_array($array));
    }

    /**
     * @covers Koch\Debug\Debug::getWrappers
     */
    public function testGetWrappers()
    {
        $returnArray = true;
        $array = \Koch\Debug\Debug::getWrappers($returnArray);

        $this->assertTrue(is_array($array));
        $this->assertArrayHasKey('openssl', $array);
        $this->assertArrayHasKey('http', $array);
        $this->assertArrayHasKey('https', $array);
        $this->assertArrayHasKey('all', $array);
    }
}
