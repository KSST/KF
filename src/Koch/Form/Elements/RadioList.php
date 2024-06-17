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

class RadioList extends Radio implements FormElementInterface
{
    protected $options;

    public function __construct()
    {
        $this->type = 'radio';
    }

    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    protected $separator = '<br/>';

    public function render()
    {
        $i    = 0;
        $html = '';
        while (list($key, $value) = each($this->options)) {
            // setup a new radio formelement
            $radio = new   \Koch\Form\Element\Radio();
            $radio->setValue($key)
                ->setName($value)
                ->setDescription($value)
                ->setLabel($value);

            // check the element, if value is "active"
            if ($this->value === $key) {
                $radio->setChecked();
            }

            // assign it as output
            $html .= $radio;

            // if we options, add a seperator
            if (++$i !== count($this->options)) {
                $html .= $this->separator;
            }
        }

        return $html;
    }
}
