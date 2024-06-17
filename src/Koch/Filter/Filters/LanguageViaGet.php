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

namespace Koch\Filter\Filters;

use Koch\Config\Config;
use Koch\Filter\FilterInterface;
use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;

/**
 * Filter for Language Selection via URL.
 *
 * I10N/I18N Localization and Internationalization
 * Purpose: Set Language via URL by appendix $_GET['lang']
 *
 * 1) Default Language
 *    At system startup the default language is defined by the config.
 *    Up to this point this language is used for any output, like system and error messages.
 *
 * 2) When languageswitch_via_url is enabled in config, the user is able to
 *    override the default language (by adding the URL appendix 'lang').
 *    When request parameter 'lang' is set, the user session value for language will be updated.
 *    Example: index.php?lang=langname
 *
 * Note: The check if a certain language exists is not important,
 *       because there are 1) english hardcoded values and 2) the default language as fallback.
 */
class LanguageViaGet implements FilterInterface
{
    private $config = null;

    public function __construct(Config $config)
    {
        // only subarray is relevant
        $this->config = $config['prefilter'];
    }

    public function executeFilter(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        /*
         * take the initiative of filtering, if language switching is enabled in CONFIG
         * or pass through (do nothing) if disabled
         */
        if (true === (bool) $this->config['language_via_get']) {
            return;
        }

        // fetch URL parameter "&lang=" from $_GET['lang']
        $language = $request->getParameterFromGet('lang');

        if (isset($language) && (mb_strlen($language) === 2)) {
            /*
             * memorize in the user session
             * a) the selected language
             * b) that the language was set via $_GET parameter
             */
            $_SESSION['user']['language']         = mb_strtolower($language);
            $_SESSION['user']['language_via_get'] = 1;
        }
    }
}
