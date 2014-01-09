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

/**
 * Renders a simple image captcha formelement.
 */
class SimpleCaptcha extends Captcha implements FormElementInterface
{
    public $name = 'simplecaptcha';
    public $type = 'captcha';

    /**
     * display captcha
     */
    public function render()
    {
        $captcha = new \Koch\Captcha();

        // \Koch\Debug\Debug::firebug('Last Captcha String = '.$_SESSION['user']['simple_captcha_string']);
        return $captcha->generateCaptchaImage();
    }

    /**
     * validate captcha
     *
     * In the code that processes the form submission, you need to add code to validate the CAPTCHA.
     */
    public function validate()
    {
        // @todo comparision of form input with session string
        // $_SESSION['user']['simple_captcha_string']
    }
}
