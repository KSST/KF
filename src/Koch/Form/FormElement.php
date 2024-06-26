<?php

/**
 * Koch Framework
 *
 * SPDX-FileCopyrightText: 2005-2024 Jens A. Koch
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Koch\Form;

/**
 * Class for a FormElement.
 */
class FormElement implements \Stringable
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $size;

    /**
     * @var string
     */
    public $disabled;

    /**
     * @var int
     */
    public $maxlength;

    /**
     * @var string
     */
    public $style;

    /**
     * @var string
     */
    public $onclick;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $value;

    /**
     * @var int
     */
    public $position;

    /**
     * @var bool
     */
    public $required;

    /**
     * @var array
     */
    public $additional_attributes;

    /**
     * @var array
     */
    protected $formelementdecorators = [];

    /**
     * @var bool
     */
    protected $disableDefaultDecorators;

    /**
     * validators are stored into an array. making multiple validators for one formelement possible.
     *
     * @var array
     */
    protected $validators = [];

    /**
     * The error messages array stores the incomming errors fromelement validators.
     *
     * @var array
     */
    protected $errormessages = [];

    /**
     * Error status (flag variable) of the formelement.
     *
     * @var bool
     */
    protected $error = false;

    /**
     * Set id of this form.
     *
     * @param $id string ID of this form.
     *
     * @return FormElement
     */
    public function setID($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Returns action of this form.
     *
     * @return string ID of this form.
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * Set type of this form.
     *
     * @param $id string Type of this form.
     * @param string $type
     *
     * @return FormElement
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Returns type of this form.
     *
     * @return string TYPE of this form.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name of this form.
     *
     * @param $name string Name of this form.
     *
     * @return FormElement
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns name of this form.
     *
     * @return string Name of this form.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns name of this formelement without brackets.
     *
     * @return string Name of this form.
     */
    public function getNameWithoutBrackets()
    {
        $name = strrpos($this->name, '[');

        // remove brackets
        $name = $this->name;
        // replace left
        $name = str_replace('[', '_', $name);
        // replace right with nothing (strip right)
        $name = str_replace(']', '', $name);

        return $name;
    }

    /**
     * Set class of this form.
     *
     * @param string $class Class to set
     *
     * @return FormElement
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Returns class of this form.
     *
     * @return string Name of this form.
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Sets value for this element.
     *
     * @param string $value
     *
     * @return FormElement
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Return the (escaped!) value of the formelement.
     *
     * @return string Escaped string
     */
    public function getValue()
    {
        if (is_array($this->value)) {
            foreach ($this->value as $key => $value) {
                $this->value[$key] = htmlspecialchars($value);
            }

            return $this->value;
        } else {
            return htmlspecialchars($this->value);
        }
    }

    /**
     * Returns the (unescaped!) value.
     *
     * @return string Unescaped string
     */
    public function getRawValue()
    {
        return $this->value;
    }

    /**
     * Disables this formelement.
     *
     * @return FormElement
     */
    public function disable()
    {
        $this->disabled = true;

        return $this;
    }

    /**
     * Enables this formelement.
     *
     * @return FormElement
     */
    public function enable()
    {
        $this->disabled = false;

        return $this;
    }

    /**
     * Set label of this formelement.
     *
     * @param string $label Label of this formelement.
     *
     * @return FormElement
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Returns label of this formelement.
     *
     * @return string Label of this formelement.
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Returns boolean true if a label exists for this formelement.
     *
     * @return bool True if label exists, false if not.
     */
    public function hasLabel()
    {
        return ($this->label !== null) ? true : false;
    }

    /**
     * This method provides a shortcut for checking if an formelement is required.
     * You might use this in conditional checks.
     *
     * @return bool True if formelement is required, false if not.
     */
    public function isRequired()
    {
        return ($this->required !== null) ? true : false;
    }

    /**
     * Set required state for the formelement.
     * A formelement is required, when the user is expected to (must) enter data into the formelement.
     *
     * @param bool $required Set required state. Defaults to true.
     *
     * @return FormElement
     */
    public function setRequired($required = true)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Set description of this formelement.
     *
     * @param string $description Description of this formelement.
     *
     * @return FormElement
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Returns description of this formelement.
     *
     * @return string Description of this formeement.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set onclick text of this formelement.
     *
     * @param string $onclick Onclick text of this formelement.
     *
     * @return FormElement
     */
    public function setOnclick($onclick)
    {
        $this->onclick = $onclick;

        return $this;
    }

    /**
     * Returns onclick text of this formelement.
     *
     * @return string Onclick text of this formelement.
     */
    public function getOnclick()
    {
        return $this->onclick;
    }

    /**
     * Sets the tab index value.
     *
     * @param string $index
     */
    public function setTabIndex($index)
    {
        $this->tabIndex = $index;
    }

    /**
     * Returns the tabindex value.
     *
     * @return string
     */
    public function getTabIndex()
    {
        #return "tabindex='" . $this->tabIndex . "'";

        return $this->tabIndex;
    }

    /**
     * ===================================================================================
     *      Formelement Attribute Handling
     * ===================================================================================.
     */

    /**
     * Returns the requested attribute if existing else null.
     *
     * @param $parametername
     *
     * @return mixed null or value of the attribute
     */
    public function getAttribute($attribute)
    {
        if ($this->{$attribute} !== null) {
            return $this->{$attribute};
        } else {
            return;
        }
    }

    /**
     * Setter method for Attributes.
     *
     * @param array $attributes Array with one or several attributename => value relationships.
     */
    public function setAttributes($attributes)
    {
        if (is_array($attributes) and $attributes === []) {
            foreach ($attributes as $attribute => $value) {
                /*
                 * In DEBUG mode the attributes are set via a setter method.
                 * So one might even set a wrong one by accident, like $attribute = 'maxxxlength'.
                 * To protect the developer a bit more, we are focing the usage of a setter method.
                 * If the setter method exists most likely the property will exist too, i guess.
                 */
                if (defined('DEBUG') and DEBUG === true) {
                    $method = 'set' . ucfirst($attribute);
                    if (method_exists($this, $method)) {
                        $this->$method($value);
                    } else {
                        throw new \RuntimeException(sprintf(
                            'You are trying to set attribute "%s", but the setter method "%s" was not found.',
                            $attribute,
                            $method
                        ));
                    }
                } else { // while in production mode
                    // set attribute directly
                    $this->{$attribute} = $value;
                }
            }
        }
    }

    /**
     * Setter method for Attribute.
     *
     * @param string $attribute Attribute name
     * @param bool   $value     Value
     */
    public function setAttribute($attribute, $value)
    {
        $this->{$attribute} = $value;
    }

    /**
     * Renders an array of key=>value pairs as an HTML attributes string.
     *
     * @param array $attributes key=>value pairs corresponding to HTML attributes name="value"
     *
     * @return string Attributes as HTML
     */
    public function renderAttributes(array $attributes = [])
    {
        if ($attributes === []) {
            return '';
        }

        $html = ' ';
        foreach ($attributes as $key => $val) {
            // html = 'key="value" '
            $html .= $key . '="' . $val . '" ';
        }

        return $html;
    }

    /**
     * ===================================================================================
     *      Formelement Validation
     * ===================================================================================.
     */

    /**
     * setRules sets validation rules as a string.
     *
     * "required, maxlength=20"
     * "required, email"
     *
     * @param string One or more (comma separated) validation rules to perform.
     *
     * @return \Koch\Form\FormElement
     */
    public function setRules($rule)
    {
        if (false === is_string($rule)) {
            throw new \InvalidArgumentException('Parameter $rule must be of type string.');
        }

        // handle multiple rules
        $rules = explode(',', $rule);

        foreach ($rules as $rule) {
            $rule = trim($rule);

            // handle values (a property name to value relationship, like maxlength=20)
            if (str_contains($rule, '=')) {
                $array = explode('=', $rule);
                $rule  = $array[0];
                $value = $array[1];

                if (in_array($rule, ['maxvalue'], true)) {
                    $value = (int) $value;
                }

                $this->addValidator($rule, [$rule => $value]);
            } else {
                $this->addValidator($rule);
            }
        }

        return $this;
    }

    /**
     * Maps a validator rule to a validator classname.
     *
     * @param type $rule Lowercased rule
     *
     * @return string Validator Classname based on rule
     */
    public function mapRulenameToClassname($rule)
    {
        $array = [
            'email'     => 'Email',
            'equals'    => 'Equals',
            'ip'        => 'Ip',
            'locale'    => 'Locale',
            'maxlength' => 'MaxLength',
            'maxvalue'  => 'MaxValue',
            'minlength' => 'MinLength',
            'minvalue'  => 'MinValue',
            'range'     => 'Range',
            'regexp'    => 'RegExp',
            'required'  => 'Required',
            'string'    => 'String',
            'url'       => 'Url',
        ];

        return $array[$rule] ?? $rule;
    }

    /**
     * addValidator.
     *
     * Is a shortcut/proxy/convenience method for addValidator()
     *
     * @param object|string Formelement Validator
     * @param mixed A Validator Property Value.
     * WATCH OUT! THIS BREAKS THE CHAINING IN REGARD TO THE FORM
     *
     * @return Koch\Form\FormElement_Validator
     */
    public function addValidator($validator, $properties = null)
    {
        if (false === is_object($validator)) {

            // raise formelement "required" flag
            if ($validator === 'required' and false === $this->isRequired()) {
                $this->setRequired();
            }

            $validator = $this->getValidatorFromFactory($validator);
        }

        if ($properties !== null) {
            $validator->setProperties($properties);
        }

        $this->setValidator($validator);

        return $validator;
    }

    /**
     * Setter method for a validator.
     * The Validator is stored into the validators array.
     * So a formelement might have multiple validators.
     *
     * @param Koch_Validator $validator Accepts a Validator object.
     *
     * @return FormElement
     */
    public function setValidator($validator)
    {
        $this->validators[] = $validator;

        return $this;
    }

    /**
     * Returns a form validator object.
     * Also a factory method, which instantiates and returns a new formvalidator object.
     *
     * @return Koch_Formvalidator
     */
    public function getValidatorFromFactory($validator)
    {
        $class = $this->mapRulenameToClassname($validator);

        // construct classname
        $class = '\Koch\Form\Validators\\' . $class;

        // return early, if this object is already stored
        if (isset($this->validators[$class])) {
            return $this->validators[$class];
        } elseif (true === class_exists($class)) {
            return new $class();
        } else {
            // validator not found
            throw new \Exception('Validator named "' . $validator . '" not available.');
        }
    }

    /**
     * Validates the value of a formelement.
     *
     * The validation method processes the value of the formelement
     * by passing it to all registered validators of the formelement.
     * The value of the formelement is valid, when it satisfies
     * each of the element's validation rules.
     *
     * @see $validators array
     *
     * @return bool
     */
    public function validate()
    {
        $value = $this->getValue();

        // return early, if value empty|null and not required
        if ((('' === $value) || (null === $value)) and false === $this->isRequired()) {
            return true;
        }

        // return early, if we have a value, but no rules / validators
        if (null === $this->validators) {
            return true;
        }

        // iterate over all validators
        foreach ($this->validators as $validator) {
            // ensure element validates
            if ($validator->validate($value)) {
                // everything is fine, proceed
                continue;
            } else {
                // raise the error flag on the formelement
                $this->setError(true);

                // and transfer error message from the validator to the formelement
                $this->addErrorMessage($validator->getErrorMessage());

                return false;
            }
        }

        // formelement value is valid
        return true;
    }

    /**
     * Return the validators of formelement.
     *
     * @return \Koch\Form\FormValidatorInterface
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * Removes all Decorators of this formelement.
     */
    public function resetValidators()
    {
        $this->formelementdecorators = null;
    }

    /**
     * Method adds an validation error to the formelement_validation_error stack.
     *
     * @param $errormessage
     */
    public function addErrorMessage($errormessage)
    {
        $this->errormessages[] = $errormessage;
    }

    /**
     * Returns the validation_error stack.
     *
     * @param $validation_error
     */
    public function getErrorMessages()
    {
        return $this->errormessages;
    }

    /**
     * Sets the error state of the form (formHasError).
     *
     * @param bool $boolean
     */
    public function setError($boolean = true)
    {
        $this->error = $boolean;
    }

    /**
     * Returns the error state of the form.
     *
     * @return bool False, if form has an error. True, otherwise.
     */
    public function hasError()
    {
        return $this->error;
    }

    /**
     * ===================================================================================
     *      Formelement Rendering
     * ===================================================================================.
     */

    /**
     * The method __toString works in the scope of the subclass.
     * Each formelement renders itself.
     * All formelements inherit the formelement base class,
     * so we place the magic method here, in the parent,
     * and catch the subclass via get_class($this).
     *
     * @return @return HTML Representation of the subclassed Formelement
     */
    public function __toString(): string
    {
        return (string) $this->render();
    }

    /**
     * ===================================================================================
     *      Formelement Decoration
     * ===================================================================================.
     */

    /**
     * Adds a decorator to the formelement.
     *
     * Usage:
     * $form->addDecorator('fieldset')->setLegend('legendname');
     *
     * WATCH OUT! THIS BREAKS THE CHAINING IN REGARD TO THE FORM OBJECT
     *
     * @param string $decorators
     *
     * @return Koch\Form\FormElement_Decorator
     */
    public function addDecorator($decorators)
    {
        // init vars
        $decoratorname = '';
        $decorator     = '';

        // check if multiple decorators are incomming at once
        if (is_array($decorators)) {
            // address each one of those decorators
            foreach ($decorators as $decorator) {
                // and check if it is an object implementing the right interface
                if ($decorator instanceof Koch\Form\DecoratorInterface) {
                    // if so, fetch this decorator objects name
                    $decoratorname = $decorator->name;
                } else {
                    // turn it into an decorator object
                    $decorator     = $this->decoratorFactory($decorator);
                    $decoratorname = $decorator->name;

                    // WATCH OUT! RECURSION!
                    $this->addDecorator($decorator);
                }
            }
        } elseif (is_object($decorators)) { // one element is incomming via recursion
            $decorator     = $decorators;
            $decoratorname = $decorator->name;
        }

        // if we got a string (ignore the plural, it's a one element string, like 'fieldset')
        if (is_string($decorators)) {
            // turn it into an decorator object
            $decorator     = $this->decoratorFactory($decorators);
            $decoratorname = $decorator->name;
        }

        // now check if this decorator is not already set (prevent decorator duplications)
        if (in_array($decoratorname, $this->formelementdecorators, true) === false) {
            // set this decorator object under its name into the array
            $this->formelementdecorators[$decoratorname] = $decorator;
        }

        // WATCH OUT! THIS BREAKS THE CHAINING IN REGARD TO THE FORM
        // We dont return $this here, because $this would be the formelement.
        // Insted the decorator is returned, to apply some properties.
        // @return decorator object
        #\Koch\Debug\Debug::printR($this->formelementdecorators[$decoratorname]);
        #\Koch\Debug\Debug::printR($this->name);
        #\Koch\Debug\Debug::firebug($this);
        #\Koch\Debug\Debug::firebug($this->formelementdecorators);

        return $this->formelementdecorators[$decoratorname];
    }

    /**
     * Getter Method for a decorators of this formelement by it's name..
     *
     * @param string $decoratorname The formelement decorator to look for in the stack of decorators.
     *
     * @return array Returns the object Koch\Form\FormElement_Decorator_$decoratorname if registered.
     */
    public function getDecoratorByName($decoratorname)
    {
        return $this->formelementdecorators[$decoratorname];
    }

    /**
     * Getter Method for the decorators of this formelement.
     *
     * @return array Returns the array of Koch\Form\FormElement_Decorators registered to this formelement.
     */
    public function getDecorators()
    {
        return $this->formelementdecorators;
    }

    /**
     * Removes the requested decorator from the decorators stack.
     *
     * @param string $decoratorname
     *
     * @throws \Exception
     */
    public function removeDecorator($decoratorname)
    {
        if ($this->formelementdecorators[$decoratorname] !== null) {
            unset($this->formelementdecorators[$decoratorname]);
        } else {
            throw new \Exception('Decorator does not exist.');
        }
    }

    /**
     * Removes all Decorators of this formelement.
     */
    public function removeDecorators()
    {
        $this->formelementdecorators = null;
    }

    /**
     * Disables the use of default decorators on this formelement.
     */
    public function disableDefaultDecorators()
    {
        $this->disableDefaultDecorators = true;
    }

    public function useDefaultDecorators()
    {
        return ($this->disableDefaultDecorators === true) ? false : true;
    }

    /**
     * Factory method. Instantiates and returns a new formdecorator object.
     *
     * @param string Formelement Decorator.
     *
     * @return string Koch\Form\FormElement\Decorators\$decorator
     */
    public function decoratorFactory($decorator)
    {
        $class = '\Koch\Form\Decorators\Formelement\\' . ucfirst((string) $decorator);

        return new $class();
    }

    /**
     * Magic Method: set.
     *
     * @param $name Name of the attribute to set to the form.
     * @param $value The value of the attribute.
     */
    public function __set($name, $value)
    {
        $this->setAttributes([$name => $value]);
    }

    /**
     * Magic Method: get.
     *
     * @param $name
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }
}
