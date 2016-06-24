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

namespace Koch\Module;

/**
 * Decorator for the ModuleController.
 *
 * Purpose: attach plugins and methods at runtime to the module by nesting (wrapping) them.
 * Pattern: @book "GOF:175" - Decorator (structural pattern)
 */
class Decorator
{
    // the moduleController to decorate
    protected $moduleController;

    /**
     * Decorate.
     */
    public function decorate(Koch_Module_Interface $moduleController)
    {
        $this->moduleController = $moduleController;
    }

    /**
     * Checks if a decorator provides a certain method
     * Order of processing: first it checks the current decorator, then all encapsulated ones.
     */
    public function hasMethod($methodname)
    {
        // is the method provided by this decorator?
        if (method_exists($this, $methodname)) {
            // yes
            return true;
        }

        // is the method provided by an encapsulated decorator?
        if ($this->moduleController instanceof Koch_Module_ControllerDecorator) {
            // dig into the encapsulated controller and ask for the method
            return $this->moduleController->hasMethod($methodname);
        }

        // there was no method found
        return false;
    }

    /**
     * Magic Method __call().
     *
     * When a method call to the current decorator is not defined, it is catched by __call().
     * So the purpose of this method is to delegate method calls to the different decorators.
     * This result is, that you have the full combination of methods of the nested decorators
     * available, without losing methods.
     *
     * Several Performance-Issues:
     * 1) costs for calling __call
     * 2) costs for calling call_user_func_array()
     * 3) the nested call stack itself: the bigger the stack, the slower it becomes.
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array($method, $arguments);
    }
}
