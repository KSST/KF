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

namespace Koch\Form\Elements;

use Koch\Form\FormElementInterface;

class Range extends Input implements FormElementInterface
{
    public function __construct()
    {
        $this->type = 'range'; // displays a slider

        return $this;
    }

    /**
     * Specifies the minimum value allowed.
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Specifies the maximum value allowed.
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Specifies legal number intervals (if step="2", legal numbers could be -2,0,2,4, etc).
     */
    public function setStep($step)
    {
        $this->step = $step;

        return $this;
    }
}
