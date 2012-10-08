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

class Value extends AbstractLifecycle
{
    private $instance;

    public function __construct($instance)
    {
        $this->instance = $instance;
    }

    public function instantiate($dependencies)
    {
        return $this->instance;
    }
}
