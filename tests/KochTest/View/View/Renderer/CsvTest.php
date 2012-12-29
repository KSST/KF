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
        $options = array();

        $this->object = new Csv($options);
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
     * @covers Koch\View\Renderer\Csv::initializeEngine
     * @todo   Implement testInitializeEngine().
     */
    public function testInitializeEngine()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\View\Renderer\Csv::configureEngine
     * @todo   Implement testConfigureEngine().
     */
    public function testConfigureEngine()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\View\Renderer\Csv::render
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
     * @covers Koch\View\Renderer\Csv::assign
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
     * @covers Koch\View\Renderer\Csv::display
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
     * @covers Koch\View\Renderer\Csv::fetch
     * @todo   Implement testFetch().
     */
    public function testFetch()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\View\Renderer\Csv::getEngine
     * @todo   Implement testGetEngine().
     */
    public function testGetEngine()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
