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
use Koch\Exception\Exception;

class YellowScreenOfDeath
{
    /**
     * Renders a Koch Framework Exception.
     */
    public static function renderException($message, $string, $code, $file, $line, $trace)
    {
        ob_start();

        /**
         * @todo add backlink to the exception codes list
         */
        if ($code > 0) {
            $code = '(#' . $code . ')';
        } else {
            $code = '';
        }

        // Header
        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"';
        $html .= ' "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $html .= '<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">';
        $html .= '<head>';
        $html .= '<title>Koch Framework Exception ' . $code . ' - ' . $message . '</title>';
        $html .= '<link rel="stylesheet" href="' . WWW_ROOT_THEMES_CORE . 'css/error.css" type="text/css" />';
        $html .= '</head>';

        // Body
        $html .= '<body>';

        // Fieldset
        $html .= '<fieldset id="top" class="error_yellow">';

        // Errorlogo
        $html .= '<div style="float: left; margin: 5px; margin-right: 25px; padding: 20px;">';
        $html .= '<img src="' . WWW_ROOT_THEMES_CORE . 'images/Clansuite-Toolbar-Icon-64-exception.png" ';
        $html .= 'style="border: 2px groove #000000;" alt="Clansuite Exception Icon" /></div>';

        // Fieldset Legend
        $html .= '<legend>Koch Framework Exception</legend>';

        // Exception Table
        $html .= '<table width="80%"><tr><td>';

        /**
         * Panel 1
         *
         * Exception Message and File
         */

        $html .= '<div id="panel1" class="panel">';
        $html .= '<h3>Exception '.$code.'</h3><h4>'.$message.'</h4>';
        $html .= '<strong>' . Errorhandler::getFileLink($file, $line) . '.</strong>';
        $html .= '</div>';

        /**
         * Panel 2
         *
         * Debug Backtrace
         */
        if (defined('DEBUG') and DEBUG == 1) {
            // lets get the backtrace as html table
            $html .= Errorhandler::getDebugBacktrace($trace);
        }

        /**
         * Panel 3
         *
         * Server Environment Informations
         */
        if (defined('DEBUG') and DEBUG == 1) {
            $html .= '<div id="panel3" class="panel">';
            $html .= '<h3>Server Environment</h3>';
            $html .= '<table width="95%">';
            $html .= '<tr><td><strong>Date: </strong></td><td>' . date('r') . '</td></tr>';
            $html .= '<tr><td><strong>Remote: </strong></td><td>' . $_SERVER['REMOTE_ADDR'] . '</td></tr>';
            $html .= '<tr><td><strong>Request: </strong></td><td>index.php?' . $_SERVER['QUERY_STRING'] . '</td></tr>';
            $html .= '<tr><td><strong>PHP: </strong></td><td>' . PHP_VERSION . ' ' . PHP_EXTRA_VERSION . '</td></tr>';
            $html .= '<tr><td><strong>Server: </strong></td><td>' . $_SERVER['SERVER_SOFTWARE'] . '</td></tr>';
            $html .= '<tr><td><strong>Agent: </strong></td><td>' . $_SERVER['HTTP_USER_AGENT'] . '</td></tr>';
            $html .= '<tr><td><strong>Application: </strong></td>';
            $html .= '<td>' . APPLICATION_VERSION . ' ' . APPLICATION_VERSION_STATE;
            $html .= ' (' . APPLICATION_VERSION_NAME . ')</td></tr>';
            $html .= '</table></div>';
        }

        /**
         * Panel 4
         *
         * Additional Information
         */
        if (empty(self::$exception_template) === false) {
            $html .= '<div id="panel4" class="panel">';
            $html .= '<h3>Additional Information & Solution Suggestion</h3>';
            $html .= self::$exception_template . '</div>';
        }

        /**
         * Panel 5
         *
         * Rapid Development
         */
        $placeholders = array();
        // assign placeholders for replacements in the html
        if (strpos($message, 'action_')) {
            $placeholders['actionname'] = substr($message, strpos($message, 'action_'));
        } elseif (strpos($message, 'module_')) {
            $placeholders['classname'] = substr($message, strpos($message, 'module_'));
        }

        if (empty($_GET['mod']) == false) {
            $placeholders['modulename'] = (string) stripslashes($_GET['mod']);
        } else {
            $placeholders['modulename'] = '';
        }

        // add development helper template to exceptions
        if (defined('DEVELOPMENT') and DEVELOPMENT == 1 and defined('RAPIDDEVTPL') and RAPIDDEVTPL == 1) {
            $html .= '<div id="panel5" class="panel">';
            $html .= '<h3>Rapid Application Development</h3>';
            $html .= Exception::getExceptionDevelopmentTemplate($placeholders).'</div>';
        }

        /**
         * Panel 6
         *
         * Backlink to Bugtracker with Exception Message.
         */
        $html .= Errorhandler::getBugtrackerBacklinks($message, $file, $line, $trace);

        // close all html element table
        $html   .= '</table>';

        /**
         * Panel 7
         *
         * Footer with Support-Backlinks
         */
        $html  .= Errorhandler::getSupportBacklinks($this);

        // close all html elements: fieldset, body+page
        $html   .= '</fieldset>';
        $html   .= '</body></html>';

        // save session before exit - but only if this is not a pdo exception
        // that would trigger a fatal error, when trying to write to the db during session save
        if ((bool) session_id() and false === strpos($message, 'SQLSTATE')) {
            session_write_close();
        }

        // clear all output buffers
        if ( ob_get_length() ) {
            ob_end_clean();
        }

        // Output the errormessage
        return $html;
    }

    /**
     * Renders a Koch Framework Error.
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     * @param int    $errline
     * @param string $errcontext
     */
    public static function renderError($errno, $errorname, $errstr, $errfile, $errline, $errcontext)
    {
        // shorten errorfile string by removing the root path
        $errfile_short = str_replace(ROOT, '', $errfile);
        $short_errorstring = \Koch\Functions\Functions::shortenString($errfile, 70, '...');

        // Header
        $html = '<html><head>';
        $html .= '<title>Koch Framework Error</title>';
        $html .= '<link rel="stylesheet" href="' . WWW_ROOT_THEMES_CORE . 'css/error.css" type="text/css" />';
        $html .= '</head>';

        // Body
        $html .= '<body>';

        // Fieldset with Legend
        $html .= '<fieldset id="top" class="error_red">';
        $html .= '<legend>Koch Framework Error</legend>';

        // Add Errorlogo
        $html .= '<div style="float: left; margin: 5px; margin-right: 25px; padding: 20px;">';
        $html .= '<img src="' . WWW_ROOT_THEMES_CORE . 'images/Clansuite-Toolbar-Icon-64-error.png"';
        $html .= ' style="border: 2px groove #000000;"/></div>';

        // Open Error Table
        $html .= '<table width="80%"><tr><td>';

        // Panel 1 - Errormessage
        $html .= '<div id="panel1" class="panel">';
        $html .= '<h3>Error - '.$errorname.' (' . $errno . ')</h3> ';
        $html .= '<p style="font-weight: bold;">' . $errstr . '</p>';
        $html .= '<p>in file "<span style="font-weight: bold;">' . $errfile_short . '</span>"';
        $html .= ' on line #<span style="font-weight: bold;">' . $errline.'.</span></p>';
        $html .= '</div>';

        // Panel 2 - Error Context
        $html .= '<div id="panel2" class="panel">';
        $html .= '<h3>Context</h3>';
        $html .= '<p><span class="small">You are viewing the source code of the file "';
        $html .= $errfile . '" around line ' . $errline . '.</span></p>';
        $html .= Errorhandler::getErrorContext($errfile, $errline, 8) . '</div>';

        // Panel 3 - Debug Backtracing
        $html .= Errorhandler::getDebugBacktrace($short_errorstring);

        // Panel 4 - Environmental Informations at Errortime
        $html .= '<div id="panel4" class="panel">';
        $html .= '<h3>Server Environment</h3>';
        $html .= '<p><table width="95%">';
        $html .= '<tr><td colspan="2"></td></tr>';
        $html .= '<tr><td><strong>Date: </strong></td><td>' . date('r') . '</td></tr>';
        $html .= '<tr><td><strong>Remote: </strong></td><td>' . $_SERVER['REMOTE_ADDR'] . '</td></tr>';
        $html .= '<tr><td><strong>Request: </strong></td><td>' . htmlentities($_SERVER['QUERY_STRING'], ENT_QUOTES);
        $html .= '</td></tr>';
        $html .= '<tr><td><strong>PHP: </strong></td><td>' . PHP_VERSION . ' ' . PHP_EXTRA_VERSION . '</td></tr>';
        $html .= '<tr><td><strong>Server: </strong></td><td>' . $_SERVER['SERVER_SOFTWARE'] . '</td></tr>';
        $html .= '<tr><td><strong>Agent: </strong></td><td>' . $_SERVER['HTTP_USER_AGENT'] . '</td></tr>';
        $html .= '<tr><td><strong>Clansuite: </strong></td><td>';
        $html .= APPLICATION_VERSION . ' ' . APPLICATION_VERSION_STATE . ' (' . APPLICATION_VERSION_NAME . ')';
        $html .= '</td></tr>';
        $html .= '</table></p></div>';

        // Panel 5 - Backlink to Bugtracker with Errormessage
        $html .= Errorhandler::getBugtrackerBacklinks($errorname, $errfile, $errline, $errcontext);

        // Close Error Table
        $html .= '</table>';

        // Add Footer with Support-Backlinks
        $html .= Errorhandler::getSupportBacklinks();

        // Close all html elements
        $html .= '</fieldset><br /><br />';
        $html .= '</body></html>';

        return $html;
    }
}
