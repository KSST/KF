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

namespace Koch\DI;

/**
 * Abstract base class for Lifecycles.
 */
abstract class AbstractLifecycle
{
    public $class;

    public function __construct($class)
    {
        $this->class = $class;

        // trigger autoloading
        class_exists($class, true);
    }

    public function isOneOf($candidates)
    {
        return in_array($this->class, $candidates);
    }

    abstract public function instantiate($dependencies);
}
