<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards.
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Koch\Form;

/**
 *  * Koch Framework - Abstract base class for Form Decorators.
 *
 * The base class for all decorators. It has the same type as \Koch\Form\Form itself.
 * The decorator pattern suggests that the decorator implements all public
 * methods of the component which it decorates.
 * Being "of the same type as Koch_Form" is achieved by implementing all methods
 * described by Koch_Form_Interface, NOT by extending Koch_Form.
 * "Implementing all methods" is achieved by using the magical __call() method.
 *
 * @see __call
 *
 * So basically instead of:
 *
 * public function render()
 * {
 *    return $this->form->render()
 * }
 *
 * and implementing each method in this way, we simply use _call($method, $parameters).
 * Ok, it's a tradeoff between magic against implementation of the interface
 * plus loosing the knowledege in which decorator the method is called.
 * If you still want to know, if a method exists on a decorator, use hasMethod().
 * Effect is that all children of this base class have all the methods of Koch_Form.
 *
 * @pattern Decorator, [GoF, 216/220]
 */
abstract class AbstractFormDecorator implements DecoratorInterface
{
    /**
     * Instance of the form, which is to decorate.
     *
     * @var \Koch\Form\Form Defaults to null.
     */
    protected $form = null;
    private $name;
    private $class;
    private $id;

    /**
     * Returns the name of the form.
     *
     * @return string
     */
    public function getFormName()
    {
        return $this->form->name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set css class.
     *
     * @param string $classname
     */
    public function setClass($classname)
    {
        $this->class = $classname;

        return $this->form;
    }

    /**
     * Get css class.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set html id attribute.
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this->form;
    }

    /**
     * Get html id attribute value.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setter method to set the form object, which is to decorate.
     *
     * @param FormInterface $form object of type Koch\Form\FormInterface or Koch\Form\FormDecoratorInterface
     */
    public function decorateWith($form)
    {
        if (null === $form) {
            throw new \InvalidArgumentException('Form is null!');
        }

        $this->form = $form;
    }

    /**
     * Setter method to set the form object, which is to decorate.
     *
     * @param Form $form
     */
    public function setForm(\Koch\Form\FormInterface $form)
    {
        $this->decorateWith($form);
    }

    /**
     * Get the form object, which is to decorate.
     *
     * @return \Koch\Form\Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * The method checks, if this object or a decorator implements a certain method.
     *
     * @param $method
     *
     * @return bool
     */
    public function hasMethod($method)
    {
        // checks, if method exists in object or decorator
        if (method_exists($this, $method)) {
            return true;
        }

        if ($this->form instanceof Koch\Form\DecoratorInterface) {
            return $this->form->hasMethod($method);
        }

        return false;
    }

    /**
     * __call Magic Method.
     *
     * In general this calls a certain method with parameters on the object which is to decorate ($form).
     *
     * @param $method
     * @param $parameters
     */
    public function __call($method, $parameters)
    {
        if (is_object($this->form)) {
            return call_user_func_array([$this->form, $method], $parameters);
        }
    }
}
