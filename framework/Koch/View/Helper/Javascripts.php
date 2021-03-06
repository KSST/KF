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

namespace Koch\View\Helper;

/**
 * Koch Framework - Class for initializing JS and CSS libraries.
 *
 * Javascripts is a (View) Helper Library and Service Gateway for all
 * the Javascript and CSS Libraries utilized.
 *
 * Purpose: Standardization of Initialization and Loading of single Files and Libraries
 *          Provide easier Access to often spread resources
 *
 * We have the following directories:
 * For single (non-library) files: "/themes/core/css" and "/themes/core/javascripts/".
 * For libraries with own subfolders for css, js, misc stuff: "/themes/core/libraries/".
 */
class Javascripts extends Layout
{
    public static function addJQuery()
    {
        self::addJS('jquery/jquery.js');
        self::addJS('jquery/jquery.ui.js');
    }

    public static function addLightbox()
    {
        self::addJS('lightbox/scripts/lightbox.js');
        self::addCSS('lightbox/css/lightbox.css');
    }

    public static function addOverlib()
    {
        self::addJS('overlib/overlib.js');
    }

    public static function addTabpane()
    {
        self::addJS('tabpane/tabpane.js');
    }

    public static function addClip()
    {
        self::addJS('clip');
    }

    /**
     * Adds a JQuery Link, which fetches directly from jquery.com.
     *
     * If you don't specifiy the version parameter, the latest version will be fetched.
     * This procedure is implemented to provide an easy of developing with the latest release of JQuery.
     * It has several problems:
     * 1) incompatibilities of the latest version to dependent local javascripts
     * 2) the download depends on jquery.com and not on your own domain
     * 3) this adds one external reference to the page loading and requires a DNS-lookup
     * 4) if you use the applicaiton as an intranet system or offline, it will simply be not loadable
     *
     * The best practice usage is to provide a version number.
     * The versions are whitelisted to keep a certain compatibilty frame.
     *
     * @param $version string The JQuery Version Number to load, like "1.3.2".
     * @param $service string jquery for download from code.jquery.com, google for google.com/jsapi
     */
    public static function addJQueryService($version = null, $service = 'google')
    {
        // determine service
        if ($service === 'jquery') {
            // load from jquery.com
            if ($version === null) {
                self::addJS('http://code.jquery.com/jquery-latest.pack.js');
            } else {
                /*
                 * JQuery version whitelist ensures a certain compatibilty frame
                 */
                $jquery_version_whitelist = ['1.4.2', '1.4.1']; // not 'latest'

                if (in_array($version, $jquery_version_whitelist, true)) {
                    self::addJS('http://code.jquery.com/jquery-' . $version . '.pack.js');
                }
            }
        } else {
            // load from google.com
            self::addJS_JQuery_GoogleCDN_Service($version);
        }
    }

    /**
     * Adds a JQuery Link, which fetches directly from google.com CDN.
     *
     * If you don't specifiy the version parameter, the latest version will be fetched.
     * For problems with this approach, @see addJS_JQuery_Service.
     *
     * The best practice usage is to provide a version number.
     * The versions are whitelisted to keep a certain compatibilty frame.
     *
     * @param $version string The GoogleCDN Version Number to load, like "1.3.2".
     */
    public static function addJQueryGoogleCDNService($version = null)
    {
        if ($version === null) {
            $version = 'latest';
        } else {
            /*
             * JQuery version whitelist ensures a certain compatibilty frame
             */
            $jquery_version_whitelist = ['1.7.2', '1.8.1']; // not 'latest'

            if (in_array($version, $jquery_version_whitelist, true)) {
                $this->jquery_initscript = '';
                $this->jquery_initscript .= "    <script src=\"http://www.google.com/jsapi\"></script>\n";
                $this->jquery_initscript .= "    <script>\n";
                $this->jquery_initscript .= "      google.load('jquery', '{$version}');\n";
                $this->jquery_initscript .= "      var $j = jQuery.noConflict();;\n";
                $this->jquery_initscript .= "    </script>\n";

                return $this->jquery_initscript;
            }
        }
    }

    /** Wrapper Methods **/

    /**
     * addMultipleJS - Wrapper Method.
     *
     * @params array  array with several filenames and their paths
     * $filenames['path','filename']
     */
    public static function addMultipleJS($filenames)
    {
        if (is_array($filenames)) {
            foreach ($filenames as $filename) {
                $js_file = WWW_ROOT_THEMES_CORE . 'javascript/' . $filename . '.js';

                return '<script src="' . $js_file . '" type="text/javascript"></script>' . CR;
            }
        }
    }

    /**
     * addJS - Wrapper Method.
     *
     * @params string javascript filename to load
     *
     * @param string $filename
     */
    public static function addJS($filename)
    {
        $js_file = WWW_ROOT_THEMES_CORE . 'javascript/' . $filename . '.js';

        return '<script src="' . $js_file . '" type="text/javascript"></script>' . CR;
    }

    /**
     * addJSInit - Wrapper Method.
     *
     * @params string name of the javascript to initialize
     * @params string init-string to initialize the js
     */
    public static function addJSInit($name, $init)
    {
        $addJSInit = '';
        $addJSInit .= '<!-- initialize javascript: ' . $name . ' -->' . CR;
        $addJSInit .= '<script type="text/javascript">' . CR . '// <![CDATA[';
        $addJSInit .= $init;
        $addJSInit .= CR . '// ]]></script>' . CR;

        return $addJSInit;
    }

    /**
     * addCSS - Wrapper Method.
     *
     * @params string filename of the cascading style sheet to load
     * @params boolean display the iehack css in case true, default is false
     *
     * @param string $filename
     *
     * @return string style type css import
     */
    public static function addCSS($filename, $iehack = false)
    {
        $html = '<style type="text/css"> @import "' . WWW_ROOT_THEMES_CORE . 'css/' . $filename . '.css"; </style>';

        if ($iehack === true) {
            return '<!--[if IE]>' . CR . $html . CR . '<![endif]-->' . CR;
        } else {
            return $html . CR;
        }
    }
}
