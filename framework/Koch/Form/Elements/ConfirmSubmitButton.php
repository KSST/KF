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

use Koch\Form\FormElementInterface;

class ConfirmSubmitButton extends Input implements FormElementInterface
{
    protected $message = 'Please Confirm';

    public function __construct($message = null)
    {
        $this->type = 'submit';
        $this->value = _('Confirm & Submit');
        $this->class = 'ButtonGreen';
        if ($message != null) {
            $this->message = $message;
        }

        /**
         * Add the Form Submit Confirmation Javascript.
         * This is a pure Javacript Return Confirm.
         * To add the value of specific "form.elements" to the message
         * use: "+ form.elements['email'].value +"
         */
        $this->setAdditionalAttributeAsText(
            "onclick=\"if (confirm('Are you sure you want to submit this form?\\n
                \\nClick OK to submit or Cancel to abort.')) {
                submit(); } else {
                return false;
            } \" value=\"Submit\""
        );
    }

    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }
}
