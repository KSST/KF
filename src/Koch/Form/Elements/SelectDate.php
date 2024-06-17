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

class SelectDate extends Input implements FormElementInterface
{
    public function __construct()
    {
        // Note: HTML5 <input type="date"> is not a select formelement.
        $this->type = 'date';

        return $this;
    }

    /**
     * HTML 5 has several Types of input formfields for date and time selection.
     *
     * date             - Selects date, month and year
     * month            - Selects month and year
     * week             - Selects week and year
     * time             - Selects time (hour and minute)
     * datetime         - Selects time, date, month and year (UTC time)
     * datetime-local   - Selects time, date, month and year (local time)
     */
    public function setType($type)
    {
        $types = ['date', 'month', 'week', 'time', 'datetime', 'datetime-local'];

        if (in_array($type, $types, true)) {
            $this->type = $type;
        } else {
            throw new \Koch\Exception\Exception(
                'Invalid formfield type specified. Choose one of ' . explode(',', $types)
            );
        }

        return $this;
    }
}
