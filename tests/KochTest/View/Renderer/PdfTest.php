<?php

namespace KochTest\View\Renderer;

use Koch\View\Renderer\Pdf;

class PdfTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Pdf
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (class_exists('mPDF', true) === false) {
            $this->markTestSkipped('This test requires the vendor library "mPDF".');
        }

        $options = array();

        $this->object = new Pdf($options);
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
     * @covers Koch\View\Renderer\Pdf::initializeEngine
     */
    public function testInitializeEngine()
    {
        $this->assertNull($this->object->initializeEngine());
    }

    /**
     * @covers Koch\View\Renderer\Pdf::configureEngine
     */
    public function testConfigureEngine()
    {
        $this->assertNull($this->object->configureEngine());
    }

    /**
     * @covers Koch\View\Renderer\Pdf::render
     * @todo   Implement testRender().
     */
    public function testRender()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\View\Renderer\Pdf::assign
     * @todo   Implement testAssign().
     */
    public function testAssign()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\View\Renderer\Pdf::display
     * @todo   Implement testDisplay().
     */
    public function testDisplay()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\View\Renderer\Pdf::fetch
     * @todo   Implement testFetch().
     */
    public function testFetch()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
