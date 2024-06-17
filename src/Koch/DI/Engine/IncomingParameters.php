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

namespace Koch\DI\Engine;

class IncomingParameters
{
    /**
     * @param \Koch\DI\DependencyInjector $injector
     */
    public function __construct(private $names, private $injector)
    {
    }

    public function with()
    {
        $values = func_get_args();
        $this->injector->useParameters(array_combine($this->names, $values));

        return $this->injector;
    }
}
