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

use Koch\Form\FormElement;
use Koch\Form\FormElementInterface;

/**
 * Formelement_Input
 *
 * @link http://www.whatwg.org/specs/web-apps/current-work/multipage/the-input-element.html
 */
class Input extends FormElement implements FormElementInterface
{
    /**
     * The formelement input type, e.g.
     * text, password, checkbox, radio, submit, reset, file, hidden, image, button.
     *
     * @var string
     */
    public $type;

    /**
     * custom css class
     *
     * @var string
     */
    public $class;

    /**
     * indicates whether checkbox is checked
     *
     * @var int
     */
    public $checked;

    /**
     * indicates whether radio button is selected
     *
     * @var int
     */
    public $selected;

    /**
     * length of field in letters
     *
     * @var int
     */
    public $size;

    /**
     * allowed length of input in letters
     *
     * @var int
     */
    public $maxlength;

    /**
     * disabled
     *
     * @var boolean
     */
    public $disabled;

    /**
     * additional string to attach to the opening form tag
     * for instance 'onSubmit="xy"'
     *
     * @var $string;
     */
    public $additional_attr_text;

    /**
     * description
     *
     * @var int
     */
    public $description;

    /**
     * A regular expression pattern, e.g. [A-Za-z]+\d+
     *
     * @var string
     */
    public $pattern;

    /**
     * String value for the placeholder attribute
     * <input placeholder="some placeholder">
     *
     * @var string
     */
    public $placeholder;

    /**
     * The readonly attribute specifies that an input field should be read-only.
     *
     * @var string
     */
    public $readonly;

    /**
     * Sets the state of the input field to read-only.
     *
     */
    public function setReadonly($readonly)
    {
        $this->readonly = (bool) $readonly;
    }

    /**
     * Set placeholder attribute value
     *
     * @link http://dev.w3.org/html5/spec/Overview.html#the-placeholder-attribute
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Get Placeholder <input placeholder="some placeholder">
     *
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * Set the regular expression pattern for client-side validation
     * e.g. [A-Za-z]+\d+
     *
     * @var string
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * defines length of field in letters
     *
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = (int) $size;
    }

    /**
     * defines allowed length of input in letters
     *
     * @param int $length
     */
    public function setMaxLength($length)
    {
        $this->maxlength = (int) $length;
    }

    /**
     * defines allowed length of input in letters
     *
     * @param boolean $disabled True or False.
     */
    public function setDisabled($disabled)
    {
        $this->disabled = (bool) $disabled;
    }

    /**
     * Set Additional Attributes as Text to formelement.
     *
     * @example
     * Setting the onclick attribute.
     * $this->setAdditionalAttributeText(' onclick="window.location.href=\''.$this->cancelURL.'\'"');
     *
     * @param string $additional_attr_text of this formelement.
     */
    public function setAdditionalAttributeAsText($additional_attr_text)
    {
        $this->additional_attr_text = $additional_attr_text;

        return $this;
    }

    /**
     * Renders the html code of the input element.
     *
     * @return string
     */
    public function render()
    {
        $html = null;
        $html .= '<input type="' . $this->type . '" name="' . $this->name . '"';
        $html .= (bool) $this->id ? ' id="' . $this->id . '"' : null;
        $html .= (bool) $this->value ? ' value="' . $this->value . '"' : null;
        $html .= (bool) $this->placeholder ? ' placeholder="' . $this->placeholder . '"' : null;
        $html .= (bool) $this->size ? ' size="' . $this->size . '"' : null;
        $html .= (bool) $this->readonly ? ' readonly="readonly"' : null;
        $html .= (bool) $this->disabled ? ' disabled="disabled"' : null;
        $html .= (bool) $this->maxlength ? ' maxlength="' . $this->maxlength . '"' : null;
        $html .= (bool) $this->pattern ? ' pattern="' . $this->pattern . '"' : null;
        $html .= (bool) $this->class ? ' class="' . $this->class . '"' : null;
        if ($this->type == 'image') {
            $html .= ' source="' . $this->source . '"';
            if ((bool) $this->width and (bool) $this->height) {
                $html .= ' style="width:' . $this->width . 'px; height:' . $this->height . 'px;"';
            }
        }
        $html .= (bool) $this->checked ? ' checked="checked"' : null;
        $html .= (bool) $this->additional_attr_text ? $this->additional_attr_text : null;
        $html .= (bool) $this->additional_attributes ? $this->renderAttributes($this->additional_attributes) : null;
        $html .= ' />' . CR;

        return $html;
    }
}
