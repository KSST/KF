<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
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
     * @return ButtonBar
     */
    public function __construct()
    {
        // apply CSS class attribute
        $this->setClass('buttonbar');

        return $this;
    }

    /**
     * @param string $buttonname
     */
    public function addButton($buttonname)
    {
        if (is_string($buttonname)) {
            $formelement = str_replace('button', 'Button', ucfirst($buttoname));
            $formelement = '\Koch\Form\Elements\\' . $formelement;
            $formelement = new $formelement;
        }

        if (!$formelement instanceof \Koch\Form\Element\Button) {
            throw new \Koch\Exception\Exception('The button must a be formelement object.');
        }

        $this->buttons[$buttonname] = $formelement;

        return $this;
    }

    /**
     * Gets a button
     *
     * @param  string                       $buttonname
     * @return \Koch\Form\Element\Buttonbar
     */
    public function getButton($buttonname)
    {
        if ($this->buttons[$buttonname] === null) {
            throw new \Koch\Exception\Exception(
                _('This button does not exist, so its not in this buttonbar: ') . $buttonname
            );
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
     * @param  string    $_buttonname
     * @return ButtonBar
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
     * @return $htmlString HTML Representation of   \Koch\Form\Element\Buttonbar
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
