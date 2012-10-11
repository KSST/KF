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
 * Formelement_Wysiwygnicedit
 *
 * @see Http://www.nicedit.com/ Official Website of NicEdit
 * @see http://wiki.nicedit.com/ Wiki of NicEdit
 */
class WysiwygNicedit extends Textarea implements FormElementInterface
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
        if (!is_file(APPLICATION_PATH . 'themes/core/javascript/nicedit/nicedit.js')) {
            exit('NicEdit Javascript Library missing!');
        }
    }

    /**
     * This renders a textarea with the WYSWIWYG editor NicEdit attached.
     */
    public function render()
    {
        // a) loads the nicedit javascript file
        $javascript = '<script src="'.WWW_ROOT_THEMES_CORE . 'javascript/nicedit/nicedit.js'. '"';
        $javascript .= ' type="text/javascript"></script>';

        // b) handler to attach nicedit to all textareas
        $javascript .= "<script type=\"text/javascript\">// <![CDATA[
                        var wysiwyg;
                            bkLib.onDomLoaded(function() {
                              wysiwyg = new nicEditor({
                                    fullPanel : true,
                                    iconsPath : '" . WWW_ROOT_THEMES_CORE . "/javascript/nicedit/nicEditorIcons.gif',
                                    maxHeight : 320,
                                    bbCode    : true,
                                    xhtml     : true
                                  }).panelInstance('".$this->name."');
                            });
                            // ]]></script>";

        // wysiwyg.instanceById('page_body').saveContent();

        /**
         * c) css style
         *
         * Developer Notice
         *
         * nicEdit has the following CSS classes:
         *
         * .nicEdit-panelContain
         * .nicEdit-panel
         * .nicEdit-main
         * .nicEdit-button
         * .nicEdit-select
         */
        $html = '<style type="text/css">'.CR.'
                 .nicEdit-main {
                    background-color: #eee !important;
                    font-size: 16px;
                    padding: 3px;
                    }'.CR.'
                </style>';

        // if we are in inheritance mode, skip this, the parent class handles this already
        return $javascript.$html;
    }
}
