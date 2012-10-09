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

use Koch\Form\Elements\Input;
use Koch\Form\FormElementInterface;

class Radio extends Input implements FormElementInterface
{
    /**
     * label next to element
     *
     * @var string
     */
    protected $label;

    protected $description;

    /**
     * constructor
     *
     */
    public function __construct()
    {
        $this->type = 'radio';

        return $this;
    }

    /**
     * checks or unchecks radio button
     *
     * @param bool checked
     */
    public function setChecked($checked)
    {
        $this->checked = $checked;

        return $this;
    }

    /**
     * sets clickable label next to element
     *
     * @param string $text
     */
    public function setLabel($text)
    {
        $this->label = '<label for="'.$this->id.'">'.$text.'</label>';

        return $this;
    }

    /**
     * sets description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
