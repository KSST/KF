<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards.
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
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
        $this->type = 'captcha';

        return $this;
    }

    /**
     * Set the name of the captcha.
     *
     * @param string $captcha The captcha name.
     *
     * @return Captcha \Koch\Form\Element\Captcha (THIS is not   \Koch\Form\Element\Captcha_$captcha )
     */
    public function setCaptcha($captcha = null)
    {
        // if no captcha is given, take the one definied in configuration
        if ($captcha === null) {
            $config  = Clansuite_CMS::getInjector()->instantiate('Koch\Config');
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
     * @param \Koch\Form\Element\Interface $captchaObject
     *
     * @return Captcha \Koch\Form\Element\Captcha
     */
    public function setCaptchaFormelement(\Koch\Form\ElementInterface $captchaObject)
    {
        $this->captchaObject = $captchaObject;

        return $this;
    }

    /**
     * Getter for the captchaObject.
     *
     * @return object \Koch\Form\Element\XYNAMECaptcha
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
     * The CaptchaFactory loads and instantiates a captcha object.
     */
    private function captchaFactory()
    {
        // camelCase rename,  cut the last 7 chars = "captcha"
        $name = mb_substr($this->captcha, 0, -7);

        $classname = 'Koch\Form\Formelements\\' . ucfirst($name) . 'Captcha';

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

        /*
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
