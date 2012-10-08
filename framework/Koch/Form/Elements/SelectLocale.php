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

use Koch\Form\Elements\Select;
use Koch\Form\FormElementInterface;

class SelectLocale extends Select implements FormElementInterface
{
    /**
     * A locale drop-down select list
     *
     * You will find the value of the drop down in $_POST['locale']!
     */
    public function __construct()
    {
        // include locale arrays
        include KOCH_FRAMEWORK . 'gettext/locales.gettext.php';

        /**
         * prepare array structure for dropdown ( key => value )
         */
        $options = array();

        foreach ($l10n_sys_locales as $locale => $locale_array) {
            /**
             * Key is the locale name.
             *
             * a locale name has the form ll_CC.
             * Where ll is an ISO 639 two-letter language code, and CC is an ISO 3166 two-letter country code.
             * Both codes are separated by a underscore.
             *
             * For example, for German in Germany, ll is de, and CC is DE. The locale is "de_DE".
             * For example, for German in Austria, ll is de, and CC is AT. The locale is "de_AT".
             */
            $key = $locale;

            /**
             * Value consists of a long form of the language name and the locale code with hyphen.
             * This string will be displayed in the dropdown.
             * For example, "Deutsch/Deutschland (de-DE)" or "Suomi (fi-FI)".
             *
             * "lang-www" contains a hyphen and not an underscore!
             */
            $value = $locale_array['lang-native'] . ' (' . $locale_array['lang-www'] . ')';

            $options[$key] = $value;
        }

        $this->setOptions($options);

        $this->setLabel(_('Select Locale'));

        // You will find the value of the drop down in $_POST['locale']!
        $this->setName('locale');
    }
}
