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
        $options = array( 'option1' => 'berlin',
                          'option2' => 'new york');

        $this->setOptions($options);

        $i=0;
        $html = '';
        while ( list($key, $value) = each($this->options)) {
            // setup a new radio formelement
            $radio = new Koch_Formelement_Radio();
            $radio->setValue($key)
                  ->setName($value)
                  ->setDescription($value)
                  ->setLabel($value);

            // check the element, if value is "active"
            if ($this->value == $key) {
                $radio->setChecked();
            }

            // assign it as output
            $html .= $radio;

            #\Koch\Debug\Debug::printR($html);

            // if we have more options comming up, add a seperator
            if (++$i!=count($this->options)) {
                $html .= $this->separator;
            }
        }

        return $html;
    }
}
