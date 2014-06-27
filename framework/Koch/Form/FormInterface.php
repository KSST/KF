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

namespace Koch\Form;

/**
 * Interface for the Form handling class.
 */
interface FormInterface
{
    // output the html representation of the form
    public function render();

    // set action, method, name

    /**
     * @return Form
     */
    public function setAction($action);

    /**
     * @return Form
     */
    public function setMethod($method);

    /**
     * @return Form
     */
    public function setName($method);

    // add/remove a formelement
    public function addElement($formelement, $position = null);

    /**
     * @return boolean
     */
    public function delElementByName($name);

    // load/save the XML description of the form
    #public function loadDescriptionXML($xmlfile);
    #public function saveDescriptionXML($xmlfile);

    // callback for validation on the whole form (all formelements)
    #public function processForm();
}
