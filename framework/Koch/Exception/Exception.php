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

namespace Koch\Exception;

use Koch\Exception\Renderer\YellowScreenOfDeath;

/**
 * Exception
 *
 * Sets up a custom Exceptionhandler.
 * @see Clansuite_CMS::initialize_Errorhandling()
 *
 * Developer Notice:
 * The "Fatal error: Exception thrown without a stack frame in Unknown on line 0"
 * is of PHP dying when an exception is thrown when running INSIDE an error or exception handler.
 * Avoid stacking Exceptions, e.g. try/catch Exception($e) and then throwing a \Koch\Exception\Exception().
 *
 * @see http://php.net/manual/de/class.exception.php
 * @see http://php.net/manual/de/function.set-exception-handler.php
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Exceptionhandler
 */
class Exception extends \Exception
{
    /**
     * Variables of a PHP Exception
     * They are used to store the content of incomming uncatched Exceptions.
     */

    /**
     * @var string exception message
     */
    protected $message = 'Unknown exception';

    /**
     * @var string debug backtrace string
     */
    private $string;

    /**
     * @var int user-defined exception code
     */
    protected $code = 0;

    /**
     * @var string source filename of exception
     */
    protected $file;

    /**
     * @var int source line of exception
     */
    protected $line;

    /**
     * @var string trace
     */
    private $trace;

    /**
     * Variables for the content of exception templates
     */

    /**
     * @var string HTML Representation of the Exception Template
     */
    private static $exception_template = '';

    /**
     * @var string HTML Representation of the Exception Development (RAD) Template
     */
    private static $exception_dev_template = '';

    /**
     * Exception Handler Callback
     * Rethrows uncatched Exceptions in our presentation style.
     *
     * @see http://php.net/manual/de/function.set-exception-handler.php
     * @param $exception PHP Exception Objects are valid (Type Hint).
     */
    public function exceptionHandler(\Exception $exception)
    {
        // re/assign variables from an uncatched exception to this exception object
        $this->message = $exception->getMessage();
        $this->string = $exception->getTraceAsString();
        $this->code = $exception->getCode();
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
        $this->trace = $exception->getTrace();

        // if no errorcode is set, say that it's an rethrow
        if ($this->code === '0') {
            $this->code = '0 (This exception is uncatched and rethrown.)';
        }

        // fetch exceptionTemplates, but not for $code = 0
        if ($this->code > 0) {
            self::fetchExceptionTemplates($this->code);
        }

        /**
         * @todo
         * 1. catch Smarty "Template Syntax" Errors
         * 2. provide link to templateeditor (file:line) to fix the error
         */
        /*$smartyTemplateError = (false !== stristr($this->message, 'Syntax Error in template')) ? true : false;
        if ($smartyTemplateError === true) {
            throw new SmartyTemplateException($exception);
        }*/

        /**
         * @todo
         * 1. catch Smarty "Unable to load template file" Errors
         * 2. provide link to templatefilemanager (module:file)
         */

        include_once __DIR__ . '/Renderer/YellowScreenOfDeath.php';
        echo \Koch\Exception\Renderer\YellowScreenOfDeath::renderException(
            $this->message,
            $this->string,
            $this->code,
            $this->file,
            $this->line,
            $this->trace
        );
    }

    /**
     * Fetches the normal and rapid development templates for exceptions and sets them to class.
     * Callable via self::getExceptionTemplate() and self::getExceptionDevelopmentTemplate($placeholders).
     *
     * @param int $code Exception Code
     */
    private static function fetchExceptionTemplates($code)
    {
        // normal exception template
        self::fetchExceptionTemplate($code);

        // development template
        if (defined('DEVELOPMENT') and DEVELOPMENT == 1) {
            self::fetchExceptionDevelopmentTemplate($code);
        }
    }

    /**
     * Fetches a Helper Template for this exception by it's exception code.
     *
     * You find the Exception Template in the folder: /themes/core/exceptions/
     * The filename has to be "exception-ID.html", where ID is the exception id.
     *
     * @example
     * <code>
     * throw new \Koch\Exception\Exception('My Exception Message: ', 20);
     * </code>
     * The file "exception-20.html" will be retrieved.
     *
     * @param $code The exception code.
     */
    private static function fetchExceptionTemplate($code)
    {
        $file = APPLICATION_PATH . 'themes/core/exceptions/exception-' . $code . '.html';

        if (is_file($file) === true) {
            self::$exception_template = file_get_contents($file);
        }
    }

    /**
     * Fetches a Helper Template for this exception by it's exception code.
     * This is for rapid development purposes.
     *
     * You find the Exception Development Template in the folder: /themes/core/exceptions/
     * The filename has to be "exception-dev-ID.html", where ID is the exception id.
     *
     * @example
     * <code>
     * throw new \Koch\Exception\Exception('My Exception Message: ', 20);
     * </code>
     * The file "exception-dev-20.html" will be retrieved.
     *
     * @param $code The exception code.
     */
    private static function fetchExceptionDevelopmentTemplate($code)
    {
        // construct filename with code
        $file = APPLICATION_PATH . 'themes/core/exceptions/exception-dev-' . $code . '.html';

        if (is_file($file) === true) {
            self::$exception_dev_template = file_get_contents($file);

            define('RAPIDDEVTPL', true);
        }
        /*
        else {
           // @todo propose to create a new rapid development template via tpleditor
        }
        */
    }

    /**
     * Getter Method for the exception_development_template_content
     *
     * @return HTML Representation of $exception_development_template_content
     */
    public static function getExceptionDevelopmentTemplate($placeholders)
    {
        $original_file_content = self::$exception_dev_template;
        $replaced_content = '';

        if ($placeholders['modulename'] !== null) {
            $replaced_content = str_replace('{$modulename}', $placeholders['modulename'], $original_file_content);
        }

        if ($placeholders['classname'] !== null) {
            $replaced_content = str_replace('{$classname}', $placeholders['classname'], $replaced_content);
        }

        if ($placeholders['actionname'] !== null) {
            $replaced_content = str_replace('{$actionname}', $placeholders['actionname'], $replaced_content);
        }

        return $replaced_content;
    }

    /**
     * Formats the debugtrace ($this->string) by applying linebreaks.
     *
     * @param $string The debug-trace string to format.
     * @return HTML with Debugtrace String
     */
    public static function formatGetTraceString($string)
    {
        $search  = array('#', '):');
        $replace = array('<br/><br/>Call #',')<br/>');
        $string  = str_replace($search, $replace, $string);
        $string  = ltrim($string, '<br/>');
        unset($search, $replace);

        return $string;
    }

    /**
     * Overwriteable Method of Class Exception
     * This is the String representation of the exception.
     * It is a pass-through to our presentation format (ysod);
     */
    public function __toString()
    {
        $ysod = new YellowScreenOfDeath;

        return $ysod->renderException();
    }
}
