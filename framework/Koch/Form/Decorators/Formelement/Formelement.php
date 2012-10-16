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

namespace Koch\Form\Decorators\Formelement;

use Koch\Form\FormelementDecorator;

/**
 * Formelement_Decorator_Formelement
 *
 * This decorator decorates a formelement (A) with another formelement (B).
 *
 * By doing this you could Live without formdecorators is easy. You would add the two formelements (A+B) to a form.
 * But consider a situation where you want to add only formelement (A) to the form.
 * From inside formelement (A) you can't reach the form to add another formelement (B).
 * But you can reach the addDecorator() method. And at this point this class comes in.
 * It utilizes the autoloader to get the formelement (B).
 *
 * @category Koch
 * @package Koch\Form
 * @subpackage Koch\Form\Decorator
 */
class Formelement extends FormelementDecorator
{
    /**
     * @var string Name of this decorator
     */
    public $name = 'formelement';

    /**
     * @var string Name of the new formelement (B) to decorate the existing formelement (A) with.
     */
    private $formelementname;

    /**
     * @var object The formelement object
     */
    private $formelement_object;

    /**
     * Setter method for formelementname and instantiation of a new formelement.
     *
     * <strong>WATCH IT! THIS BREAKS THE CHAINING IN REGARD TO THE DECORATOR.</strong>
     *
     * @param  string $formelementname Name of the new formelement (B) to decorate the existing formelement (A) with.
     * @return object Instance of formelement.
     */
    public function newFormelement($formelementname)
    {
        // set name of the formelement to class
        $this->formelementname = $formelementname;

        // instantiate, set to class and return formelement object
        return $this->formelement_object = new $formelementname;
    }

    /**
     * renders new formelement (B) AFTER formelement (A)
     */
    public function render($html_formelement)
    {
        if (is_object($this->formelement_object)) {
            // WATCH THE DOT to render after formelement (A)
            $html_formelement .= CR . $this->formelement_object->render();

            return $html_formelement;
        }
    }
}
