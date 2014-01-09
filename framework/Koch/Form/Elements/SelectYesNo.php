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

use Koch\Form\FormElementInterface;

class SelectYesNo extends Select implements FormElementInterface
{
    public function getYesNo()
    {
        $options = array('yes' => '1', 'no' => '0');

        return $options;
    }

    public function render()
    {
        // check if we have options
        if ($this->options == null) {
            // if we don't have options, we set only 'yes' and 'no'
            $this->setOptions($this->getYesNo());
        } else {
            // if options is set, it means that a options['select'] is given
            // we combine it with yes/no
            $this->setOptions($this->options += $this->getYesNo());
        }

        return parent::render();
    }
}
