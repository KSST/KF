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

namespace Koch\Form\Generators;

use \Koch\Functions;

/**
 * Form Generator from a PHP Array description.
 *
 * Purpose: automatic form generation from an array.
 */
class PHPArray extends Form implements FormGeneratorInterface
{
    public function __construct(array $form_array = null, $form_object = null)
    {
        if (null != $form_array) {
            if ($form_object === null) {
                // init parent Koch_Form with name, method and action
                parent::__construct(
                    $form_array['form']['name'],
                    $form_array['form']['method'],
                    $form_array['form']['action']
                );
            } else {
                $form_object::__construct(
                    $form_array['form']['name'],
                    $form_array['form']['method'],
                    $form_array['form']['action']
                );
            }

            // unset the key form inside form_array
            // because the "form" description is no longer needed
            // parent Koch_Form is already informed
            unset($form_array['form']);

            $this->validateArrayAndgenerateForm($form_array);

            return $this;
        }
    }

    public function validateArrayAndgenerateForm($form_array)
    {
        // first we ensure, that the formdescription meets certain requirements
        if (self::validateFormArrayStructure($form_array)) {
            // now that the form description is valid, we generate the form
            $this->generateFormByArray($form_array);
        } else { // the formdescription is invalid
            throw new \Koch\Exception\Exception('Obligatory formelements not present.', 30);
        }
    }

    /**
     * Level 1 - The form
     *
     * $form_array_section is an array of the following structure:
     *
     * @todo $form_array_section description
     *
     * Level 2 - The formelements
     *
     * $form_array_element is an array of the following structure:
     *
     * Obligatory Elements to describe the Formelement
     * These array keys have to exist!
     *
     *   [id]            => resultsPerPage_show
     *   [name]          => resultsPerPage_show
     *   [label]         => Results per Page for Action Show
     *   [description]   => This defines the Number of Newsitems to show per Page in Newsmodule
     *   [formfieldtype] => text
     *
     * Optional Elements to describe the Formelement
     *
     *   [value] => 3
     *   [class] => cssClass
     *
     * @param $form_array the form array
     * @return boolean true/false
     */
    public static function validateFormArrayStructure($form_array)
    {
        $obligatory_form_array_elements = array('id', 'name', 'label', 'description', 'formfieldtype', 'value');
        $optional_form_array_elements   = array('class', 'decorator');

        // loop over all elements of the form description array
        foreach ($form_array as $form_array_section => $form_array_elements) {
            #\Koch\Debug\Debug::firebug($form_array_elements);
            #\Koch\Debug\Debug::firebug($form_array_section);

            foreach ($form_array_elements as $form_array_element_number => $form_array_element) {
                #\Koch\Debug\Debug::firebug(array_keys($form_array_element));
                #\Koch\Debug\Debug::firebug($obligatory_form_array_elements);

                // this does the validation. it ensures that required keys are present
                $report_differences_or_true = Functions::array_compare(
                    $obligatory_form_array_elements,
                    array_keys($form_array_element)
                );

                // errorcheck for valid formfield elements
                if (is_array($report_differences_or_true) == false) {
                    // form description arrays are identical
                    return true;
                } else {
                    // form description arrays are not identical
                    throw new \Koch\Exception\Exception(
                        'Form Array Structure not valid. The first array shows the obligatory form array elements.
                         The second array shows your form definition. Please add the missing array keys with values.'
                        . var_dump($report_differences_or_true)
                    );
                }
            }
        }
    }

    public function generateFormByArray($form_array)
    {
        // debug display incomming form description array
        #\Koch\Debug\Debug::firebug($form_array);

        // loop over all elements of the form description array
        foreach ($form_array as $form_array_section => $form_array_elements) {
            #\Koch\Debug\Debug::firebug($form_array_elements);
            #\Koch\Debug\Debug::firebug($form_array_section);

            foreach ($form_array_elements as $form_array_element_number => $form_array_element) {
                #\Koch\Debug\Debug::firebug($form_array_element);

                // @todo ensure these elements exist !!!

                // add a new element to this form, position it by it's number in the array
                $this->addElement($form_array_element['formfieldtype'], $form_array_element_number);

                // fetch the new formelement object by its positional number
                $formelement = $this->getElementByPosition($form_array_element_number);

                #\Koch\Debug\Debug::firebug($formelement);

                // and apply the settings (id, name, description, value) to it
                $formelement->setID($form_array_element['id']);

                // provide array access to the form data (in $_POST) by prefixing it with the formulars name
                // @todo if you group formelements, add the name of the group here
                $formelement->setName($this->getName().'['.$form_array_section.']['.$form_array_element['name'].']');
                $formelement->setDescription($form_array_element['description']);

                // @todo consider this as formdebug display (sets formname as label)
                #$formelement->setLabel($this->getName().'['.$form_array_element['name'].']');

                $formelement->setLabel($form_array_element['label']);

                // set the options['selected'] value as default value
                if ($form_array_element['options']['selected'] !== null) {
                    $formelement->setDefault($form_array_element['options']['selected']);
                    unset($form_array_element['options']['selected']);
                }

                /**
                 * check if $form_array_element['value'] is of type array or single value
                 * array indicates, that we have a request for
                 * something like a multiselect formfield with several options
                 */
                if (is_array($form_array_element['value']) == false) {
                    $formelement->setValue($form_array_element['value']);
                } else {
                    $formelement->setOptions($form_array_element['value']);
                }

                /**
                 * OPTIONAL ELEMENTS
                 */

                // if we have a class attribute defined, then add it (optional)
                if ($form_array_element['class'] !== null) {
                    $formelement->setClass($form_array_element['class']);
                }

                /**
                 * set a decorator for the formelement
                 * optional because: the default decorator would be active
                 */
                if ($form_array_element['decorator'] !== null) {
                    if ($form_array_element['decorator'] instanceOf Koch_Formelement_Decorator) {
                        $formelement->setDecorator($form_array_element['decorator']);
                    }
                }
            }
        }

        // unset the form description array, because we are done with it
        unset($form_array);

        return $this->render();
    }

    /**
     * Facade/Shortcut
     */
    public function generate($array)
    {
        $this->generateFormByArray($array);
    }

    public function generateArrayByForm()
    {
        // serialize an save the array
    }
}
