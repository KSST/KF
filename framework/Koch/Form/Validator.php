<?php

/**
 * Koch Framework
 * Jens A. Koch © 2005 - onwards
 *
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Koch\Form;

/**
 * Class for Validation of Forms.
 */
abstract class Validator
{
    /**
     * Error state of the validator.
     *
     * @var boolean
     */
    public $error = false;

    /**
     * General prupose options array.
     * For instance, this options array is passed as third parameter to
     * filter_var($value, FILTER_CONSTANT, $options).
     *
     * @var array
     */
    public $options = array();

    /**
     * Getter for Options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Setter for Options.
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Setter for the error state of the validator.
     *
     * @param boolean $bool True if error, false if not.
     */
    public function setError($bool)
    {
        $this->error = (bool) $bool;
    }

    /**
     * Getter for the error state of the validator.
     *
     * @return boolean
     */
    public function hasError()
    {
        return $this->error;
    }

    /**
     * Each Formelement Validator must return an errormessage.
     * The errormessage must be wrapped in a gettext shorthand call, like:
     * return _('This value is not ok.');
     *
     * @param string The Errormessage, when the validation fails.
     */
    abstract public function getErrorMessage();

    /**
     * A Formelement Validator must (!might) return a hint message.
     * The hint message is a description of the validator to the user.
     * It describes how the formelement should be filled to validate.
     *
     * The hint message must be wrapped in a gettext shorthand call, like
     * return _('You should enter a valid credit card number.');
     *
     * @return string Validation Hint Message
     */
    abstract public function getValidationHint();

    /**
     * Each Formelement Validator must implement validation logic.
     * This is the pure validation logic, called by validate().
     * If you need more complex things for validation, then
     * add some static helper functions for usage inside this method.
     *
     * @param $value The value to validate.
     * @param boolean True if formelement validates, false if not.
     */
    abstract protected function processValidationLogic($value);

    /**
     * Accepts an array and assigns object property names (by key) and values (by value) accordingly.
     * The objects needs a setter method for the value.
     *
     * Example:
     * $properties = array('maxlength' = '100');
     * Result:
     * $this->setMaxlength(100); === $this->maxlength = 100;
     *
     * @param array $properties
     */
    public function setProperties(array $properties)
    {
        foreach ($properties as $property_name => $value) {
            $setter_method = 'set' . $property_name;

            // Set the value via a Setter Method
            $this->{$setter_method}($value);
        }
    }

    /**
     * Main method for the validation of this formelement.
     *
     * @param boolean True if formelement validates, false if not.
     */
    public function validate($value)
    {
        return ($this->processValidationLogic($value) === true) ? true : false;
    }
}
