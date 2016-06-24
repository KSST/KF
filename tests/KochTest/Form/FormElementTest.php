<?php

namespace KochTest\Form;

use Koch\Form\FormElement;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-01-28 at 15:59:53.
 */
class FormElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormElement
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new FormElement();
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
     * @covers Koch\Form\FormElement::setID
     * @covers Koch\Form\FormElement::getID
     */
    public function testSetID()
    {
        $id = 123;
        $this->object->setID($id);
        $this->assertEquals($this->object->getID(), $id);
    }

    /**
     * @covers Koch\Form\FormElement::setType
     * @covers Koch\Form\FormElement::getType
     */
    public function testSetType()
    {
        $this->object->setType('type');
        $this->assertEquals('type', $this->object->getType());
    }

    /**
     * @covers Koch\Form\FormElement::setName
     * @covers Koch\Form\FormElement::getName
     */
    public function testSetName()
    {
        $this->object->setName('name');
        $this->assertEquals('name', $this->object->getName());
    }

    /**
     * @covers Koch\Form\FormElement::getNameWithoutBrackets
     */
    public function testGetNameWithoutBrackets()
    {
        $this->object->setName('[textarea-custom-name]');
        $this->assertEquals('_textarea-custom-name', $this->object->getNameWithoutBrackets());
    }

    /**
     * @covers Koch\Form\FormElement::setClass
     * @covers Koch\Form\FormElement::getClass
     */
    public function testSetClass()
    {
        $this->object->setClass('class');
        $this->assertEquals('class', $this->object->getClass());
    }

    /**
     * @covers Koch\Form\FormElement::setValue
     * @covers Koch\Form\FormElement::getValue
     */
    public function testSetValue()
    {
        $this->object->setValue('value');
        $this->assertEquals('value', $this->object->getValue());
    }

    /**
     * @covers Koch\Form\FormElement::getRawValue
     */
    public function testGetRawValue()
    {
        $this->object->setValue('value');
        $this->assertEquals('value', $this->object->getRawValue());
    }

    /**
     * @covers Koch\Form\FormElement::disable
     * @covers Koch\Form\FormElement::enable
     */
    public function testDisable()
    {
        $this->object->enable();
        $this->assertFalse($this->object->disabled);

        $this->object->disable();
        $this->assertTrue($this->object->disabled);
    }

    /**
     * @covers Koch\Form\FormElement::setLabel
     * @covers Koch\Form\FormElement::getLabel
     * @covers Koch\Form\FormElement::hasLabel
     */
    public function testSetLabel()
    {
        $this->assertFalse($this->object->hasLabel());
        $this->object->setLabel('label');
        $this->assertTrue($this->object->hasLabel());
        $this->assertEquals('label', $this->object->getLabel());
    }

    /**
     * @covers Koch\Form\FormElement::setRequired
     * @covers Koch\Form\FormElement::isRequired
     */
    public function testIsRequired()
    {
        $this->object->setRequired();
        $this->assertTrue($this->object->required);
        $this->assertTrue($this->object->isRequired());
    }

    /**
     * @covers Koch\Form\FormElement::setDescription
     * @covers Koch\Form\FormElement::getDescription
     */
    public function testSetDescription()
    {
        $description = '123';
        $this->object->setDescription($description);
        $this->assertEquals($description, $this->object->getDescription());
    }

    /**
     * @covers Koch\Form\FormElement::setOnclick
     * @covers Koch\Form\FormElement::getOnclick
     */
    public function testSetOnclick()
    {
        $value = '123';
        $this->object->setOnclick($value);
        $this->assertEquals($value, $this->object->getOnclick());
    }

    /**
     * @covers Koch\Form\FormElement::setTabIndex
     * @covers Koch\Form\FormElement::getTabIndex
     */
    public function testSetTabIndex()
    {
        $value = '123';
        $this->object->SetTabIndex($value);
        $this->assertEquals($value, $this->object->getTabIndex());
    }

    /**
     * @covers Koch\Form\FormElement::getAttribute
     * @covers Koch\Form\FormElement::setAttributes
     */
    public function testGetAttribute()
    {
        $value = ['required' => true, 'label' => 'someLabel'];
        $this->object->setAttributes($value);
        $this->assertTrue($this->object->required);
        $this->assertEquals('someLabel', $this->object->getLabel());
    }

    /**
     * @covers Koch\Form\FormElement::setAttribute
     */
    public function testSetAttribute()
    {
        $this->object->setAttribute('required', true);
        $this->assertTrue($this->object->required);

        $this->object->setAttribute('required', false);
        $this->assertFalse($this->object->required);
    }

    /**
     * @covers Koch\Form\FormElement::renderAttributes
     */
    public function testRenderAttributes()
    {
        $attributes = ['key1' => 'value1', 'key2' => 'value2'];
        $this->assertEquals(' key1="value1" key2="value2" ', $this->object->renderAttributes($attributes));
    }

    /**
     * @covers Koch\Form\FormElement::setRules
     */
    public function testSetRules()
    {
        $this->object->setRules('string');
        $validators = $this->object->getValidators();
        $this->assertInstanceOf('Koch\Form\Validators\String', $validators[0]);
    }

    /**
     * @covers Koch\Form\FormElement::setRules
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Parameter $rule must be of type string.
     */
    public function testSetRulesThrowsException()
    {
        $rule = 123;
        $this->object->setRules($rule);
    }

    public function MapRulenameToClassnameDataprovider()
    {
        return [
            ['email', 'Email'],
            ['equals', 'Equals'],
            ['ip', 'Ip'],
            ['locale', 'Locale'],
            ['maxlength', 'MaxLength'],
            ['maxvalue', 'MaxValue'],
            ['minlength', 'MinLength'],
            ['minvalue', 'MinValue'],
            ['range', 'Range'],
            ['regexp', 'RegExp'],
            ['required', 'Required'],
            ['string', 'String'],
            ['url', 'Url'],
        ];
    }

    /**
     * @covers Koch\Form\FormElement::mapRulenameToClassname
     * @dataProvider MapRulenameToClassnameDataprovider
     */
    public function testMapRulenameToClassname($rule, $classname)
    {
        $this->assertEquals($classname, $this->object->mapRulenameToClassname($rule));
    }

    /**
     * @covers Koch\Form\FormElement::addValidator
     */
    public function testAddValidator()
    {
        $validator = 'string';
        $this->object->addValidator($validator);

        $validators = $this->object->getValidators();
        $this->assertInstanceOf('Koch\Form\Validators\String', $validators[0]);
    }

    /**
     * @covers Koch\Form\FormElement::setValidator
     */
    public function testSetValidator()
    {
        $validator = new \Koch\Form\Validators\String();

        $this->object->setValidator($validator);

        $validators = $this->object->getValidators();
        $this->assertInstanceOf('Koch\Form\Validators\String', $validators[0]);
    }

    /**
     * @covers Koch\Form\FormElement::getValidatorFromFactory
     * @expectedException Exception
     * @expectedExceptionMessage Validator named "not-existing" not available.
     */
    public function testGetValidatorFromFactory()
    {
        $this->object->getValidatorFromFactory('not-existing');
    }

    /**
     * @covers Koch\Form\FormElement::validate
     */
    public function testValidate()
    {
        $this->object->setValue('');
        $this->object->isRequired();
        $this->assertTrue($this->object->validate());

        $this->object->setValue(null);
        $this->object->isRequired();
        $this->assertTrue($this->object->validate());

        $this->object->resetValidators();
        $this->assertTrue($this->object->validate());
    }

    /**
     * @covers Koch\Form\FormElement::getValidators
     */
    public function testGetValidators()
    {
        $this->object->setRules('string');
        $validators = $this->object->getValidators();
        $this->assertInstanceOf('Koch\Form\Validators\String', $validators[0]);
    }

    /**
     * @covers Koch\Form\FormElement::addErrorMessage
     * @covers Koch\Form\FormElement::getErrorMessages
     */
    public function testGetErrorMessages()
    {
        $errormessages = [0 => 'Message1', 1 => 'Message2'];

        $this->object->addErrorMessage($errormessages[0]);
        $this->object->addErrorMessage($errormessages[1]);

        $this->assertEquals($errormessages, $this->object->getErrorMessages());
    }

    /**
     * @covers Koch\Form\FormElement::setError
     * @covers Koch\Form\FormElement::hasError
     */
    public function testSetError()
    {
        $this->object->setError(true);
        $this->assertTrue($this->object->hasError());
    }

    /**
     * @covers Koch\Form\FormElement::__toString
     */
    public function testMagicToString()
    {
        $formelement = new \Koch\Form\Elements\Email();
        $this->assertEquals('<input type="email" name="" />' . CR, $formelement->__toString());
    }

    /**
     * @covers Koch\Form\FormElement::addDecorator
     * @covers Koch\Form\FormElement::getDecoratorByName
     */
    public function testAddDecorator()
    {
        $decorator = 'description';
        $this->object->addDecorator($decorator);

        $decorator = $this->object->getDecoratorByName('description');
        $this->assertInstanceOf('\Koch\Form\Decorators\Formelement\Description', $decorator);
    }

    /**
     * @covers Koch\Form\FormElement::getDecorators
     */
    public function testGetDecorators()
    {
        $decorator = 'description';
        $this->object->addDecorator($decorator);

        $decorator  = $this->object->getDecoratorByName('description');
        $decorators = $this->object->getDecorators();
        $this->assertInstanceOf('\Koch\Form\Decorators\Formelement\Description', $decorators['description']);
    }

    /**
     * @covers Koch\Form\FormElement::removeDecorator
     */
    public function testRemoveDecorator()
    {
        $decorator = 'description';
        $this->object->addDecorator($decorator);

        $decorator  = $this->object->getDecoratorByName('description');
        $decorators = $this->object->getDecorators();
        $this->assertInstanceOf('\Koch\Form\Decorators\Formelement\Description', $decorators['description']);

        $this->object->removeDecorator('description');
        $decorators = $this->object->getDecorators();
        $this->assertFalse(isset($decorators['desciption']));
    }

    /**
     * @covers Koch\Form\FormElement::removeDecorators
     */
    public function testRemoveDecorators()
    {
        $decorator = 'description';
        $this->object->addDecorator($decorator);

        $decorator  = $this->object->getDecoratorByName('description');
        $decorators = $this->object->getDecorators();
        $this->assertInstanceOf('\Koch\Form\Decorators\Formelement\Description', $decorators['description']);

        $this->object->removeDecorators();
        $this->assertNull($this->object->getDecorators());
    }

    /**
     * @covers Koch\Form\FormElement::disableDefaultDecorators
     * @covers Koch\Form\FormElement::useDefaultDecorators
     */
    public function testDisableDefaultDecorators()
    {
        $this->object->disableDefaultDecorators();

        $this->assertFalse($this->object->useDefaultDecorators());
    }

    /**
     * @covers Koch\Form\FormElement::decoratorFactory
     *
     * @todo   Implement testDecoratorFactory().
     */
    public function testDecoratorFactory()
    {
        $decorator = $this->object->decoratorFactory('description');
        $this->assertInstanceOf('\Koch\Form\Decorators\Formelement\Description', $decorator);
    }

    /**
     * @covers Koch\Form\FormElement::__set
     * @covers Koch\Form\FormElement::__get
     */
    public function testMagicSet()
    {
        $this->object->name = 'test';

        $this->assertEquals('test', $this->object->name);
        $this->assertEquals('test', $this->object->getName());
    }

    /**
     * @covers Koch\Form\FormElement::__set
     * @expectedException RuntimeException
     * @expectedExceptionMessage
     * You are trying to set attribute "noSetter", but the setter method "noSetter" was not found.
     */
    public function testMagicSetThrowsException()
    {
        $this->object->noSetter = 'test';
    }
}
