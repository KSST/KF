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
 *
 */
class Select extends FormElement implements FormElementInterface
{
    /**
     * @var array array with options for the dropdown
     */
    public $options;

    /**
     * @var string name of the default option of the select dropdown (pre-selection)
     */
    public $default;

    /**
     * @var string description
     */
    public $description;

    /**
     * 0 = pure dropdown with 1 field
     * 3 = 3 elements shown, rest available via scrollbar
     * @var int number of displayed items
     */
    public $size;

    public $withValuesAsKeys;

    /**
     * @var string Label
     */
    #public $label ='Select an item from this pull-down menu.';

    public function __construct()
    {
        $this->type = 'select';
    }

    /**
     * Sets the array with options for the dropdown element.
     *
     * @param  array                    $options
     * @param  boolean                  $addSelectText Adds " - Select -" as first entry to the options array. Default true.
     * @return \Koch_Formelement_Select
     */
    public function setOptions($options, $addSelectText = true)
    {
        if ($addSelectText === true) {
            // add one entry on top for the dropdown
            array_unshift($options, _('Select...'));
        }

        $this->options = $options;

        return $this;
    }

    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * This sets the default value.
     * Value is used to mark that option as "selected"
     */
    public function setDefaultValue($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Render option tags with value => value relation
     *
     * <option value="' . $value. '">' . $value . '</option>
     *
     * and not as a key => value relation
     *
     * <option value="' . $key . '">' . $value . '</option>
     *
     * This makes it a bit easier to pass actual values around via POST,
     * instead of passing the numeric index for lookup.
     *
     * @return \Koch_Formelement_Select
     */
    public function withValuesAsKeys()
    {
        $this->withValuesAsKeys = true;

        return $this;
    }

    public function render()
    {
        // open the html select tag
        $html = '';
        $html .= '<select ';
        $html .= (bool) $this->name ? 'name="'.$this->name.'"' : null;
        $html .= (bool) $this->id ? 'id="'.$this->id.'"' : null;
        $html .= (bool) $this->class ? 'class="'.$this->class.'"' : null;
        $html .= (bool) $this->size ? 'size="'.$this->size.'"' : null;
        $html .= '>';

        /**
         * This handles the default value setting via the options array key "selected".
         * It grabs the first element in the options array, which keyname should be 'selected'
         * and then removes it, setting its value to $this->default.
         *
         * The check for empty($this->default) is important, because the default value might already
         * be set via setDefaultValue(). Such a scenario is given, when the form is generated via array.
         * The array would contain options['selected'] with an default value, but the actual default
         * value is incomming via setDefaultValue().
         *
         * Note: If the options array is incomming via a formgenerator: the generator has already performed this step!
         * $this->setDefault(options['selected']);
         */
        if ($this->options['selected'] !== null and empty($this->default)) {
            $this->default = $this->options['selected'];
            unset($this->options['selected']);
        }

        if (empty($this->options) === false) {
            // loop over all selectfield options
            foreach ($this->options as $key => $value) {
                if ($this->withValuesAsKeys === true) {
                    $html .= $this->renderOptionTag($value, $value);
                } else {
                    $html .= $this->renderOptionTag($key, $value);
                }
            }
        } else {
            $html .= '<option value="0">No Options given.</option>';
        }

        // close the html select tag
        $html .= '</select>';

        return $html;
    }

    private function renderOptionTag($key, $value)
    {
        /**
         * the addSelectText would be posted as value.
         * in order to be able to use empty() on the incomming post array variables,
         * we need to remove it. this makes it just a select helper, without data.
         */
        if ($key == 'Select...') {
            $key = '';
        }

        // check if the value is the default one and in case it is, add html "selected"
        if ($key == $this->default) {
            return '<option value="' . $key . '" selected="selected">' . $value . '</option>';
        } else { // a normal select element is rendered

            return '<option value="' . $key . '">' . $value . '</option>';
        }
    }

}
