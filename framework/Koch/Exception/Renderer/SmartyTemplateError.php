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

namespace Koch\Exception\Renderer;

use Koch\Exception\Errorhandler;

class SmartyTemplateError
{
    /**
     * Smarty Error Display
     *
     * This method defines the html-output when an Smarty Template Error occurs.
     * It's output is a shortened version of the normal error report, presenting
     * only the errorname, filename and the line of the error.
     * The parameters used for the small report are $errorname, $errorfile, $errorline.
     * If you need a full errorreport, you can add more parameters from the methodsignature
     * to the $errormessage output.
     *
     * Smarty Template Errors are only displayed, when Koch Framework is in DEBUG Mode.
     * @see clansuite_error_handler()
     *
     * A direct link to the template editor for editing the file with the error
     * is only displayed, when Koch Framework runs in DEVELOPMENT Mode.
     * @see addTemplateEditorLink()
     *
     * @param  int $errno      contains the error as integer
     * @param  string  $errstr     contains error string info
     * @param  string  $errfile    contains the filename with occuring error
     * @param  string  $errline    contains the line of error
     * @param  array   $errcontext contains vars from error context
     * @return string  HTML with Smarty Error Text and Link.
     */
    public static function render($errno, $errorname, $errstr, $errfile, $errline, $errcontext)
    {
        $html = '';
        $html .= '<span>';
        $html .= '<h4><font color="#ff0000">&raquo; Smarty Template Error &laquo;</font></h4>';
        #$html .= '<u>' . $errorname . ' (' . $errno . '): </u><br/>';
        $html .= '<b>' . wordwrap($errstr, 50, "\n") . '</b><br/>';
        $html .= 'File: ' . $errfile . '<br/>Line: ' . $errline;
        $html .= Errorhandler::getTemplateEditorLink($errfile, $errline, $errcontext);
        $html .= '<br/></span>';

        return $html;
    }
}
