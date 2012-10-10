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

namespace Koch\Form\Decorators\Form;

use Koch\Form\FormDecorator;

class Html5Validation extends FormDecorator
{
    /**
     * @var string Name of this decorator
     */
    public $name = 'html5validation';

    /**
     * Adds HTML5 form validation support to the form
     *
     * HTML5 validates forms without additional JavaScript.
     * Currently (September 2010) only Safari & Google Chrome support this functionality
     * The jQuery Plugin html5form validates formelements with html5 syntax in Firefox, Opera & Internet Explorer
     */
    public function addValidationJavascript()
    {
        $html = '';

        // add html5 validation support for FF,O,IE
        $html .= '<script type="text/javascript"';
        $html .= ' src="' . WWW_ROOT_THEMES_CORE . 'javascript/jquery/jquery.html5form-min.js">';

        $ident_form = '[Error] Form has no attribute "id" or "name"!';

        // to identify the form use the name or id
        if ( mb_strlen($this->getName()) > 0 ) {
             $ident_form .= $this->getName();
        }

        if ( mb_strlen($this->getId()) > 0 ) {
             $ident_form .= $this->getId();
        }

        // activate html5 syntax validation support on the form
        $html .= '<script>
                      $(document).ready(function(){
                          $(\'#' . $ident_form . '\').html5form();
                      });
                      </script>';

        return $html;
    }

    public function render($html_form_content)
    {
        if (true === is_file(ROOT_THEMES_CORE . 'javascript/jquery/jquery.html5form-min.js')) {
            // put all the pieces of html together
            return $this->addValidationJavascript() . $html_form_content;
        } else { // fail by prepending a message :(
            $message = '[ERROR] HTML5 Validation Support not available. File missing : <br/>'.
            ROOT_THEMES_CORE . 'javascript/jquery/jquery.html5form-min.js';

            return  $message . $html_form_content;
        }
    }
}
