<?php
namespace Koch\Form\Decorators\Form;

class FieldsetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Fieldset
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Fieldset;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Koch\Form\Decorators\Form\Fieldset::setLegend
     * @covers Koch\Form\Decorators\Form\Fieldset::getLegend
     */
    public function testSetLegend()
    {
        $legend = 'Legend-Test';
        $this->object->setLegend($legend);
        $this->assertEquals($legend, $this->object->getLegend());
    }

    /**
     * @covers Koch\Form\Decorators\Form\Fieldset::render
     */
    public function testRender()
    {
        $html_form_content = 'Form Content';
        $r = $this->object->render($html_form_content);

        $expectedOutput = '<fieldset class="form"><legend class="form"><em></em></legend>Form Content</fieldset>';

        $this->assertEquals($expectedOutput, $r);

    }
}
