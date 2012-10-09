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

class ButtonBar extends FormElement implements FormElementInterface
{
    /**
     * Definition Array for the Buttonbar
     * It defines the buttons to add as formelements to the buttonbar.
     * The default buttons are submit, reset and cancel.
     *
     * @var array $_buttons buttonname => button object
     */
    private $buttons = array('submitbutton' => '', 'resetbutton' => '', 'cancelbutton' => '');

    /**
     * Adds the objects to the buttonnames fo the initial buttons array
     *
     * @return Koch_Formelement_Buttonbar
     */
    public function __construct()
    {
        // apply CSS class attribute
        $this->setClass('buttonbar');

        return $this;
    }

    public function addButton($buttonname)
    {
        if (is_string($buttonname)) {
            // turn buttoname to formelement name (cancelbutton => CancelButton)
            $formelement = str_replace('button', 'Button', ucfirst($buttoname));
            // fetch the button-formelement
            $formelement = '\Koch\Form\Elements\\' . $formelement;

            $formelement = new $formelement;
        }

        // @todo use instanceof Koch_Formelement_Button
        if (is_object($buttonname) and (!$buttonname instanceof Koch_Formelement_Input)) {
            throw new \Koch\Exception\Exception('The button must a be formelement object.');
        }

        // attach button object to buttons array
        $this->buttons[$buttonname] = $formelement;

        return $this;
    }

    /**
     * Gets a button
     *
     * @param  string                     $_buttonname
     * @return Koch_Formelement_Buttonbar
     */
    public function getButton($buttonname)
    {
        if ($this->buttons[$buttonname] === null) {
            throw new \Koch\Exception\Exception(_('This button does not exist, so its not in this buttonbar: ') . $buttonname);
        }

        // return the button object
        if (is_object($this->buttons[$buttonname])) {
            return $this->buttons[$buttonname];
        }

        // instantiate the button object first and then return
        if (false === is_object($this->buttons[$buttonname])) {
            $this->addButton($buttonname);

            return $this->buttons[$buttonname];
        }
    }

    /**
     * Remove a button from the stack
     *
     * @param  string                     $_buttonname
     * @return Koch_Formelement_Buttonbar
     */
    public function removeButton($_buttonname)
    {
        if (isset($this->buttons[$_buttonname])) {
            unset($this->buttons[$_buttonname]);
        }

        return $this;
    }

    public function setCancelButtonURL($url)
    {
       $this->getButton('cancelbutton')->setCancelURL($url);

       return $this;
    }

    /**
     * Renders the buttonbar with all registered buttons
     *
     * @return $htmlString HTML Representation of Koch_Formelement_Buttonbar
     */
    public function render()
    {
        $htmlString = '<div class="' . $this->getClass() . '">';

        foreach ($this->buttons as $buttonname => $buttonobject) {
            if (is_object($buttonobject)) {
                $htmlString .= $buttonobject->render();
            } else {
                // does this ever happen???, see addButton!
                $formelement = '\Koch\Form\Elements\\' . ucfirst($buttonname);
                $formelement = new $formelement;
                $htmlString .= $formelement->render();
            }
        }

        $htmlString .= '</div>';

        return $htmlString;
    }
}
