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

use Koch\Form\FormElement;
use Koch\Form\FormElementInterface;

class Captcha extends FormElement implements FormElementInterface
{
    /**
     * @var string Name the Captcha Type: 'recaptcha', 'simplecaptcha', 'somenamecaptcha'.
     */
    private $captcha;

    /**
     * @var object The captcha object.
     */
    private $captchaObject;

    public function __construct()
    {
        // formfield type
        $this->type  = 'captcha';

        return $this;
    }

    /**
     * Set the name of the captcha
     *
     * @param  string $captcha The captcha name.
     * @return object Koch_Formelement_Captcha (THIS is not Koch_Formelement_Captcha_$captcha )
     */
    public function setCaptcha($captcha = null)
    {
        // if no captcha is given, take the one definied in configuration
        if ($captcha == null) {
            $config = Clansuite_CMS::getInjector()->instantiate('Koch\Config');
            $captcha = $config['antispam']['captchatype'];
            unset($config);
        }

        $this->captcha = mb_strtolower($captcha);

        return $this;
    }

    /**
     * @return string Name of the Captcha
     */
    public function getCaptcha()
    {

    }

    /**
     * @param  Koch_Formelement_Interface $captchaObject
     * @return object                     Koch_Formelement_Captcha
     */
    public function setCaptchaFormelement(Koch_Formelement_Interface $captchaObject)
    {
        $this->captchaObject = $captchaObject;

        return $this;
    }

    /**
     * Getter for the captchaObject
     *
     * @return object Koch_Formelement_XYNAMECaptcha
     */
    public function getCaptchaFormelement()
    {
        if (empty($this->captchaObject)) {
            return $this->setCaptchaFormelement($this->captchaFactory());
        } else {
            return $this->captchaObject;
        }
    }

    /**
     * The CaptchaFactory loads and instantiates a captcha object
     */
    private function captchaFactory()
    {
        // camelCase rename,  cut the last 7 chars = "captcha"
        $name = mb_substr($this->captcha, 0, -7);

        $classname = 'Koch\Form\Formelements\\'. ucfirst($name) . 'Captcha';

        $editor_formelement = new $classname();

        return $editor_formelement;
    }

    /**
     * At some point in the lifetime of this object you decided that
     * this captcha should be a captcha element of specific kind.
     * The captchaFactory will load the file and instantiate the captcha object.
     * But you already defined some properties like Name or Size for this captcha.
     * Therefore it's now time to transfer these properties to the captcha object.
     * Because we don't render this captcha, but the requested captcha object.
     */
    private function transferPropertiesToCaptcha()
    {
        // get captcha formelement
        $formelement = $this->getCaptchaFormelement();

        // transfer props from $this to captcha formelement
        $formelement->setRequired($this->required);
        $formelement->setLabel($this->label);
        $formelement->setName($this->name);
        $formelement->setValue($this->value);

        // a) attach an decorator of type formelement (chain returns the decorator)
        $formelement->addDecorator('formelement')
        // b) create a new formelement inside this decorator (chain returns the formelement)
                    ->newFormelement('input')
        // c) and attach some properties, like the required captcha value for later validation
                    ->setLabel($this->label)
                    ->setName($this->name);
                    #->setRequired()
                    #->setValidation();

        // return the formelement, to call e.g. render() on it
        return $formelement;
    }

    /**
     * Renders the captcha representation of the specific captcha formelement.
     *
     * @return $html HTML Representation of captcha formelement
     */
    public function render()
    {
        $html = '';
        $html = $this->getCaptchaFormelement()->transferPropertiesToCaptcha()->render();

        /**
         * at this point we have $_SESSION['user']['simple_captcha_string']
         * it's needed as string for the validation rule to the captcha formelement
         */
        // @todo validation object
        #$this->getCaptchaFormelement()->setRequired()->setValidator($validator);

        // renders the decorators of the captcha formelement
        foreach ($this->getCaptchaFormelement()->formelementdecorators as $formelementdecorator) {
            $html = $formelementdecorator->render($html);
        }

        #\Koch\Debug\Debug::firebug($html);

        return $html;
    }
}
