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

use Koch\DI\Lifecycle\Value;

class Variable
{
    public $preference;
    private $context;

    /**
     * @param Context $context
     */
    public function __construct($context)
    {
        $this->context = $context;
    }

    public function willUse($preference)
    {
        $this->preference = $preference;

        return $this->context;
    }

    public function useString($string)
    {
        $this->preference = new Value($string);

        return $this->context;
    }
}
