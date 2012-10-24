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
 * Renders jQuery Colorpicker for Color selection, you know?
 */
class JqSelectColor extends FormElement implements FormElementInterface
{
    /**
     * JQSelectColor uses jQuery Farbtastic Colorpicker
     */
    public function __construct()
    {
        $this->type = 'color';
    }

    public function getValue()
    {
        if (empty($this->value)) {
            // set a default color as return value
            return '#123456';
        }

        return $this->value;
    }

    public function render()
    {
        // add the javascripts to the queue of the page (@todo queue, duplication check)
        $javascript = '<script type="text/javascript"';
        $javascript .= ' src="'.WWW_ROOT_THEMES_CORE . 'javascript/jquery/jquery.farbtastic.js"></script>';
        $javascript .= '<link rel="stylesheet" href="'.WWW_ROOT_THEMES_CORE . 'css/farbtastic.css" type="text/css" />';

        /**
         * Add the jQuery UI Date Select Dialog.
         *
         * WARNING: the div dialog must be present in the dom,
         *          before you assign a js function to it via $('#datepicker')
         */
        $datepickerJs   = "<script type=\"text/javascript\">
                                          $(document).ready(function() {
                                            $('#colorpicker').farbtastic('#color');
                                            $('#colorpicker').hide();
                                            $('img#color').click(function(){
                                                $('#colorpicker').toggle();
                                            });
                                          });
                                        </script>";

        $html = '<input type="text" id="color" name="'.$this->getName().'" value="'.$this->getValue().'" />';
        $html .= '<img src="'.WWW_ROOT_THEMES_CORE . 'images/icons/colors.png"';
        $html .= ' align="top" style="margin-top:1px; margin-left:3px;" id="color"></img><div id="colorpicker"></div>';

        return $javascript.$datepickerJs.$html;
    }
}
