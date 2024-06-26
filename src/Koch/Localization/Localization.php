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

namespace Koch\Localization;

use Koch\Config\Config;

/**
 * Class for Localization (l10n) & Internationalization (i18n) Handling.
 */
class Localization
{
    // Locale Variables
    public $locale;

    /**
     * @var Set Locale Defaults: the textdomain.
     */
    private $domain;

    /**
     * @var Sets Encoding.
     */
    private $encoding;

    // References
    private static $config;

    public function __construct(Config $config)
    {
        // Set Reference to Config
        self::$config = $config;

        // Set Locale Defaults
        $this->domain   = strtolower(APPLICATION_NAME);
        $this->encoding = self::$config['locale']['outputcharset'];

        // Get Locale
        $locale = $this->getLocale();

        /*
         * Important Notice:
         *
         * List new available languages in the method head of getLanguage( $supported=array( 'en', 'de' ))
         * to make them a valid target for a browser detected language!
         */

        /*
         * Require PHP-gettext's emulative functions, if PHP gettext extension is off
         *
         * The library provides a simple gettext replacement that works independently from
         * the system's gettext abilities.
         * It can read the MO files and use them for the translation of strings.
         * It can also cache the mo files.
         * Note that gettext.inc includes gettext.php and streams.php.
         *
         * Review the following articles/manual to understand how this works:
         * @link http://www.php.net/gettext PHP Manual Gettext
         * @link https://launchpad.net/php-gettext/ PHP-GETTEXT Library
         * @link http://www.gnu.org/software/gettext/manual/gettext.html GNU Gettext
         */
        if (function_exists('T_setlocale') === false) {
            include VENDOR_PATH . 'php-gettext/php-gettext/gettext.inc';
        }

        // Load Domain
        $this->loadTextDomain($this->domain, $locale);
    }

    /**
     * Get Locale.
     *
     * Order of Language-Detection:
     * URL (language filter) -> SESSION -> BROWSER -> DEFAULT LANGUAGE (from Config)
     */
    public function getLocale()
    {
        // if language_via_url was used, the filter set the URL value to the session
        if (isset($_SESSION['user']['language_via_url']) && ($_SESSION['user']['language_via_url'] === 1)) {
            // use language setting from session
            $this->locale = $_SESSION['user']['language'];
        } else {
            // get language from browser
            $this->locale = $this->getLanguage();

            if (empty($this->locale)) { // 3) get the default language from config as fallback
                $this->locale = self::$config['locale']['default'];
            }

            $_SESSION['user']['language'] = $this->locale;
        }

        return $this->locale;
    }

    /**
     * loadTextDomain.
     *
     * Loads a new domain using a certain path into the domain table.
     *
     * Note on gettext paths:
     * Give a path/to/your/mo/files without LC_MESSAGES and locale!
     * If you use: T_bindtextdomain($this->domain, '/html/locales');
     * The mo.file would be looked up in /html/locales/de_DE/LC_MESSAGES/{$this->domain}.mo
     *
     * @link http://www.php.net/function.bindtextdomain
     *
     * @param string $domain
     * @param string $module
     */
    public function loadTextDomain($domain, $locale, $module = null)
    {
        // if, $locale string is not over 3 chars long -> $locale = "en", build "en_EN"
        if (isset($locale[3]) === false) {
            $locale = mb_strtolower((string) $locale) . '_' . mb_strtoupper((string) $locale);
        }

        // Environment Variable LANGUAGE has priority above any local setting
        putenv('LANGUAGE=' . $locale);
        putenv('LANG=' . $locale);
        setlocale(LC_ALL, $locale . '.UTF-8');
        T_setlocale(LC_ALL, $locale . '.UTF8', $locale);

        /*
         * Set the domain_directory (where look for MO files named $domain.po)
         */
        if ($module === null) {
            //  it's the ROOT_LANGUAGES directory
            $domainDirectory = ROOT_LANGUAGES;
        } else { // set a specific module directory
            $domainDirectory = APPLICATION_MODULES_PATH . $module . '/languages';
        }

        // Set the Domain
        T_bindtextdomain($domain, $domainDirectory);
        T_bind_textdomain_codeset($domain, $this->encoding);
        T_textdomain($domain);

        /*\Koch\Debug\Debug::firebug(
           '<p>Textdomain "' .$domain .'" loaded from path "'. $domain_directory .'" for "'. $module .'"</p>'
        );*/

        return true;
    }

    /**
     *  getLanguage.
     *
     *  This function will return a language, which is supported by both
     *  the browser and the application language system.
     *
     *  @param $supported   (optional) An array with the list of supported languages.
     *                      Default Setting is 'en' for english.
     *
     *  @return $language Returns a $language string, which is supported by browser and system.
     */
    public function getLanguage($supported = ['en', 'de'])
    {
        // start with the default language
        $language = $supported[0];

        // get the list of languages supported by the browser
        $browserLanguages = $this->getBrowserLanguages();

        // look if the browser language is a supported language, by checking the array entries
        foreach ($browserLanguages as $browserLanguage) {
            // if a supported language is found, set it and stop
            if (in_array($browserLanguage, $supported, true)) {
                $language = $browserLanguage;
                break;
            }
        }

        // return the found language
        return $language;
    }

    /**
     * Browser Locale Detection.
     *
     * This functions check the HTTP_ACCEPT_LANGUAGE HTTP-Header
     * for the supported browser languages and returns an array.
     *
     * Basically HTTP_ACCEPT_LANGUAGE locales are composed by 3 elements:
     * PREFIX-SUBCLASS ; QUALITY=value
     *
     * PREFIX:      is the main language identifier
     *              (i.e. en-us, en-ca => both have prefix EN)
     * SUBCLASS:    is a subdivision for main language (prefix)
     *              (i.e. en-us runs for english - united states) IT CAN BE BLANK
     * QUALITY:     is the importance for given language
     *              primary language setting defaults to 1 (100%)
     *              secondary and further selections have a lower Q value (value <1).
     * EXAMPLE:     de-de,de;q=0.8,en-us;q=0.5,en;q=0.3
     *
     * @return array containing the list of supported languages
     */
    public function getBrowserLanguages()
    {
        // check if environment variable HTTP_ACCEPT_LANGUAGE exists
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) === false) {
            // if not return an empty language array
            return [];
        } elseif (extension_loaded('intl')) {
            /*
             * Try to find best available locale based on HTTP "Accept-Language" header
             * via Locale class, which is part INTL, a php default extension as of php 5.3.
             */
            $lang = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);

            return (array) mb_substr($lang, 0, 2);
        } else { // fallback for non "ext/intl" environments
            // explode environment variable HTTP_ACCEPT_LANGUAGE at ,
            $browserLanguages = explode(',', (string) $_SERVER['HTTP_ACCEPT_LANGUAGE']);

            // convert the headers string to an array
            $browserLanguagesSize = count($browserLanguages);

            for ($i = 0; $i < $browserLanguagesSize; ++$i) {
                // explode string at ;
                $browserLanguage = explode(';', $browserLanguages[$i]);
                // cut string and place into array
                $browserLanguages[$i] = mb_substr($browserLanguage[0], 0, 2);
            }

            // remove the duplicates and return the browser languages
            return array_values(array_keys(array_flip($browserLanguages)));
        }
    }
}
