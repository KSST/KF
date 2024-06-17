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

class CheckboxList extends Checkbox implements FormElementInterface
{
    public function getOptions()
    {
        $options = ['1' => 'eins', '2' => 'zwei', '3' => 'drei', '4' => 'Polizei'];

        return $options;
    }

    public function render()
    {
        $html = '';

        foreach ($this->getOptions() as $key => $value) {
            $checkbox = new \Koch\Form\Element\Checkbox();
            $checkbox->setLabel($value);
            $checkbox->setName($value);
            $checkbox->setDescription($value);
            $checkbox->setValue($key);
            $html .= $checkbox;
        }

        return $html;
    }
}
