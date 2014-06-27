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
 * Interface for Formelements.
 */
interface FormElementInterface
{
    // add/remove attributes for a formelement

    /**
     * @return null|Elements\JqSelectDate
     */
    public function setAttribute($attribute, $value);
    public function getAttribute($attribute);

    // getter/ setter for the value

    /**
     * @return FormElement|null
     */
    public function setValue($value);
    public function getValue();

    // initializes the attributes of the formelement
    #public function initialize();

    // renders the output of the formobject as html
    public function render();

    // sets a validation rule the form element
    #public function addValidation();

    #public function hasError();
    #public function getErrorMessage();
}
