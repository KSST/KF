<?php

namespace KochTest\Form;

use Koch\Form\Form;
use Koch\Form\Elements;

/**
 * @todo method chaining tests on all setter methods
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->form = new Form('TestForm');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->form);
    }

    /**
     * @covers Koch\Form\Form::__construct
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage
     */
    public function testConstructorThrowsExceptionWhenFirstArgumentMissing()
    {
        $this->form = new Form();
    }

    /**
     * @covers Koch\Form\Form::__construct
     */
    public function testConstructorArgsAreSet()
    {
        $this->form = new Form('Form', 'GET', 'someActionName');

        $this->assertEquals('GET', $this->form->getMethod());
        if (defined('REWRITE_ENGINE_ON') and REWRITE_ENGINE_ON) {
            $expectedURL = WWW_ROOT . 'someActionName';
        } else {
            $expectedURL = WWW_ROOT . 'index.php?mod=someActionName';
        }
        $this->assertEquals($expectedURL, $this->form->getAction());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The method parameter is "abc", but has to be GET or POST.
     */
    public function testSetMethodThrowsInvalidArgumentException()
    {
        $this->form->setMethod('abc');
    }

    public function testSetMethod()
    {
        $this->form->setMethod('GET');

        // via getter
        $this->assertNotEquals('get', $this->form->getMethod());
        $this->assertEquals('GET', $this->form->getMethod());
        // via property
        $this->assertEquals('GET', $this->form->method);
    }

    public function testGetMethod()
    {
        // defaults to POST
        $this->assertEquals('POST', $this->form->getMethod());

        $this->form->setMethod('GET');
        // via getter
        $this->assertEquals('GET', $this->form->getMethod());
    }

    public function testSetAction()
    {
        // set internal url - rebuilds the external url via router
        $this->form->setAction('/news/show');
        if (defined('REWRITE_ENGINE_ON') and REWRITE_ENGINE_ON) {
            $expectedURL = WWW_ROOT . 'news/show';
        } else {
            $expectedURL = WWW_ROOT . 'index.php?mod=news&amp;ctrl=show';
        }
        $this->assertEquals($expectedURL, $this->form->getAction());

        // set external url
        $this->form->setAction(WWW_ROOT .'index.php?mod=news&action=show');
        $this->assertEquals( WWW_ROOT . 'index.php?mod=news&action=show', $this->form->getAction());

        // set external url withput www_root (http root)
        $this->form->setAction('index.php?mod=news&action=show');
        $this->assertEquals( WWW_ROOT . 'index.php?mod=news&action=show', $this->form->getAction());
    }

    public function testGetAction()
    {
        // via getter - qualified url
        $url = WWW_ROOT . 'index.php?mod=news&action=show';
        $this->form->setAction( $url );
        $this->assertEquals( $url, $this->form->getAction());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The target parameter is "abc", but has to be one of _blank, _self, _parent, _top.
     */
    public function testMethod_setTargetThrowsException()
    {
        $this->form->setTarget('abc');
    }

    /**
     * @covers Koch\Form\Form::getTarget
     * @covers Koch\Form\Form::setTarget
     */
    public function testMethod_setTarget()
    {
        $this->form->setTarget('_self');

        $this->assertEquals('_self', $this->form->getTarget());
    }

    public function testGetAutocomplete()
    {
        $this->form->setAutocomplete(false);
        $this->assertEquals('off', $this->form->getAutocomplete());
    }

    public function testSetAutocomplete()
    {
        $this->form->setAutocomplete(false);
        $this->assertEquals('off', $this->form->getAutocomplete());

        $this->form->setAutocomplete(true);
        $this->assertEquals('on', $this->form->getAutocomplete());
    }

    public function testGetNoValidation()
    {
        $this->form->setNoValidation(true);
        $this->assertEquals('novalidate', $this->form->getNoValidation());
    }

    public function testSetNoValidation()
    {
        $this->form->setNoValidation(false);

        // via getter - returns empty string
        $this->assertEquals('', $this->form->getNoValidation());

        $this->form->setNoValidation(true);

        // via getter - returns string
        $this->assertEquals('novalidate', $this->form->getNoValidation());
    }

    public function testGetAttribute()
    {
        $this->form->setAttribute('myAttribute', true);

        // via getter - returns string
        $this->assertEquals(true, $this->form->getAttribute('myAttribute'));
    }

    public function testSetAttribute()
    {
        $this->form->setAttribute('myAttribute', true);

        // via getter - returns string
        $this->assertEquals(true, $this->form->getAttribute('myAttribute'));
    }

    public function testSetAttributes()
    {
        $array = array('attr1' => 'val1', 'attr2' => true);

        $this->form->setAttributes($array);

        // via getter - returns string
        $this->assertEquals('val1',  $this->form->getAttribute('attr1'));
        $this->assertEquals(true,  $this->form->getAttribute('attr2'));

        unset($array);
    }

    public function testSetAttributesContainsFormKey()
    {
        $this->markTestSkipped('Depends on Form\Generator\PHPArray');

        $attributes = array(
            'attr1' => 'val1',
            'attr2' => true,
            'form'  => array(
                'name' => 'formname',
                'action' => 'someAction',
                'method' => 'POST',
                'key-a' => 'value-a',
                'key-b' => 'value-b'
            )
        );

        $this->form->setAttributes($attributes);
    }

    public function testCopyObjectProperties()
    {
        // prefilled object
        $from_object_a = new \stdClass();
        $from_object_a->attribute_string = 'value_of_attr_a';
        $from_object_a->attribute_int = 9;
        $from_object_a->attribute_bool = true;
        $from_object_a->attribute_array = array('key' => 'value');

        // empty target object
        $to_object_b = new \stdClass();

        $this->form->copyObjectProperties($from_object_a, $to_object_b);

        $this->assertEquals($from_object_a, $to_object_b);
        $this->assertEquals($to_object_b->attribute_string, 'value_of_attr_a');
        $this->assertEquals($to_object_b->attribute_int, 9);
        $this->assertEquals($to_object_b->attribute_bool, true);
        $this->assertEquals($to_object_b->attribute_array['key'], 'value');
    }

    /**
     * @covers Koch\Form\Form->setID()
     * @covers Koch\Form\Form->getID()
     */
    public function testSetID()
    {
        $this->form->setId('identifier1');
        $this->assertEquals('identifier1', $this->form->getId());
    }

    /**
     * @covers Koch\Form\Form->setName()
     * @covers Koch\Form\Form->getName()
     */
    public function testSetName()
    {
        $this->form->setName('name1');
        $this->assertEquals('name1', $this->form->getName());
    }

    /**
     * @covers Koch\Form\Form->setAcceptCharset()
     * @covers Koch\Form\Form->getAcceptCharset()
     */
    public function testGetAcceptCharset()
    {
        // via getter - returns default value utf-8 as string
        $this->assertEquals('utf-8', $this->form->getAcceptCharset());

        $this->form->setAcceptCharset('iso-8859-1');

        // via getter - returns string
        $this->assertEquals('iso-8859-1', $this->form->getAcceptCharset());
    }

    /**
     * @covers Koch\Form\Form->setClass()
     * @covers Koch\Form\Form->getClass()
     */
    public function testSetClass()
    {
        $this->form->setClass('cssclassname1');

        // via getter - returns string
        $this->assertEquals('cssclassname1', $this->form->getClass());
    }

    /**
     * @covers Koch\Form\Form->setDescription()
     * @covers Koch\Form\Form->getDescription()
     */
    public function testSetDescription()
    {
        $this->form->setDescription('description1');

        // via getter - returns string
        $this->assertEquals('description1', $this->form->getDescription());
    }

    /**
     * @covers Koch\Form\Form->setHeading()
     * @covers Koch\Form\Form->getHeading()
     */
    public function testSetHeading()
    {
        $this->form->setHeading('heading2');

        // via getter - returns string
        $this->assertEquals('heading2', $this->form->getHeading());
    }

    /**
     * @covers Koch\Form\Form->setEncoding()
     * @covers Koch\Form\Form->getEncoding()
     */
    public function testGetEncoding()
    {
        // via getter - returns default value as string
        $this->assertEquals('multipart/form-data', $this->form->getEncoding());

        $this->form->setEncoding('text/plain');

        // via getter - returns string
        $this->assertEquals('text/plain', $this->form->getEncoding());
    }

    /**
     * @covers Koch\Form\Form->setLegend()
     * @covers Koch\Form\Form->getLegend()
     */
    public function testSetLegend()
    {
        $this->form->setLegend('legend-set');

        // via getter - returns string
        $this->assertEquals('legend-set', $this->form->getLegend());

        // allows method chaining
        $this->assertEquals($this->form, $this->form->setLegend('returns form object'));
    }

    public function testSetLegend_allowsMethodChaining()
    {
        $return_value = $this->form->setLegend('returns form object');

        $this->assertSame($this->form, $return_value);
    }

    /**
     * @covers Koch\Form\Form->setFormelements()
     * @covers Koch\Form\Form->getFormelements()
     */
    public function testSetFormelements()
    {
        // via getter - returns inital empty array
        $this->assertEquals(array(), $this->form->getFormelements());

        $formelements = array('formelements');
        $this->form->setFormelements($formelements);
        $this->assertEquals($formelements, $this->form->getFormelements());
    }

    public function testFormHasErrors()
    {
        $this->assertFalse($this->form->FormHasErrors());
    }

    public function testregisterDefaultFormelementDecorators()
    {
        $this->form->addElement('Textarea');
        $formelements = $this->form->getFormelements();
        $textarea_formelement = $formelements['0'];

        $this->form->registerDefaultFormelementDecorators($textarea_formelement);

        $formelement_decorators = $textarea_formelement->getDecorators();

        $this->assertFalse(empty($formelement_decorators));
        $this->assertTrue(is_object($formelement_decorators['label']));
        $this->assertTrue(is_object($formelement_decorators['description']));
        $this->assertTrue(is_object($formelement_decorators['div']));
    }

    public function testRenderAllFormelements()
    {
        $this->form->addElement('Textarea');

        $formelements_html_expected = CR . '<div class="formline"><textarea id="textarea-formelement-0"></textarea></div>' . CR;

        $formelements_html = $this->form->renderAllFormelements();
        $this->assertFalse(empty($formelements_html));
        $this->assertEquals($formelements_html, $formelements_html_expected);
    }

    /**
     * @expectedException Koch\Exception\Exception
     * @expectedExceptionMessage Error rendering formelements. No formelements on form object. Consider adding some formelements using addElement().
     */
    public function testRenderAllFormelements_throwsException()
    {
        $this->form->renderAllFormelements();
    }

    public function testuseDefaultFormDecorators_disable_via_constructor()
    {
        $form = new Form(array('useDefaultFormDecorators' => true));
        $decorators = $form->getDecorators();
        $this->assertEquals(array(), $decorators);
        unset($form);
    }

    public function testuseDefaultFormDecorators_method_true()
    {
        $this->form->useDefaultFormDecorators(true);

        $this->form->registerDefaultFormDecorators();
        $default_form_decorators = $this->form->getDecorators();
        $this->assertFalse(empty($default_form_decorators));
        $this->assertTrue(is_object($default_form_decorators['form']));
        $this->assertTrue(is_a($default_form_decorators['form'], 'Koch\Form\FormDecorator'));
    }

    public function testregisterDefaultFormDecorators()
    {
        $this->form->registerDefaultFormDecorators();
        $default_form_decorators = $this->form->getDecorators();
        $this->assertFalse(empty($default_form_decorators));
        $this->assertTrue(is_object($default_form_decorators['form']));
        $this->assertTrue(is_a($default_form_decorators['form'], 'Koch\Form\FormDecorator'));
    }

    public function testremoveDecorator()
    {
        $this->form->registerDefaultFormDecorators();
        $this->form->removeDecorator('form');
        $default_form_decorators = $this->form->getDecorators();
        $this->assertFalse(array_key_exists('form', $default_form_decorators));
    }

    public function testgetDecorator()
    {
        $this->form->registerDefaultFormDecorators();
        $default_form_decorators = $this->form->getDecorators();
        $this->assertTrue(array_key_exists('form', $default_form_decorators));
        $this->assertInstanceOf('Koch\Form\Decorators\Form\Form', $this->form->getDecorator('form'));
    }

    /*
     * expectedException        InvalidArgumentException
     * expectedExceptionMessage The Form does not have a Decorator called "not-existing-formdecorator".
     */
    public function testgetDecorator_exception_notfound()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->form->getDecorator('not-existing-formdecorator');
    }

    public function testRender()
    {
        $this->form->addElement('Textarea');

        $html = $this->form->render();
        $this->assertFalse(empty($html));
        $this->assertContains('<form', $html);
        $this->assertContains('<textarea id="textarea-formelement-0">', $html);
        $this->assertContains('</form>', $html);
    }

    public function test__toString()
    {
        $this->form->addElement('Textarea');

        ob_start();
        print $this->form;
        $html = ob_get_clean();

        $this->assertFalse(empty($html));
        $this->assertContains('<form', $html);
        $this->assertContains('<textarea id="textarea-formelement-0">', $html);
        $this->assertContains('</form>', $html);
    }

    public function testAddElement()
    {
        $this->form->addElement('Text');
        // $this->form->getElementByPosition('0');
        $formelements_array = $this->form->getFormelements();

        $formelement = new \Koch\Form\Elements\Text;
        $formelement->setID('text-formelement-0');

        $this->assertEquals($formelement, $formelements_array[0]);
    }

    public function testAddElement_addingFileElementSetsEncoding()
    {
        $this->form->addElement('file');
        $this->assertEquals('multipart/form-data', $this->form->getEncoding());
    }

    public function testAddElement_toSpecificPositions()
    {
        $this->form->addElement('textarea', array(), 'Position1');
        $this->form->addElement('checkbox', array(), 'Position2');
        $this->form->addElement('submitbutton', array(), 'Position3');

        $formelements = $this->form->getFormelements();
        $this->assertArrayHasKey('Position1', $formelements);
        $this->assertArrayHasKey('Position2', $formelements);
        $this->assertArrayHasKey('Position3', $formelements);
    }

    public function testAddElement_withMultipleElements()
    {
        $formelements = array();
        $formelements[] = $this->form->addElement('ButtonBar');
        $formelements[] = $this->form->addElement('Textarea');
        $formelements[] = $this->form->addElement('Checkbox');

        $formelements_from_testobject = $this->form->getFormelements();

        $this->assertNotEmpty($formelements_from_testobject);
        $this->assertSame($formelements, $this->form->getFormelements());
    }

    public function testAddElement_withSettingAttributes()
    {
        // test element
        $attributes = array(
            'class' => 'myFormelementClass',
            'maxlength' => '20',
            'label' => 'myFormelementLabel',
            'id' => 'text-formelement-0'
        );

        $this->form->addElement('Text', $attributes);
        $formelement = $this->form->getElementByPosition('0');

        $this->assertEquals($attributes['class'], $formelement->class);
        $this->assertEquals($attributes['maxlength'], $formelement->maxlength);
        $this->assertEquals($attributes['label'], $formelement->label);
        $this->assertEquals($attributes['id'], $formelement->id);
    }

    public function testAddElement_ToCertainPosition()
    {
        // PREPARE:
        // this will take position 0
        $this->form->addElement('File');
        // this will take position 1
        $this->form->addElement('Captcha');

        // TEST:
        // this will take position 0 + reorders the array
        $this->form->addElement('Text', null, 0);

        $array = array();
        $array[] = new Elements\Text;    // 0 - Text
        $array[] = new Elements\File;    // 1 - File
        $array[] = new Elements\Captcha; // 2 - Captcha

        // manually reapply formelement identifiers
        $array['0']->setID('text-formelement-0');
        $array['1']->setID('file-formelement-1');
        $array['2']->setID('captcha-formelement-2');

        $this->assertEquals($array, $this->form->getFormelements());
    }

    public function testAddElement_switchEncodingWhenUsingFormelementFile()
    {
        $this->form->addElement('File');

        $this->assertContains('enctype="multipart/form-data"', $this->form->render());
    }

    public function testregenerateFormelementIdentifiers()
    {
        // PREPARE:
        // this will take position 0
        $this->form->addElement('File');
        // this will take position 1
        $this->form->addElement('Captcha');

        // TEST:
        // this will take position 0 + reorders the array
        $this->form->addElement('Text', null, 0);

        $array = array();
        $array[] = new \Koch\Form\Elements\Text;    // 0 - Text
        $array[] = new \Koch\Form\Elements\File;    // 1 - File
        $array[] = new \Koch\Form\Elements\Captcha; // 2 - Captcha

        // manually reapply formelement identifiers
        $array['0']->setID('text-formelement-0');
        $array['1']->setID('file-formelement-1');
        $array['2']->setID('captcha-formelement-2');

        $this->assertEquals($array, $this->form->getFormelements());
    }

    public function testDelElementByName()
    {
        $this->form->addElement('Textarea')->setName('myTextareaElement');
        $this->form->delElementByName('myTextareaElement');

        $this->assertNull($this->form->getElementByName('myTextareaElement'));

        // delete of non existing element returns false
        $this->assertFalse($this->form->delElementByName('a-not-existing-formelement'));
    }

    public function testGetElementByPosition()
    {
        $this->form->addElement('Text');

        $formelements_array = $this->form->getFormelements();

        $this->assertSame( $formelements_array['0'], $this->form->getElementByPosition(0));

        // not existing position returns null
        $this->assertNull($this->form->getElementByPosition(1));
    }

    public function testGetElementByName()
    {
        $this->form->addElement('Button')->setName('myButton1');

        $formelement_object = $this->form->getElementByName('myButton1');
        $this->assertSame('myButton1', $formelement_object->getName());
    }

        public function testGetElement_ByName_or_ByPosition_or_LastElement()
    {
        $this->form->addElement('Button')->setName('myButton1');

        // ByName
        $formelement_object = $this->form->getElement('myButton1');
        $this->assertSame('myButton1', $formelement_object->getName());

        // ByPosition
        $formelement_object = $this->form->getElement('0');
        $this->assertSame('myButton1', $formelement_object->getName());

        // Default Value null as param
        $formelement_object = $this->form->getElement();
        $this->assertSame('myButton1', $formelement_object->getName());
    }

    public function testFormelementFactory()
    {
        $formelement_object = $this->form->formelementFactory('Url');

        $this->assertInstanceof('\Koch\Form\Elements\Url', $formelement_object);
    }

    public function testMethod_processForm()
    {
        $this->markTestIncomplete();
    }

    public function testsetValues_DataArrayPassedToMethod()
    {
        // create multiselect "Snacks" with three options
        $this->form->addElement('MultiSelect')->setName('Snacks')->setOptions(
            array('cola' => 'Cola', 'popcorn' => 'Popcorn', 'peanuts' => 'Peanuts')
        );

        // two options were selected (array is incomming via post)
        $data = array('snacks' => array('cola', 'popcorn'));

        $this->form->setValues($data);

        $snacks_array = $this->form->getElementByName('Snacks')->getValue();
        $this->assertSame(count($snacks_array), 2);
        $this->assertSame($snacks_array[0], 'cola');
        $this->assertSame($snacks_array[1], 'popcorn');
    }

    public function testgetValues()
    {
        $this->form->addElement('Textarea', array('value' => 'Some Text Inside The First Textarea'));
        $this->form->addElement('Textarea', array('value' => 'More Text Inside The Second Textarea'));

        $values = $this->form->getValues();

        $this->assertTrue(is_array($values));
        $this->assertSame(count($values), 2);

        $expected_values = array (
            'textarea-formelement-0' => 'Some Text Inside The First Textarea',
            'textarea-formelement-1' => 'More Text Inside The Second Textarea'
        );

        $this->assertSame($values, $expected_values);
    }

    public function testSetFormelementDecorator_formelementPositionNull()
    {
        $this->form->addElement('Textarea');
        $this->form->setFormelementDecorator('label', null);

        $formelements = $this->form->getFormelements();
        $textarea_element = $formelements[0];
        $decorators = $textarea_element->formelementdecorators;

        $this->assertTrue(is_array($decorators));
        $this->assertEquals(1, count($decorators));
        $this->assertTrue(isset($decorators['label']));
    }

    public function testAddFormelementDecorator()
    {
        $this->form->addElement('Textarea');
        $this->form->addElement('MultiSelect');
        $this->form->addFormelementDecorator('label', 1);

        $formelements = $this->form->getFormelements();
        $textarea_element = $formelements[1];
        $decorators = $textarea_element->formelementdecorators;

        $this->assertTrue(is_array($decorators));
        $this->assertEquals(1, count($decorators));
        $this->assertTrue(isset($decorators['label']));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage No Formelements found. Add the formelement(s) first, then decorate!
     */
    public function testAddFormelementDecorator_ThrowsExceptionWhenNoFormelementsFound()
    {
        $this->form->addFormelementDecorator('label', 1);
    }

    public function testRemoveFormelementDecorator()
    {
        $this->form->removeFormelementDecorator('label');
    }

    public function testSetDecorator()
    {
        $this->form->setDecorator('label');

        $decorators = $this->form->getDecorators();

        $this->assertTrue(is_array($decorators));
        $this->assertEquals(1, count($decorators));
        $this->assertTrue(isset($decorators['label']));
    }

    public function testAddDecorator()
    {
        $this->form->addDecorator('label');

        $decorators = $this->form->getDecorators();

        $this->assertTrue(is_array($decorators));
        $this->assertEquals(1, count($decorators));
        $this->assertTrue(isset($decorators['label']));
    }

    public function testGetDecorators()
    {
        $this->form->setDecorator('label');
        $decorators = $this->form->getDecorators();

        $this->assertTrue(is_array($decorators));
        $this->assertEquals(1, count($decorators));
    }

    public function testDecoratorFactory()
    {
        $form_decorator_object = $this->form->DecoratorFactory('label');

        $this->assertInstanceOf('Koch\Form\Decorators\Form\Label', $form_decorator_object);
    }


    /**
     * @covers Koch\Form\Form->setDecoratorAttributesArray()
     * @covers Koch\Form\Form->getDecoratorAttributesArray()
     */
    public function testsetDecoratorAttributesArray()
    {
        $attributes = array('attribute1' => 'value1');
        $this->form->setDecoratorAttributesArray($attributes);

        $this->assertSame($attributes, $this->form->getDecoratorAttributesArray());
    }

    public function testapplyDecoratorAttributes()
    {
        // decorator type will be form
        // this is just another way of setting attributes to the form itself
        $attributes = array('form' =>
            array('form' => // this is Koch\Form\Decorator\Form
                array('heading' => 'This is the Heading of the form.',
                      'description' => 'This is a form description text.')
        ));

        $this->form->setDecoratorAttributesArray($attributes);

        $this->form->registerDefaultFormDecorators();

        $this->form->applyDecoratorAttributes();

        $form_decorator_form = $this->form->getDecorator('form');

        $this->assertSame('This is the Heading of the form.', $form_decorator_form->heading);
        $this->assertSame('This is a form description text.', $form_decorator_form->description);
    }

    public function testAddValidator()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function testValidateForm_false()
    {
        $this->form->addElement('Textarea')
                ->setName('Textarea-Validate-Test')
                ->setRequired()
                ->setRules('required, string, maxlength=20');
        // ->setValue() is missing intentionally

        // no value set, but required
        $this->assertFalse($this->form->validateForm());

        // set a value, exceeding maxlength
        $element = $this->form->getElementByName('Textarea-Validate-Test');
        $element->setValue('0123456789-0123456789'); // 21 chars

        // max length exceeded
        $this->assertFalse($this->form->validateForm());
    }

    public function testValidateForm_true()
    {
        $this->form->addElement('Textarea')
                ->setName('Textarea-Validate-Test')
                ->setRequired()
                ->setRules('required, string, maxlength=20');

        // set value, length ok
        $element = $this->form->getElementByName('Textarea-Validate-Test');
        $element->setValue('01234567890123456789'); // 20 chars

        $this->assertTrue($this->form->validateForm());
    }

    public function testsetRequired()
    {
        $this->form->addElement('Textarea')->setName('Textarea-A')->setRequired();

        $formelement = $this->form->getElementByName('Textarea-A');
        $this->assertTrue($formelement->required);
        $this->assertTrue($formelement->isRequired());
    }

    public function testIsRequired()
    {
        $this->form->addElement('Textarea')->setName('Textarea-A')->setRequired();
        $formelement = $this->form->getElementByName('Textarea-A');
        $this->assertTrue($formelement->required);
        $this->assertTrue($formelement->isRequired());
    }

    public function testsetErrorState()
    {
        $this->form->setErrorState(true);
        $this->assertTrue($this->form->error);
        $this->assertTrue($this->form->getErrorState());
    }

    public function testgetErrorState()
    {
        $this->form->setErrorState(true);
        $this->assertTrue($this->form->error);
        $this->assertTrue($this->form->getErrorState());

        $this->form->setErrorState(false);
        $this->assertFalse($this->form->error);
        $this->assertFalse($this->form->getErrorState());
    }

    public function testaddErrorMessage()
    {
        $message = 'message text';
        $this->form->addErrorMessage($message);
        $errormessages = $this->form->getErrorMessages();
        $this->assertSame($message, $errormessages['0']);
    }

    public function testaddErrorMessages()
    {
        $set1 = array('aaa', 'bbb', 'ccc');
        $this->form->addErrorMessages($set1);
        $this->assertSame($set1, $this->form->getErrorMessages());
    }

    public function testaddErrorMessages_OverwriteMessages()
    {
        $set1 = array('aaa', 'bbb', 'ccc');
        $set2 = array('ddd', 'eee');
        $this->form->addErrorMessages($set1);
        $this->assertSame($set1, $this->form->getErrorMessages());
        $this->form->addErrorMessages($set2);
        $this->assertSame($set2, $this->form->getErrorMessages());
    }

    public function testresetErrorMessages()
    {
        $set1 = array('aaa', 'bbb', 'ccc');
        $this->form->addErrorMessages($set1);
        $this->form->resetErrorMessages();
        $messages = $this->form->getErrorMessages();
        $this->assertTrue(empty($messages));
    }

    public function testgetErrorMessages()
    {
        $set1 = array('aaa', 'bbb', 'ccc');
        $this->form->addErrorMessages($set1);
        $this->assertSame($set1, $this->form->getErrorMessages());
    }

    public function test__set()
    {
        // this will call __set
        $this->form->method = 'methodname';

        $this->assertEquals('methodname', $this->form->getMethod());
    }

    public function test__get()
    {
        // this will call __set
        $this->form->method = 'methodname';

        // this will call __get
        $this->assertEquals('methodname', $this->form->method);
    }
}
