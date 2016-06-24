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

namespace Koch\DI\Lifecycle;

use Koch\DI\AbstractLifecycle;

class Reused extends AbstractLifecycle
{
    private $instance;

    public function instantiate($dependencies)
    {
        if (false === isset($this->instance)) {
            $this->instance = call_user_func_array(
                [new \ReflectionClass($this->class), 'newInstance'],
                $dependencies
            );
        }

        return $this->instance;
    }
}
