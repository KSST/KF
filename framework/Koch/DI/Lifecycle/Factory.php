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

class Factory extends AbstractLifecycle
{
    public function instantiate($dependencies)
    {
        return call_user_func_array(
            array(new \ReflectionClass($this->class), 'newInstance'),
            $dependencies
        );
    }
}
