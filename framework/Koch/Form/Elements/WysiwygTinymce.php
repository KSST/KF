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
 * Formelement_Wysiwygtinymce
 *
 * @link http://tinymce.moxiecode.com/ Official Website
 * @link http://tinymce.moxiecode.com/js/tinymce/docs/api/index.html API Documentation
 * @link http://tinymce.moxiecode.com/examples/ Examples
 */
class WysiwygTinymce extends Textarea implements FormElementInterface
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
        if (!is_file(APPLICATION_PATH . 'themes/core/javascript/tiny_mce/tiny_mce.js')) {
            exit('TinyMCE Library missing!');
        }
    }

    /**
     * This renders a textarea with the WYSWIWYG editor TinyMCE attached.
     */
    public function render()
    {
        // a) loads the tinymce javascript file
        $javascript = '<script src="'.WWW_ROOT_THEMES_CORE . 'javascript/tiny_mce/tiny_mce.js"';
        $javascript .= ' type="text/javascript"></script>';

        // b) handler to attach tinymce to a textarea named "mceSimple" and "mceAdvanced"
        $javascript .= '<script type="text/javascript">// <![CDATA[
                            tinyMCE.init({
                                mode : "textareas",
                                theme : "simple",
                                editor_selector : "mceSimple"
                            });

                            tinyMCE.init({
                                mode : "textareas",
                                theme : "advanced",
                                editor_selector : "mceAdvanced"
                            });
                        // ]]></script>';
        return $javascript;
    }
}
