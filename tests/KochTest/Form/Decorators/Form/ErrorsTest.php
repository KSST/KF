<?php

namespace KochTest\Form\Decorators\Form;

use Koch\Form\Decorators\Form\Errors;

class ErrorsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Errors
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new Errors();

        // create new form
        $form = new \Koch\Form\Form('my_form');
        $form->addElement('textarea');

        // set form to object
        $this->object->setForm($form);

        // set some error messages
        $this->object->getForm()->addErrorMessage('Message 1');
        $this->object->getForm()->addErrorMessage('Message 2');
    }

    protected function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Form\Decorators\Form\Errors::render
     */
    public function testRender()
    {
        $html_form_content = 'FORM';

        $r = $this->object->render($html_form_content);

        $e = '<ul id="form-errors"><li>Message 1</li><li>Message 2</li></ul>FORM';

        $this->assertEquals($e, $r);
    }
}
