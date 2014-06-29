<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
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

namespace Koch\Exception;

use Koch\Exception\Renderer\YellowScreenOfDeath;
use Koch\Exception\Renderer\SmartyTemplateError;

/**
 * Koch Framework - Class for Errorhandling
 *
 * Sets up a custom Errorhandler.
 * @example
 * <code>
 * 1) trigger_error('Errormessage', E_ERROR_TYPE);
 *    E_ERROR_TYPE as string or int
 * 2) trigger_error('Errorhandler Test - This should trigger a E_USER_NOTICE!', E_USER_NOTICE);
 * </code>
 */
class Errorhandler
{
    /**
     * Registers this class as the new PHP errorhandler, thereby overwriting any previous handlers.
     */
    public static function register()
    {
        set_error_handler(array('Errorhandler', 'handleError'));
        //set_error_handler(array('Errorhandler', 'handleErrorAsErrorException'));
        //set_exception_handler(array('Errorhandler', 'handleException'));
        //register_shutdown_function(array('Errorhandler, 'catchFatalErrors'));
    }

    /**
     * Handle Error as ErrorException
     *
     * @param  integer         $errnum   contains the error as integer.
     * @param  string          $message  contains the error string.
     * @param  string          $filename contains the filename with occuring error.
     * @param  integer         $lineno   contains the line of error.
     * @throws \ErrorException
     */
    public function handleErrorAsErrorException($errnum, $message, $filename, $lineno)
    {
        throw new \ErrorException($message, 0, $severity, $filename, $lineno);
    }

    /**
     * Koch Framework - Error callback.
     *
     * This is basically a switch statement, defining the actions taken,
     * in case of serveral PHP error states.
     *
     * @link http://www.usegroup.de/software/phptutorial/debugging.html
     * @link http://www.php.net/manual/de/function.set-error-handler.php
     * @link http://www.php.net/manual/de/errorfunc.constants.php
     *
     * @param integer $errnum     contains the error as integer.
     * @param string  $errstr     contains the error string.
     * @param string  $errfile    contains the filename with occuring error.
     * @param string  $errline    contains the line of error.
     * @param string  $errcontext (optional) array with variables from error context.
     */
    public static function handleError($errnum, $errstr, $errfile, $errline, $errcontext = null)
    {
        // return, if the error is suppressed, due to (@)silencing-operator
        if (error_reporting() === 0) {
            return;
        }

        /**
         * Assemble the error informations
         */

        /**
         * Definition of PHP error types, with names for all the php error codes.
         * @link http://php.net/manual/de/errorfunc.constants.php
         */
        $errorTypes = array (
            1     => 'E_ERROR',              // fatal run-time errors, like php is failing memory allocation
            2     => 'E_WARNING',            // Run-time warnings (non-fatal errors)
            4     => 'E_PARSE',              // compile-time parse errors - generated by the parser
            8     => 'E_NOTICE',             // Run-time notices (could be an indicator for an error)
            16    => 'E_CORE_ERROR',         // PHP Core reports errors in PHP's initial startup
            32    => 'E_CORE_WARNING',       // PHP Core reports warning (non-fatal errors)
            64    => 'E_COMPILE_ERROR',      // Zend Script Engine reports fatal compile-time errors
            128   => 'E_COMPILE_WARNING',    // Zend Script Engine reports compile-time warnings (non-fatal errors)
            256   => 'E_USER_ERROR',         // trigger_error(), user_error() reports user-defined error
            512   => 'E_USER_WARNING',       // trigger_error(), user_error() reports user-defined warning
            1024  => 'E_USER_NOTICE',        // trigger_error(), user_error() reports user-defined notice
            2048  => 'E_STRICT',             // PHP suggests codechanges to ensure interoperability / forwad compat
            4096  => 'E_RECOVERABLE_ERROR',  // catchable fatal error, if not catched it's an e_error (since PHP 5.2.0)
            8191  => 'E_ALL 8191',           // PHP 6 -> 8191
            8192  => 'E_DEPRECATED',         // notice marker for 'in future' deprecated php-functions (since PHP 5.3.0)
            16384 => 'E_USER_DEPRECATED',    // trigger_error(), user_error() reports user-defined deprecated functions
            30719 => 'E_ALL 30719 PHP5.3.x', // all errors and warnings - E_ALL of PHP Version 5.3.x
            32767 => 'E_ALL 32767 PHP6'      // all errors and warnings - E_ALL of PHP Version 6
        );

        // get the errorname from the array via $errornumber
        $errorname = isset($errorTypes[$errnum]) ? $errorTypes[$errnum] : '';

        // Handling the ErrorType via Switch
        switch ($errorname) {
            // This one is handled by register_shutdown_function + catchFatalErrorsShutdownHandler
            case 'E_ERROR':
                $errorname .= ' [PHP Fatal Error]';
                break;
            // What are the errortypes that can be handled by a user-defined errorhandler?
            case 'E_WARNING':
                $errorname .= ' [PHP Warning]';
                break;
            case 'E_NOTICE':
                $errorname .= ' [PHP Notice]';
                break;
            case 'E_USER_ERROR':
                $errorname .= ' [Koch Framework Internal Error]';
                break;
            case 'E_USER_WARNING':
                $errorname .= ' [Koch Framework Internal Error]';
                break;
            case 'E_USER_NOTICE':
                $errorname .= ' [Koch Framework Internal Error]';
                break;
            case 'E_ALL':
            case 'E_STRICT':
                $errorname .= ' [PHP Strict]';
                break;
            case 'E_RECOVERABLE_ERROR':
                $errorname .= ' [php not-unstable]';
                break;
            // when it's not in there, its an unknown errorcode
            default:
                $errorname .= ' Unknown Errorcode ['. $errnum .']: ';
        }

        // make the errorstring more useful by linking it to the php manual
        $pattern = "/<a href='(.*)'>(.*)<\/a>/";
        $replacement = '<a href="http://php.net/$1" target="_blank">?</a>';
        $errstr = preg_replace($pattern, $replacement, $errstr);

        // if DEBUG is set, display the error
        if (defined('DEBUG') and DEBUG == 1) {

            /**
             * SMARTY ERRORS are thrown by trigger_error() - so they bubble up as E_USER_ERROR.
             *
             * In order to handle smarty errors with an seperated error display,
             * we need to detect, if an E_USER_ERROR is either incoming from
             * SMARTY or from a template_c file (extension tpl.php).
             */
            if ((true === (bool) mb_strpos(mb_strtolower($errfile), 'smarty')) or
                (true === (bool) mb_strpos(mb_strtolower($errfile), 'tpl.php'))) {
                // render the smarty template error
                echo SmartyTemplateError::render($errnum, $errorname, $errstr, $errfile, $errline, $errcontext);
            } else {
                // render normal error display, with all pieces of information, except backtraces
                echo YellowScreenOfDeath::renderError($errnum, $errorname, $errstr, $errfile, $errline, $errcontext);
            }
        }

        // Skip PHP internal error handler
        return true;
    }

    /**
     * getTemplateEditorLink
     *
     * a) determines the path to the invalid template file
     * b) provides the html-link to the templateeditor for this file
     *
     * @param string      $errfile    Template File with the Error.
     * @param string      $errline    Line Number of the Error.
     * @param string|null $errcontext
     * @todo correct link to the templateeditor
     */
    public static function getTemplateEditorLink($errfile, $errline, $errcontext)
    {
        // display the link to the templateeditor, if we are in DEVELOPMENT MODE
        // and more essential if the error relates to a template file
        if (defined('DEVELOPMENT') and DEVELOPMENT === 1 and (mb_strpos(mb_strtolower($errfile), '.tpl') === true)) {
            // ok, it's a template, so we have a template context to determine the templatename
            $tpl_vars = $errcontext['this']->getTemplateVars();

            // maybe the templatename is defined in tpl_vars
            if ($tpl_vars['templatename'] !== null) {
                $errfile = $tpl_vars['templatename'];
            } else { // else use resource_name from the errorcontext
                $errfile = $errcontext['resource_name'];
            }

            // construct the link to the tpl-editor
            $html = '<br/><a href="index.php?mod=templatemanager&amp;sub=admin&amp;action=editor';
            $html .= '&amp;file=' . $errfile . '&amp;line=' . $errline;
            $html .= '">Edit the Template</a>';

            // return the link
            return $html;
        }
    }

    /**
     * getDebugBacktrace
     *
     * Transforms the output of php's debug_backtrace() and exception backtraces
     * to a more readable html format.
     *
     * @return string $backtrace_string contains the backtrace
     */
    public static function getDebugBacktrace($trace = null)
    {
        // show backtraces only in DEBUG mode
        if (defined('DEBUG') === false xor DEBUG == 0) {
            return;
        }

        // Normally backtraces are incomming from exceptions.
        // But, when called from the errorhandler, we need to fetch the traces ourselfs.
        if ($trace === null) {

            if (function_exists('xdebug_get_function_stack') === true) {
                $trace = xdebug_get_function_stack();
            } else {
                $trace = debug_backtrace();
            }

            /**
             * Now we get rid of several last calls in the backtrace stack,
             * to get nearer to the relevant error position in the stack.
             *
             * What exactly happens is: we shift-off the last 3 calls to
             * 1) getDebugBacktrace()   [this method itself]
             * 2) yellowScreenOfDeath() [our exception and error display method]
             * 3) trigger_error()       [php core function call]
             */
            $trace = array_slice($trace, 3);
        }

        /**
         * Assemble the html for the backtrace panel
         */
        $html = '';
        $html .= '<div id="panel3" class="panel"><h3>Backtrace</h3>';
        $html .= '<table class="cs-backtrace-table" width="95%">';

        // table row 1 - header
        $html .= '<tr><th width="2%">Callstack</th><th>Function</th><th width="46%">Location</th></tr>';

        // the number of backtraces
        $backtraces_counter_i = count($trace) - 1;

        for ($i = 0; $i <= $backtraces_counter_i; $i++) {
            $html .= '<tr>';

            // Position in the Callstack
            $html .= '<td align="center">' . (($backtraces_counter_i - $i) + 1) . '</td>';

            if (isset($trace[$i]['class']) === false) {
                $html .= '<td>[A PHP Core Function Call]</td>';
            } else {

                // replace trace type string with it's operator
                if ($trace[$i]['type'] === 'dynamic') {
                    $trace[$i]['type'] = '->';
                } else {
                    $trace[$i]['type'] = '::';
                }

                $html .= '<td>';

                // show the function call, e.g. Class->Method() or Class::Method()
                $html .= $trace[$i]['class'] . $trace[$i]['type'] . $trace[$i]['function'] . '()';

                // if the class is one of our own, add backlink to API Documentation
                if (1 === preg_match('/^Koch/', $trace[$i]['class'])) {
                    $html .= '<span class="error-class">';
                    $html .= '<a target="_new" href="http://docs.kf.com/en/latest/api/';
                    $html .= str_replace('\\', '_', $trace[$i]['class']);
                    $html .= '.html"> ' . $trace[$i]['class'] . '</a></span>';
                } else {
                    // else it's a php internal class, then add a backlink to the php manual
                    $classReflection = new \ReflectionClass($trace[$i]['class']);
                    if ($classReflection->isInternal()) {
                        $html .= '<span class="error-class"><a target="_new" href="http://php.net/manual/en/class.';
                        $html .= str_replace('_', '-', strtolower($trace[$i]['class']));
                        $html .= '.php">' . $trace[$i]['class'] . '</a></span>';
                    } else {
                        $html .= '<span class="error-class">'. $trace[$i]['class'] . '</span>';
                    }
                }

                // is this a normal php function? if yes, add a backlink to the php manual
                if (function_exists($trace[$i]['function']) === true) {
                    $functionReflection = new \ReflectionFunction($trace[$i]['function']);
                    if ($functionReflection->isInternal()) {
                        $html .= '<span class="error-function">';
                        $html .= '<a target="_new" href="http://php.net/manual/en/function.';
                        $html .= str_replace('_', '-', $trace[$i]['function']);
                        $html .= '.php">' . $trace[$i]['function'] . '</a></span>';
                    }
                }

                // XDebug uses the array key 'params' for the method parameters array
                // PHP backtrace uses 'args', so let's rename to 'args'
                if (isset($trace[$i]['params']) === true) {
                    $trace[$i]['args'] = $trace[$i]['params'];
                    unset($trace[$i]['params']);
                }

                // Method Arguments
                if (isset($trace[$i]['args']) === true and empty($trace[$i]['args']) === false) {
                    // the number of arguments (method parameters)
                    $backtrace_counter_j = count($trace[$i]['args']) - 1;

                    // use reflection to get the method parameters (and their names for display)
                    $reflected_method = new \ReflectionMethod($trace[$i]['class'], $trace[$i]['function']);
                    /* @var $reflected_params \ReflectionParameter */
                    $reflected_params = $reflected_method->getParameters();

                    // render a table with method parameters
                    // argument position | name | type | value
                    $html .= '<table style="border-collapse: collapse;">';
                    $html .= '<tr><th style="line-height: 0.8em;" colspan="4">Parameters</th></tr>';
                    $html .= '<tr style="line-height: 0.8em;">';
                    $html .= '<th>Pos</th><th>Name = Default Value</th><th>Type</th><th>Value</th></tr>';

                    // loop over all arguments
                    for ($j = 0; $j <= $backtrace_counter_j; $j++) {
                        // fetch data for this argument
                        $data = self::formatBacktraceArgument($trace[$i]['args'][$j]);
                        // fetch current reflection parameter object
                        $parameter = $reflected_params[$j];
                        // get just the parameter name and it's default value
                        preg_match('/\[ ([^[]+) \]/', $parameter, $matches);

                        $html .= '<tr>';
                        $html .= '<td>' . ($j + 1) . '</td>'; // pos
                        $html .= '<td>' . $matches['1'] . '</td>'; // name
                        $html .= '<td>' . $data['type'] . '</td>'; // type
                        $html .= '<td>' . $data['arg'] . '</td>'; // value $defaultValue
                    }
                    $html .= '</tr></table>';
                }
                $html .= '</td>';
            }

            // Location with Link
            if (isset($trace[$i]['file']) === true) {
                $html .= '<td>' . self::getFileLink($trace[$i]['file'], $trace[$i]['line']) . '</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</table></div>';

        return $html;
    }

    /**
     * formatBacktraceArgument
     *
     * Performs a type check on the backtrace argument and beautifies it.
     *
     * This formater is based on comments for debug-backtrace in the php manual
     * @link http://de2.php.net/manual/en/function.debug-backtrace.php#30296
     * @link http://de2.php.net/manual/en/function.debug-backtrace.php#47644
     *
     * @param backtraceArgument mixed The argument for type identification and string formatting.
     *
     * @return array With keys 'arg' and 'type'.
     */
    public static function formatBacktraceArgument($argument)
    {
        // do not throw a notice on PHP 5.3 - the constant was added with PHP 5.4 htmlspecialchars()
        defined('ENT_SUBSTITUTE') || define('ENT_SUBSTITUTE', 8);

        $result = array();
        $arg = '';
        $type = '';

        switch (gettype($argument)) {
            case 'boolean':
                $type .= '<span>bool</span>';
                $arg .= $argument ? 'true' : 'false';
                break;
            case 'integer':
                $type .= '<span>int</span>';
                $arg .= $argument;
                break;
            case 'float':
            case 'double':
                $type .= '<span>float/double</span>';
                $arg .= $argument;
                break;
            case 'string':
                $type .= '<span>string</span>';
                $argument = htmlspecialchars($argument, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $arg .= \Koch\Functions\Functions::shortenString($argument);
                break;
            case 'array':
                $type .= '<span>array</span>';
                $arg .= count($argument);
                break;
            case 'object':
                $type .= '<span>object</span>';
                $arg .= get_class($argument);
                /* @todo use self::getClassProperties($backtraceArgument) */
                break;
            case 'resource':
                $type .= '<span>resource</span>';
                if ($type === 'stream') {
                    $type .= '(stream)';
                    $meta = stream_get_meta_data($argument);
                    if (isset($meta['uri'])) {
                        $type .= htmlspecialchars($meta['uri'], ENT_NOQUOTES, 'UTF-8');
                    }
                }
                $arg .= mb_strstr($argument, '#') . ' - ' . get_resource_type($argument);
                break;
            case 'NULL':
                $type .= '<span>null</span>';
                $arg .= '';
                break;
            default:
                $type .= 'Unknown';
                $arg .= 'Unknown';
        }

        return compact('arg', 'type');
    }

    public static function getClassProperties($class, $nestingLevel = 2)
    {
        $html = '';
        $html .= '<ul>';
        $ref = new ReflectionClass($class);
        foreach ($ref->getProperties() as $p) {
            $html .= '<li><span>';
            // static ?
            $html .= ($p->isStatic()) ? '<em>static</em> ' : '';
            // scope ?
            if ($p->isPrivate()) {
                $html .= 'private';
                $p->setAccessible(true);
            } elseif ($p->isProtected()) {
                $html .= 'protected';
                $p->setAccessible(true);
            } else {
                $html .= 'public';
            }
            $html .= ' </span><span>$' . $p->getName() . ' </span>';
            $html .= '<span>' . self::formatBacktraceArgument($p->getValue($class), $nestingLevel - 1) . '</span>';
            $html .= '</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * getErrorContext displayes some additional lines of sourcecode around the line with error.
     *
     * @param string $file  file with the error in it
     * @param int    $scope the context scope (defining how many lines surrounding the error are displayed)
     * @param int    $line  the line with the error in it
     *
     * @return string sourcecode of file
     */
    public static function getErrorContext($file, $line, $scope)
    {
        // ensure that sourcefile is readable
        if (true === is_readable($file)) {
            // Scope Calculations
            $surround_lines          = round($scope/2);
            $errorcontext_starting_line = $line - $surround_lines;
            $errorcontext_ending_line   = $line + $surround_lines;

            // create linenumbers array
            $lines_array = range($errorcontext_starting_line, $errorcontext_ending_line);

            // colourize the errorous linenumber red
            $lines_array[$surround_lines] = '<span class="error-line">'.$lines_array[$surround_lines].'</span>';
            $lines_array[$surround_lines] .= '<span class="error-triangle">&#9654;</span>';

            // transform linenumbers array to string for later display, use spaces as separator
            $lines_html = implode($lines_array, ' ');

            // get ALL LINES syntax highlighted source-code of the file and explode it into an array
            // the if check is needed to workaround "highlight_file() has been disabled for security reasons"
            if (function_exists('highlight_file') === true) {
                $array_content = explode('<br />', highlight_file($file, true));
            } else {
                $array_content = explode('<br />', $file);
            }

            // get the ERROR SURROUNDING LINES from ALL LINES
            $array_content_sliced = array_slice($array_content, $errorcontext_starting_line-1, $scope, true);

            $result = array_values($array_content_sliced);

            // @todo now colourize the background of the errorous line RED
            //$result[$surround_lines] = '<span style="background-color:#BF0000;">'
            // . $result[$surround_lines] .'</span>';

            // @todo remove 4 space identation, still buggy on inline stmts
            //foreach ($result as $i => $line) {
            //     $result[$i] = str_replace('&nbsp;&nbsp;&nbsp;&nbsp;', '', $line);
            //}

            // transform the array into html string
            // enhance readablility by imploding the array with spaces (try either ' ' or  '<br>')
            $errorcontext_lines  = implode($result, '<br>');

            $sprintf_html = '<table>
                                <tr>
                                    <td class="num">'.CR.'%s'.CR.'</td>
                                    <td><pre>'.CR.'%s'.CR.'</pre></td>
                                </tr>
                            </table>';

            // @todo consider using wordwrap() to limit too long source code lines?
            return sprintf($sprintf_html, $lines_html, $errorcontext_lines);
        }
    }

    /**
     * Returns the Clansuite Support Backlinks as HTML string.
     *
     * @return string Clansuite Support Backlinks as HTML.
     */
    public static function getSupportBacklinks()
    {
        $html  = '<div id="support-backlinks" style="padding-top: 45px; float:right;">';
        $html  .= '<!-- Live Support JavaScript -->
                   <a class="btn" href="http://support.clansuite.com/chat.php"';
        $html  .= ' target="_blank">Contact Support (Start Chat)</a>
                   <!-- Live Support JavaScript -->';
        $html  .= '<a class="btn" href="http://trac.clansuite.com/newticket/">Bug-Report</a>
                   <a class="btn" href="http://forum.clansuite.com/">Support-Forum</a>
                   <a class="btn" href="http://docs.clansuite.com/">Manuals</a>
                   <a class="btn" href="http://clansuite.com/">visit clansuite.com</a>
                   <a class="btn" href="#top"> &#9650; Top </a>
                   </div>';

        return $html;
    }

    /**
     * Returns a link to the file:line with the error.
     *
     * a) returns a link in the xdebug file_link_format.
     *    this will opens your IDE at file/line.
     * b) returns a link in clansuite format.
     *    this will open the module editor at file/line.
     * c) returns NO link, just file:line.
     *
     * @return string Link to file and line with error.
     */
    public static function getFileLink($file, $line)
    {
        $fileLinkFormatString = '';

        /***
         * a) "xdebug.file_link_format"
         *
         * This uses the file "xdebug.file_link_format" php.ini configuration directive,
         * which defines a link template (sprintf) for calling your Editor/IDE.
         */
        $fileLinkFormatString = ini_get('xdebug.file_link_format');

        if (isset($fileLinkFormatString)) {

            // insert file and line into the fileLinkFormatString
            $link = strtr($fileLinkFormatString, array('%f' => $file, '%l' => $line));

            // shorten file string by removing the root path
            $file = str_replace(APPLICATION_PATH, '..' . DIRECTORY_SEPARATOR, $file);

            // build an edit link
            return sprintf(' in <a href="%s" title="Edit file">%s line #%s</a>', $link, $file, $line);
        } else {
            /*
             * elseif (DEVELOPMENT) {
              // link to our editor
              $fileLinkFormatString = 'index.php?module=editor&action=edit&file=%f&line=%l';

              // insert file:line into the fileLinkFormatString
              $link = strtr($fileLinkFormatString, array('%f' => $file, '%l' => $line));

              return sprintf(' in <a href="%s" title="Edit file">%s line #%s</a>', $link, $file, $line);
              } else { */
            // shorten file string by removing the root path
            $file = str_replace(APPLICATION_PATH, '..' . DIRECTORY_SEPARATOR, $file);

            return sprintf(' in %s line #%s', $file, $line);
        }
    }

    /**
     * Adds a link to our bugtracker, for creating a new ticket with the errormessage
     *
     * @param  string $errorstring the errormessage
     * @return string html-representation of the bugtracker links
     */
    public static function getBugtrackerBacklinks($errorstring, $errorfile, $errorline, $errorcontext)
    {
        $msg1 = '<div id="panel5" class="panel"><h3>' . 'Found a bug in Clansuite?' . '</h3><p>';
        $msg2 = 'If you think this should work and you can reproduce the problem,';
        $msg2 .= ' please consider creating a bug report.';
        $msg3 = 'Before creating a new bug report, please first try searching for similar issues,';
        $msg3 .= ' as it is quite likely that this problem has been reported before.';
        $msg4 = 'Otherwise, please create a new bug report describing the problem and explain how to reproduce it.';

        $search_link = NL . NL . '<a class="btn" target="_blank" href="http://trac.clansuite.com/search?q=';
        $search_link .= htmlentities($errorstring, ENT_QUOTES) . '&noquickjump=1&ticket=on">';
        $search_link .= '&#9658; Search for similar issue';
        $search_link .= '</a>';

        $newticket_link = '&nbsp;<a class="btn" target="_blank" href="';
        $newticket_link .= self::getTracNewTicketURL($errorstring, $errorfile, $errorline).'">';
        $newticket_link .= '&#9658; Create new ticket';
        $newticket_link .= '</a>';

        return $msg1 . $msg2 . NL . $msg3 . NL . $msg4 . $search_link . $newticket_link . '</p></div>' . NL;
    }

    /**
     * Returns a link to Trac's New Ticket Dialog prefilled with error data.
     *
     * @returns string Link to Trac's Create New Ticket Dialog prefilled.
     * @param string $summary
     */
    public static function getTracNewTicketURL($summary, $errorfile, $errorline)
    {
        /**
         * This is the error description.
         * It's written in trac wiki formating style.
         *
         * @link http://trac.clansuite.com/wiki/WikiFormatting
         */
        $description = '[Error] ' . $summary . ' [[BR]] [File] ' . $errorfile . ' [[BR]] [Line] ' . $errorline;

        // options array for http_build_query
        $array = array(
            'summary'     => $summary,
            'description' => $description,
            'type'        => 'defect-bug',
            'milestone'   => 'Triage-Neuzuteilung',
            'version'     => APPLICATION_VERSION,
            #'component'   => '',
            'author'      => isset($_SESSION['user']['email']) ? $_SESSION['user']['email'] : '',
        );

        return 'http://trac.clansuite.com/newticket/?' . http_build_query($array);
    }

    /**
     * Returns a link to a new Issue on Github
     * @link http://developer.github.com/v3/issues/#create-an-issue
     */
    public static function getGithubIssueURL($summary, $errorfile, $errorline)
    {
        // POST /repos/:owner/:repo/issues

        /*{
            "title": "Found a bug",
            "body": "I'm having a problem with this.",
            "assignee": "octocat",
            "milestone": 1,
            "labels": [
              "Label1",
              "Label2"
            ]
          }
         */
    }

    /**
     * This method might be registered to the shutdown handler to catch fatal errors.
     */
    public static function catchFatalErrors()
    {
        $lastError = error_get_last();

        // return early, if there hasn't been an error yet
        if (null === $lastError) {
            return;
        }

        $fatals = array(
            E_USER_ERROR => 'Fatal Error',
            E_ERROR => 'Fatal Error',
            E_PARSE => 'Parse Error',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning'
        );

        if (isset($fatals[$lastError['type']]) === true) {
            self::handle($lastError['type'], $lastError['message'], $lastError['file'], $lastError['line']);
        }
    }
}
