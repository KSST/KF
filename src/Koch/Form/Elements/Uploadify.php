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

class Uploadify extends File implements FormElementInterface
{
    /**
     * This renders an file upload form using jQuery Uploadify.
     */
    public function render()
    {
        // load the required scripts and styles
        $javascript = '<link rel="stylesheet" type="text/css"'
            . ' href="' . WWW_ROOT_THEMES_CORE . 'cssc/uploadifye/default.css" />'
            . '<link rel="stylesheet" type="text/css"'
            . '  href="' . WWW_ROOT_THEMES_CORE . 'css/uploadify/uploadify.css" />'
            . '<script type="text/javascript"'
            . ' src="' . WWW_ROOT_THEMES_CORE . 'javascript/uploadify/swfobject.js"></script>'
            . '<script type="text/javascript"'
            . ' src="' . WWW_ROOT_THEMES_CORE . 'javascript/jquery/jquery.uploadify.v2.1.4.min.js"></script>';

        // attach the uploadify handler and apply some configuration
        $javascript .= "<script type=\"text/javascript\">// <![CDATA[
                        $(document).ready(function () {
                            $('#uploadify').uploadify({
                            'uploader'  : '" . WWW_ROOT_THEMES_CORE . "/javascript/uploadify/uploadify.swf',
                            'script'    : '" . WWW_ROOT_THEMES_CORE . "/javascript/uploadify/uploadify.php',
                            'cancelImg' : '" . WWW_ROOT_THEMES_CORE . "/images/icons/cancel.png',
                            'auto'      : true,
                            'removecompleted' : true,
                            'folder'    : '/uploads'
                            });
                        });
                        // ]]></script>";

        // output the div elements
        $html = "<div id=\"fileQueue\"></div>
                 <input type=\"file\" name=\"uploadify\" id=\"uploadify\" />
                 <p><a href=\"javascript:jQuery('#uploadify').uploadifyClearQueue()\">Cancel All Uploads</a></p>";

        return $javascript . $html;
    }
}
