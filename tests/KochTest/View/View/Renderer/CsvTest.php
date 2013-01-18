<?php

namespace KochTest\View\Renderer;

use Koch\View\Renderer\Csv;

class CsvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Csv
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Csv();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->object);
    }


    /**
     * @covers Koch\View\Renderer\Csv::__construct
     * @covers Koch\View\Renderer\Csv::getOptions
     */
    public function testConstructor()
    {
        $setOptions = array('key' => 'value');
        $this->object = new Csv($setOptions);

        $options = $this->object->getOptions();
        $this->assertEquals('value', $options['key']);
    }

    /**
     * @covers Koch\View\Renderer\Csv::initializeEngine
     */
    public function testInitializeEngine()
    {
        $this->assertNull($this->object->initializeEngine());
    }

    /**
     * @covers Koch\View\Renderer\Csv::configureEngine
     */
    public function testConfigureEngine()
    {
        $this->assertNull($this->object->configureEngine());
    }

    /**
     * @covers Koch\View\Renderer\Csv::render
     */
    public function testRender()
    {
        /* @todo use phpunit dataprovider */
        $headers = array('ABC', 'NUMS', 'QUOTATEDABC');

        $data = array(
            array('aaa', 'bbb', 'ccc', 'dddd'),
            array('123', '456', '789'),
            array('"aaa"', '"bbb"')
        );

        $this->object->assign($data, $headers);

        // template is in the case the output file to write to
        $file = 'output.csv';

        $result = $this->object->render($file);

        $this->assertTrue($result);
    }

    /**
     * @covers Koch\View\Renderer\Csv::render
     */
    public function testRender_withData()
    {
        $data = array(
            array('aaa', 'bbb', 'ccc', 'dddd'),
            array('123', '456', '789'),
            array('"aaa"', '"bbb"')
        );

        // template is in the case the output file to write to
        $file = 'output.csv';

        $result = $this->object->render($file, $data);

        $this->assertTrue($result);
    }

    /**
     * @covers Koch\View\Renderer\Csv::assign
     */
    public function testAssign()
    {
        /* @todo use phpunit dataprovider */
        $headers = array('ABC', 'NUMS', 'QUOTATEDABC');

        $data = array(
            array('aaa', 'bbb', 'ccc', 'dddd'),
            array('123', '456', '789'),
            array('"aaa"', '"bbb"')
        );

        $this->object->assign($data, $headers);

        $this->assertEquals($this->object->viewdata, $data);
        $this->assertEquals($this->object->headers, $headers);
    }

    /**
     * @covers Koch\View\Renderer\Csv::display
     */
    public function testDisplay()
    {
        /* @todo use phpunit dataprovider */
        $headers = array('ABC', 'NUMS', 'QUOTATEDABC');

        $data = array(
            array('aaa', 'bbb', 'ccc', 'dddd'),
            array('123', '456', '789'),
            array('"aaa"', '"bbb"')
        );

        $this->object->assign($data, $headers);

        $this->object->display('');

        $expectedString = <<< 'EOF'
ABC,NUMS,QUOTATEDABC
aaa,bbb,ccc,dddd
123,456,789
"""aaa""","""bbb"""

EOF;

        $this->expectOutputString($expectedString);
    }

    /**
     * @covers Koch\View\Renderer\Csv::fetch
     */
    public function testFetch()
    {
        /* @todo use phpunit dataprovider */
        $headers = array('ABC', 'NUMS', 'QUOTATEDABC');

        $data = array(
            array('aaa', 'bbb', 'ccc', 'dddd'),
            array('123', '456', '789'),
            array('"aaa"', '"bbb"')
        );

        $this->object->assign($data, $headers);

        $result = $this->object->fetch('');

       $expectedString = '123,456,789';

        $this->assertContains($expectedString, $result);
    }
}
