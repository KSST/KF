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
 * Abstract base class for Formelement Decorators.
 */
abstract class AbstractFormElementDecorator implements DecoratorInterface
{
    // instance of formelement, which is to decorate
    protected $formelement;

    private $name;
    private $class;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
    * Set class=""
    *
    * @param string $classname
    */
    public function setClass($classname)
    {
        $this->class = $classname;

        return $this->formelement;
    }

    /**
    * Get class="" values
    *
    * @return string
    */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Constructor
     *
     * @param $form Accepts a \Koch\Form\Form Object implementing the Koch_Form_Interface.
     */
    /*public function __construct(Koch_Form_Interface $form)
    {
        $this->decorate($form);
    }*/

    /**
     * Setter method to set the object which is to decorate.
     *
     * @param $form object of type Koch_Form_Interface or Koch_Form_Decorator_Interface
     */
    public function decorateWith($formelement)
    {
        $this->formelement = $formelement;
    }

    /**
     * The method checks, if this object or a decorator implements a certain method.
     *
     * @param $method
     * @return boolean
     */
    public function hasMethod($method)
    {
        if (method_exists($this, $method)) {
            return true;
        }

        if ($this->formelement instanceof Koch_Formelement_Decorator) {
            return $this->formelement->hasMethod($method);
        }

        return false;
    }

    /**
     * __call Magic Method
     *
     * In general this calls a certain method with parameters on the object which is to decorate ($form).
     *
     * @param $method
     * @param $parameters
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->formelement, $method), $parameters);
    }
}
