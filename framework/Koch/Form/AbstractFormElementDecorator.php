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

    public $name;

    public $cssClass;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
    * Set a CSS class attribute.
    *
    * @param string $cssClass
    * @return object Formelement
    */
    public function setCssClass($cssClass)
    {
        $this->cssClass = $cssClass;

        return $this->formelement;
    }

    /**
    * Get the CSS class attribute.
    *
    * @return string
    */
    public function getClass()
    {
        return $this->cssClass;
    }

    /**
     * Setter method to set the object which is to decorate.
     *
     * @param $form object of type \Koch\Form\Interface or \Koch\Form\Decorator\Interface
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

        if ($this->formelement instanceof \Koch\Form\Element\Decorator) {
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
