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

class Checkbox extends Input implements FormElementInterface
{
    /**
     * Label next to element
     *
     * @var string
     */
    public $label;

    /**
     * Default option
     *
     * @var string
     */
    public $default;

    /**
     * Options
     *
     * @var array
     */
    public $options;

    public $description;

    public function setDefaultOption($default)
    {
        $this->default = $default;

        return $this;
    }

    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * constructor
     */
    public function __construct()
    {
        $this->type = 'checkbox';
        $this->label = null;

        return $this;
    }

    /**
     * check or unchecks the checkbox
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
     * @param string $text
     */
    public function setDescription($text)
    {
        $this->description = $text;

        return $this;
    }

    public function render()
    {
        return parent::render() . $this->getLabel();
    }
}
