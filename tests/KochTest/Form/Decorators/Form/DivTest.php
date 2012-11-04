<?php
namespace Koch\Form\Decorators\Form;

class DivTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Div
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Div;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Koch\Form\Decorators\Form\Div::render
     */
    public function testRender()
    {
        $this->object->setClass('testCssClass');
        $this->object->setId('testID');

        $result = $this->object->render('formcontent');

        $this->assertEquals(CR.'<div class="testCssClass" id="testID">formcontent</div>'.CR, $result);
    }
}
