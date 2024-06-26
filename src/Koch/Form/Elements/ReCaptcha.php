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

namespace Koch\Form\Elements;

use Koch\Form\FormElementInterface;

class ReCaptcha extends Captcha implements FormElementInterface
{
    /**
     * @var string The ReCaptcha API PublicKey. You got this key from the ReCaptcha signup page.
     */
    private $publicKey;

    /**
     * @var string The ReCaptcha API PrivateKey.
     */
    private $privateKey;

    /**
     * @var object Instance of \Koch\Http\HttpRequest Object.
     */
    public function __construct()
    {
        $this->request = Clansuite_CMS::getInjector()->instantiate(\Koch\Http\HttpRequest::class);

        // Load Recaptcha Library
        // @todo replace with google/recaptcha
        // $recaptcha = new \ReCaptcha\ReCaptcha($secret);
        include_once VENDOR_PATH . '/recaptcha/recaptchalib.php';

        /*
         * Fetch publickey from config
         *
         * [recaptcha]
         * public_key  = ""
         * private_key = ""
         */
        $config           = Clansuite_CMS::getInjector()->instantiate('Koch\Config');
        $this->publicKey  = $config['recaptcha']['public_key'];
        $this->privateKey = $config['recaptcha']['private_key'];
        unset($config);
    }

    /**
     * Displays a ReCaptcha.
     */
    public function render()
    {
        return recaptcha_get_html($this->publicKey);
    }

    /**
     * Validates a ReCaptcha.
     *
     * In the code that processes the form submission, you need to add code to validate the CAPTCHA.
     */
    public function validate()
    {
        $response = recaptcha_check_answer(
            $this->privateKey,
            $this->request->getRemoteAddress(),
            $this->request->getPost('recaptcha_challenge_field'),
            $this->request->getPost('recaptcha_response_field')
        );

        if ($response->is_valid === false) {
            return _('The reCAPTCHA was not entered correctly. Try again. (recaptcha error ' . $response->error . ')');
        }
    }

    /**
     * Administrative Functions for ReCaptcha.
     */
    public function getAPIKey()
    {
        return recaptcha_get_signup_url();
    }
}
