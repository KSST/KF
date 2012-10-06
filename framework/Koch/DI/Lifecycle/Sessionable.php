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

namespace Koch\DI\Lifecycle;

use Koch\DI\AbstractLifecycle;

class Sessionable extends AbstractLifecycle
{
    private $slot;

    public function __construct($class, $slot = false)
    {
        parent::__construct($class);
        $this->slot = $slot ? $slot : $class;
    }

    public function instantiate($dependencies)
    {
        @session_start();
        if (false === isset($_SESSION[$this->slot])) {
            $_SESSION[$this->slot] = call_user_func_array(
                    array(new ReflectionClass($this->class), 'newInstance'), $dependencies);
        }

        return $_SESSION[$this->slot];
    }
}
