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
 * Formelement_Wysiwygmarkitup
 *
 * @see http://markitup.jaysalvat.com/home/ Official Website of markItUp!
 */
class WysiwygMarkitup extends Textarea implements FormElementInterface
{
    public function __construct()
    {
        self::checkDependencies();
    }

    /**
     * Ensure, that the library is available, before the client requests a non-existant file.
     */
    public static function checkDependencies()
    {
        if (!is_file(ROOT_THEMES_CORE . 'javascript/markitup/jquery.markitup.js')) {
            exit('MarkitUp Javascript Library missing!');
        }
    }

    /**
     * This renders a textarea with the WYSWIWYG editor markItUp! attached.
     */
    public function render()
    {
        // a) loads the markitup javascript files
        #$javascript = '<script type="text/javascript" src="'.WWW_ROOT_THEMES_CORE . 'javascript/jquery/jquery.js"></script>';
        $javascript = '<script type="text/javascript" src="'.WWW_ROOT_THEMES_CORE . 'javascript/markitup/jquery.markitup.js"></script>'.CR;

        // b) load JSON default settings
        $javascript .= '<script type="text/javascript" src="'.WWW_ROOT_THEMES_CORE . 'javascript/markitup/sets/default/set.js"></script>'.CR;

        // c) include CSS
        $css = '<link rel="stylesheet" type="text/css" href="'.WWW_ROOT_THEMES_CORE . 'javascript/markitup/skins/markitup/style.css" />'.CR.'
                 <link rel="stylesheet" type="text/css" href="'.WWW_ROOT_THEMES_CORE . 'javascript/markitup/sets/default/style.css" />'.CR;

        // d) plug it to an specific textarea by ID
        $javascript .= '<script type="text/javascript">// <![CDATA[
                           jQuery(document).ready(function($){
                              $("textarea:visible").markItUp(mySettings);
                           });
                        // ]]></script>';
        return $javascript.$css;
    }
}
