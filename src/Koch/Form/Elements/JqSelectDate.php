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

use Koch\Form\FormElement;
use Koch\Form\FormElementInterface;

/**
 *Renders jquery Datepicker for date selection, you know?
 */
class JqSelectDate extends FormElement implements FormElementInterface
{
    /**
     * Flag Variable for the output of the datepicker as an icon (if true).
     */
    protected $asIcon = false;

    /**
     * contains the datepicker html string for output.
     */
    protected $html = '<div type="text" id="datepicker"></div>';

    /**
     * Datepicker Attributes Array contains the options for the javascript object.
     *
     * @var array
     */
    private $attributes = [];

    /**
     * Contains a sprintf javascript function string.
     *
     * @var string
     */
    private $sprintf_datepicker_js = '<script type="text/javascript">
                                         $(document).ready(function () {
                                            $("#%s").datepicker({
                                            %s
                                         }); });</script>';

    /**
     * contains the javascript libraries.
     *
     * @var string
     */
    private $jsLibraries = '';

    public function __construct()
    {
        $this->type = 'date';
        $this->name = 'datepicker';

        // dependencies
        // <script type="text/javascript" src="http://jqueryui.com/latest/jquery-1.3.2.js"></script>
        // <script type="text/javascript" src="http://jqueryui.com/latest/ui/ui.core.js"></script>
        $this->jsLibraries = '<link type="text/css" href="http://jqueryui.com/latest/themes/base/ui.all.css"'
            . 'rel="stylesheet" /><script type="text/javascript" src="http://jqueryui.com/latest/ui/ui.datepicker.js">'
            . '</script>';
    }

    /**
     * Returns the generated.
     */
    public function getJavascript()
    {
        return $this->jsLibraries . sprintf($this->sprintf_datepicker_js, $this->getName(), $this->getAttributes());
    }

    /**
     * Gets the datepicker attributes array.
     *
     * @see setAttributes
     */
    public function getAttributes()
    {
        $html = '';
        foreach ($this->attributes as $attribute => $value) {
            $html .= $attribute . ':"' . $value . '",' . CR;
        }

        return $html;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes += $attributes;

        return $this;
    }

    /**
     * Sets a single attribute to the datepicker attributes array.
     *
     * @see setAttributes
     */
    public function setAttribute($attribute, $value)
    {
        $new_attribute             = [];
        $new_attribute[$attribute] = $value;
        $this->setAttributes($new_attribute);

        return $this;
    }

    /**
     * Adjusts datepicker options and html output to display the datepicker as an icon.
     */
    public function asIcon()
    {
        // define relevat attributes to display the datepicker as an icon
        $datepickerAttributes = [
            'firstDay'        => '1',
            'format'          => 'yy-mm-dd',
            'showOn'          => 'button',
            'buttonImage'     => 'themes/core/images/lullacons/calendar.png',
            'buttonImageOnly' => 'true',
            'constrainInput'  => 'false',
        ];

        // set the relevant attributes
        $this->setAttributes($datepickerAttributes);

        // datepicker icon trigger needs a input element, so we replace the original (div) string
        $this->html = '<input type="text" id="datepicker">';

        return $this;
    }

    /**
     * Adds the jQuery UI Date Select Dialog.
     */
    public function render()
    {
        $html = '';
        $html .= $this->html;

        /*
         * Watch out!
         * The div dialog must be present in the dom,
         * before you assign JS functions to it via $('#datepicker')
         */
        $html .= $this->getJavascript();

        return $html;
    }
}
