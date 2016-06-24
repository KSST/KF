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

namespace Koch\Form\Generator;

use Koch\Form\Form;
use Koch\Form\FormGeneratorInterface;

/**
 * Form Generator from XML description.
 *
 * Purpose:
 * 1) form generation (html representation) from an xml description file (xml->form(html))
 * 2) xml generation from an array description of the form (form(array) ->xml).
 */
class XML extends Form implements FormGeneratorInterface
{
    /**
     * Facade/Shortcut.
     */
    public function generate($array)
    {
        $this->generateFormByXML($array);
    }

    /**
     * Generates a formular from a XML description file.
     *
     * @param string $filename XML file with formular description.
     *
     * @return \Koch_Array_Formgenerator
     */
    public function generateFormByXML($filename)
    {
        // XML -> toArray -> Koch_Array_Formgenerator->generate($array)
        $array = [];
        $array = new Koch\Config($filename);

        #\Koch\Debug\Debug::firebug($filename);
        #\Koch\Debug\Debug::firebug($array);
        $form = '';
        $form = new Koch_Array_Formgenerator($array);

        #\Koch\Debug\Debug::firebug($form);

        return $form;
    }

    /**
     * Generates a XML Form Description File from an form describing array.
     *
     * @param $array
     */
    public function generateXMLByArray($array)
    {
        /* $filename = APPLICATION_MODULES_PATHULES . $array['modulename'] . DIRECTORY_SEPARATOR . 'forms/';
          $filename .= $array['actionname'] . 'form.xml.php';

          \Koch\Config\XML::write($filename, $array);
         */
    }
}
