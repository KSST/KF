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

class CancelButton extends Input implements FormElementInterface
{
    /**
     * Holds the url when canceling
     *
     * @var string
     */
    public $cancelURL = 'history.back(); return false;'; // depends on javascript

    public function __construct()
    {
        $this->type  = 'button';
        $this->value = _('Cancel');

        $this->class = 'CancelButton ButtonRed';
        $this->id    = 'CancelButton';
        $this->name  = 'CancelButton';
    }

    public function getCancelURL()
    {
        return $this->cancelURL;
    }

    /**
     * Sets the cancel URL (the url to redirect the user to, after clicking cancel)
     *
     * @example
     * $form->addElement('buttonbar')
     *        ->getButton('cancelbutton')->setCancelURL('index.php?mod=languages&sub=admin&action=show');
     *
     * @param string cancelURL The Cancel URL (By default wrapped by window.location.href="")
     * @param bool suppressWrapping Toggle for wrapping
     */
    public function setCancelURL($cancelURL, $suppressWrapping = false)
    {
        if ($suppressWrapping === false) {
            $this->cancelURL = 'window.location.href=\'' . $cancelURL . '\'';
        } else {
            $this->cancelURL = $cancelURL;
        }
    }

    public function render()
    {
        $this->setAdditionalAttributeAsText(' onclick="' . $this->getCancelURL() . '"');

        return parent::render();
    }
}
