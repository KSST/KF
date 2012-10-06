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

namespace Koch\Form\Elements;

class Password extends Text implements FormElementInterface
{
    /**
     * constructor
     */
    public function __construct()
    {
        $this->type = 'password';
    }

}
