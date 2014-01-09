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

class JqConfirmSubmitButton extends Input implements FormElementInterface
{
    protected $message = 'Please Confirm';

    /**
     * @var string $formid Takes the name of the form (to trigger the original sumbit).
     */
    protected $formid;

    public function __construct()
    {
        $this->type = 'submit';
        $this->value = _('Confirm & Submit');
        $this->class = 'ButtonGreen';

        /**
         * Add the Form Submit Confirmation Javascript.
         * This is a jQuery UI Modal Confirm Dialog.
         *
         * a) To add the value of specific form.elements to the message use "+ form.elements['email'].value +"
         * b) Take care, that the div dialog is present in the DOM, BEFORE you assign function to it via $('#dialog')
         *
         */
        $this->description = "<div id=\"dialog\" title=\"Verify Form\">
                                  <p>If your is correct click Submit Form.</p>
                                  <p>To edit, click Cancel.<p>
                              </div>

                              <script type=\"text/javascript\">

                               // jQuery UI Dialog

                               $('#dialog').dialog({
                                    autoOpen: false,
                                    width: 400,
                                    modal: true,
                                    resizable: false,
                                    buttons: {
                                        \"Submit Form\": function () {
                                            document.".$this->formid.".submit();
                                        },
                                        \"Cancel\": function () {
                                            $(this).dialog(\"close\");
                                        }
                                    }
                                });


                              $('form#".$this->formid."').submit(function () {
                                $('#dialog').dialog('open');

                                 return false;
                               });
                              </script>
                             ";
    }

    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    public function setFormId($formid)
    {
        $this->formid = $formid;

        return $this;
    }
}
