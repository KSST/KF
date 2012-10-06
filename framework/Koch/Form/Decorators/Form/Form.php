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

class Form extends FormDecorator
{
    /**
     * Name of this decorator
     *
     * @var string
     */
    public $name = 'form';

    public function openOpenFormTag()
    {
        // init var
        $html_form = '';

        // init html form with an comment
        $html_form = '<!-- Start of Form "'. $this->getName() .'" -->' . CR;

        // open the opening form tag
        $html_form .= '<form ';

        return $html_form;
    }

    /**
     * @return string returns html of attributes inside the opening form tag
     */
    public function getFormTagAttributesAsHTML()
    {
        // init var
        $html_form = '';

        if ( mb_strlen($this->getID()) > 0 ) {
            $html_form .= 'id="'.$this->getID().'" ';
        }

        if ( mb_strlen($this->getAction()) > 0 ) {
            $html_form .= 'action="'.$this->getAction().'" ';
        }

        if ( mb_strlen($this->getMethod()) > 0 ) {
            $html_form .= 'method="'.$this->getMethod().'" ';
        }

        if ( mb_strlen($this->getEncoding()) > 0 ) {
            $html_form .= 'enctype="'.$this->getEncoding().'" ';
        }

        if ( mb_strlen($this->getTarget()) > 0 ) {
            $html_form .= 'target="'.$this->getTarget().'" ';
        }

        if ( mb_strlen($this->getName()) > 0 ) {
             $html_form .= 'name="'.$this->getName().'" ';
        }

        if ( mb_strlen($this->getAcceptCharset()) > 0 ) {
             $html_form .= 'accept-charset="'.$this->getAcceptCharset().'" ';
        }

        if ( $this->getAcceptCharset() === true ) {
             $html_form .= ' autocomplete ';
        }

        if ( $this->getNoValidation() === true ) {
             $html_form .= ' novalidation ';
        }

        $html_form .= 'class="form '.$this->getClass().'"';

        // return the attributes inside the opening form tag
        return $html_form;
    }

    public function closeOpenFormTag()
    {
        // close the opened form tag
        return '>' . CR;
    }

    public function addHeading()
    {
        $html_form = '';

        // add heading
        if ( mb_strlen($this->getHeading()) > 0 ) {
             $html_form = '<h2>'.$this->getHeading().'</h2>' . CR;
        }

        return $html_form;
    }

    public function addDescription()
    {
         $html_form = '';

        // add description
        if ( mb_strlen($this->getDescription()) > 0 ) {
             $html_form = '<p>'.$this->getDescription().'</p>' . CR;
        }

        return $html_form;
    }

    public function closeFormTag()
    {
        // close form
        return CR . '</form>' . CR . '<!--- End of Form "'. $this->getName() .'" -->' . CR;
    }

    public function render($html_form_content)
    {
        // put all the pieces of html together
        $html_form_content = $this->openOpenFormTag().              // <form
                             $this->getFormTagAttributesAsHTML().   //  id/method/action/...
                             $this->closeOpenFormTag().             // >
                             $this->addHeading().                   // heading
                             $this->addDescription().               // description
                             $html_form_content.                    // formelements
                             $this->closeFormTag();                 // </form>

        return $html_form_content;
    }
}
