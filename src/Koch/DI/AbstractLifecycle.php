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

namespace Koch\DI;

/**
 * Abstract base class for Lifecycles.
 */
abstract class AbstractLifecycle
{
    public function __construct(public $class)
    {
        // trigger autoloading
        class_exists($this->class, true);
    }

    public function isOneOf($candidates)
    {
        return in_array($this->class, $candidates, true);
    }

    abstract public function instantiate($dependencies);
}
