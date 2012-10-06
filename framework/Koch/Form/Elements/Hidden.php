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

use Koch\Form\Elements\Input;
use Koch\Form\FormElementInterface;

class Hidden extends Input implements FormElementInterface
{
    public function __construct()
    {
        $this->type = 'hidden';

        /**
         * Convention: Default decorators are disabled on hidden formelements!
         *
         * When useDefaultDecorators() is activated on the form,
         * the default decorators would be applied to any formlement before rendering.
         * Rendering of a decorators, like e.g. the "label" decorator, rendering the "label" tag,
         * is unappreciated, because this element should obviously be "hidden".
         *
         * If you want a "non-default" decorator on this element, then use addDecorator().
         */
        $this->disableDefaultDecorators();
    }

    /**
     * Adds a hidden field for charset detection.
     *
     * @return string
     */
    public function addHiddenFieldForCharsetDetection()
    {
        return '<input name="_charset_" type="hidden" />';
    }

    /**
     * Assigns an array to a hidden field by combining the array into a string.
     * The separation char is ",".
     *
     * When the string is incomming via POST you need to explode it,
     * to get the array back. Like so:
     * $data_array = explode(',', $_POST['imploded_array']);
     *
     * The method sets the Name implicitly.
     *
     * @param string|array $value The data you want to pass through POST.
     */
    public function setValue($value)
    {
        $data = '';

        if (is_array($value)) {
            // transform the array to a string by imploding it with comma
            $data = implode(',', $value);

            /**
             * Add imploded_array to the name.
             *
             * By appending the state of the array to the name, it's marked
             * to be exploded again, when incomming as $_POST data.
             */
            $this->setName('_imploded_array');
        } elseif ((is_string($value) === true) or (is_numeric($value) === true)) {
            $data = $value;
        } else {
            $msg = _('%s() only accepts array, string or numeric as $value. Your input was (%s) %s.');
            $msg = sprintf($msg, __METHOD__, gettype($value), $value);
            throw new \InvalidArgumentException($msg);
        }

        $this->value = htmlspecialchars($data);

        unset($data);
    }

    /**
     * Proxy / Convenience Method for setName() and setValue() (a two in one call)
     *
     * @param type         $name
     * @param string|array $value The data you want to pass through POST.
     */
    public function setData($name, $value)
    {
        $this->setName($name);
        $this->setValue($value);
    }
}
